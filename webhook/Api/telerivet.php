<?php
class Telerivet_API
{
    private $api_key;
    private $api_url;
    public $num_requests = 0;
    private $client_version = '1.6.1';

    private $curl;
    public $debug = false;

    public function __construct($api_key, $api_url = 'https://api.telerivet.com/v1')
    {
        $this->api_key = $api_key;
        $this->api_url = $api_url;
    }

    function getProjectById($id)
    {
        return new Telerivet_Project($this, $this->doRequest("GET", "{$this->getBaseApiPath()}/projects/{$id}"));
    }

    function initProjectById($id)
    {
        return new Telerivet_Project($this, array('id' => $id), false);
    }

    function queryProjects($options = null)
    {
        return $this->newApiCursor('Telerivet_Project', "{$this->getBaseApiPath()}/projects", $options);
    }

    function getOrganizationById($id)
    {
        return new Telerivet_Organization($this, $this->doRequest("GET", "{$this->getBaseApiPath()}/organizations/{$id}"));
    }

    function initOrganizationById($id)
    {
        return new Telerivet_Organization($this, array('id' => $id), false);
    }

    function queryOrganizations($options = null)
    {
        return $this->newApiCursor('Telerivet_Organization', "{$this->getBaseApiPath()}/organizations", $options);
    }

    function getBaseApiPath()
    {
        return "";
    }
    function doRequest($method, $path, $params = null)
    {
        $curl = $this->curl;
        if (!$curl)
        {
            $curl = $this->curl = curl_init();
        }

        $url = "{$this->api_url}{$path}";

        $headers = array(
            "User-Agent: Telerivet PHP Client/{$this->client_version} PHP/" . PHP_VERSION . " OS/" . PHP_OS,
            "Expect:", // avoid sending Expect: 100-continue to reduce latency
        );
        if ($method === 'POST' || $method == 'PUT')
        {
            $headers[] = "Content-Type: application/json";
            $post_data = json_encode($params);
            $data_len = strlen($post_data);
            if ($data_len >= 400 && function_exists('gzencode'))
            {
                $headers[] = "Content-Encoding: gzip";
                $post_data = gzencode($post_data);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }
        else
        {
            if ($params)
            {
                $url .= "?" . http_build_query($params, '', '&');
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, '');
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_BUFFERSIZE, 4096);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');

        if ($this->debug)
        {
            error_log("$method $url");
        }

        $cacert_file = dirname(__FILE__) . "/cacert.pem";
        if (file_exists($cacert_file))
        {
            curl_setopt($curl, CURLOPT_CAINFO, $cacert_file);
        }
        curl_setopt($curl, CURLOPT_USERPWD, "{$this->api_key}:");

        $this->num_requests++;

        $response_json = curl_exec($curl);
        $network_error = curl_error($curl);

        if ($network_error)
        {
            throw new Telerivet_IOException("Error connecting to Telerivet API: {$network_error}");
        }
        else
        {
            $response = json_decode($response_json, true);

            if (isset($response['error']))
            {
                $error = $response['error'];
                $error_code = $error['code'];
                switch ($error_code)
                {
                    case 'invalid_param':
                        throw new Telerivet_InvalidParameterException($error['message'], $error['code'], $error['param']);
                    case 'not_found':
                        throw new Telerivet_NotFoundException($error['message'], $error['code']);
                    default:
                        throw new Telerivet_APIException($error['message'], $error['code']);
                }
            }
            else if ($response)
            {
                return $response;
            }
            else if (json_last_error() != JSON_ERROR_NONE || $response_json === '')
            {
                $info = curl_getinfo($curl);
                $http_code = $info['http_code'];
                throw new Telerivet_IOException("Unexpected response from Telerivet API (HTTP {$http_code}): {$response_json}");
            }
            else
            {
                return $response;
            }
        }
    }

    function __destruct()
    {
        if ($this->curl)
        {
            curl_close($this->curl);
        }
    }

    function newApiCursor($item_cls, $path, $options)
    {
        return new Telerivet_ApiCursor($this, $item_cls, $path, $options);
    }
}

// base class for exceptions raised by this library
class Telerivet_Exception extends Exception {}

// exception corresponding to error returned in API response
class Telerivet_APIException extends Telerivet_Exception
{
    public $error_code;

    function __construct($message, $error_code)
    {
        parent::__construct($message);
        $this->error_code = $error_code;
    }
}

class Telerivet_InvalidParameterException extends Telerivet_APIException
{
    public $param;
    function __construct($message, $error_code, $param)
    {
        parent::__construct($message, $error_code);
        $this->param = $param;
    }
}

class Telerivet_NotFoundException extends Telerivet_APIException
{
    function __construct($message, $error_code)
    {
        parent::__construct($message, $error_code);
    }
}

// exception raised when client could not connect to server
class Telerivet_IOException extends Telerivet_Exception {}

$tr_lib_dir = dirname(__FILE__) . '/Telerivet';
require_once "{$tr_lib_dir}/entity.php";
require_once "{$tr_lib_dir}/apicursor.php";

require_once "{$tr_lib_dir}/message.php";
require_once "{$tr_lib_dir}/scheduledmessage.php";
require_once "{$tr_lib_dir}/contact.php";
require_once "{$tr_lib_dir}/broadcast.php";
require_once "{$tr_lib_dir}/task.php";
require_once "{$tr_lib_dir}/project.php";
require_once "{$tr_lib_dir}/label.php";
require_once "{$tr_lib_dir}/group.php";
require_once "{$tr_lib_dir}/phone.php";
require_once "{$tr_lib_dir}/route.php";
require_once "{$tr_lib_dir}/datatable.php";
require_once "{$tr_lib_dir}/datarow.php";
require_once "{$tr_lib_dir}/service.php";
require_once "{$tr_lib_dir}/contactservicestate.php";
require_once "{$tr_lib_dir}/organization.php";
require_once "{$tr_lib_dir}/airtimetransaction.php";

