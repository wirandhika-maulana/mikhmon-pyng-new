<?php
// hide all error
//error_reporting(0);
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$session = $_GET['session'];

	include('../include/config.php');
	include('../include/readcfg.php');
	
// lang
	include('../include/lang.php');
	include('../lang/'.$langid.'.php');

    if (!isset($API) || !is_object($API)) {
	    $API = new RouterosAPI();
	    $API->debug = false;

        // Load Dual Router Config (Secondary PPPoE Router)
        $dual_router_ip = "";
        if (file_exists('../include/dual_router_config.php')) {
            include('../include/dual_router_config.php');
            if (isset($dual_router[$session]) && !empty($dual_router[$session]['ip'])) {
                $dual_router_ip = $dual_router[$session]['ip'];
                $dual_router_user = $dual_router[$session]['user'];
                $dual_router_pass = decrypt($dual_router[$session]['pass']);
            }
        }

        if (!empty($dual_router_ip)) {
            $API->connect($dual_router_ip, $dual_router_user, $dual_router_pass);
        } else {
	        $API->connect($iphost, $userhost, decrypt($passwdhost));
        }
    }

	// load session MikroTik
	$session = $_GET['session'];

	include "ppp/function.php";
	
    // Fetch system logs
    $getlog = $API->comm("/log/print");
    
    // Filter only PPP logs and reverse them so newest is on top
    $log = array();
    foreach ($getlog as $l) {
        if (isset($l['topics']) && strpos($l['topics'], 'ppp') !== false) {
            $log[] = $l;
        }
    }
    
    $log = array_reverse($log);
    $TotalReg = count($log);
}

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3><i class=" fa fa-align-justify"></i> <?= $_ppp_log ?> &nbsp; | &nbsp;&nbsp;<i onclick="location.reload();" class="fa fa-refresh pointer " title="Reload data"></i></h3>
            </div>
            <div class="card-body">
                <div style="max-width: 350px;">
                    <input id="filterTable" type="text" class="form-control" placeholder="Search.."> 
                </div>
                <div style="padding: 5px; max-height: 75vh;" class="mr-t-10 overflow">
                    <table class="table table-sm table-bordered table-hover" id="dataTable" >
                        <thead>
                            <tr>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Time </th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> User </th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Message </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 0; $i < $TotalReg; $i++) {
                                $logm = $log[$i]['message'];
                                
                                // Parse username vs action (e.g., "<pppoe-user1>: disconnected")
                                $parts = explode(": ", $logm, 2);
                                if (count($parts) > 1) {
                                    $loguser = $parts[0];
                                    $logmsg = $parts[1];
                                } else {
                                    $loguser = "system";
                                    $logmsg = $logm;
                                }
                                
                                // Clean up the user string if it has <pppoe-...> tags
                                $loguser = str_replace(array('<pppoe-', '>'), '', $loguser);
                            ?>
                                <tr>
                                    <td><?= $log[$i]['time'] ?></td>
                                    <td><?= trim($loguser) ?></td>
                                    <td><?= trim($logmsg) ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
