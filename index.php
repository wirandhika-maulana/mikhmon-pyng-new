<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);
// check url

ob_start("ob_gzhandler");


$url = $_SERVER['REQUEST_URI'];

// load session MikroTik

$session = $_GET['session'];

if (!isset($_SESSION["mikhmon"])) {
  header("Location:./admin.php?id=login");
} elseif (empty($session)) {
  echo "<script>window.location='./admin.php?id=sessions'</script>";
} else {
  $_SESSION["$session"] = $session;
  $setsession = $_SESSION["$session"];

  $_SESSION["connect"] = "";

// time zone
  date_default_timezone_set($_SESSION['timezone']);

// lang
  include('./include/lang.php');
  include('./lang/'.$langid.'.php');

// quick bt
  include('./include/quickbt.php');

// load config
  include('./include/config.php');
  include('./include/readcfg.php');

// theme  
  include('./include/theme.php');
  include('./settings/settheme.php');
  if ($_SESSION['theme'] == "") {
    $theme = $theme;
    $themecolor = $themecolor;
  } else {
    $theme = $_SESSION['theme'];
    $themecolor = $_SESSION['themecolor'];
  }

// routeros api
  include_once('./lib/routeros_api.class.php');
  include_once('./lib/formatbytesbites.php');
  include_once('./include/cache.php');
  
  $API = new RouterosAPI();
  $API->debug = false;

  // Load Dual Router Config (Secondary PPPoE Router)
  $dual_router_ip = "";
  $dual_router_user = "";
  $dual_router_pass = "";
  $dual_file = "./include/dual_router_config.php";
  if (file_exists($dual_file)) {
      include($dual_file);
      if (isset($dual_router[$session]) && !empty($dual_router[$session]['ip'])) {
          $dual_router_ip = $dual_router[$session]['ip'];
          $dual_router_user = $dual_router[$session]['user'];
          $dual_router_pass = decrypt($dual_router[$session]['pass']);
      }
  }

  // Determine if current request is for a PPPoE module
  $ppp_get = isset($_GET['ppp']) ? $_GET['ppp'] : '';
  $pageppp = array(
      'secrets','profiles','active','addsecret','billing','history',
      'nonactive', 'ppplog', 'grafik', 'edit-profile', 'add-profile', 'add-secret-by-profile'
  );
  $is_pppoe = false;
  if (
      in_array($ppp_get, $pageppp) || 
      isset($_GET['enable-pppsecret']) || 
      isset($_GET['disable-pppsecret']) || 
      isset($_GET['remove-pppsecret']) || 
      isset($_GET['remove-pprofile']) || 
      isset($_GET['remove-pactive'])
  ) {
      $is_pppoe = true;
  }

  // Connect to the appropriate router
  if ($is_pppoe && !empty($dual_router_ip)) {
      $API->connect($dual_router_ip, $dual_router_user, $dual_router_pass);
  } else {
      $API->connect($iphost, $userhost, decrypt($passwdhost));
  }

  $getidentity = $API->comm("/system/identity/print");
  $identity = $getidentity[0]['name'];
  

// get variable
  $regiter = $_GET['id'];
  $hotspot = $_GET['hotspot'];
  $hotspotuser = $_GET['hotspot-user'];
  $userbyname = $_GET['hotspot-user'];
  $removeuseractive = $_GET['remove-user-active'];
  $removehost = $_GET['remove-host'];
  $removecookie = $_GET['remove-cookie'];
  $removeipbinding = $_GET['remove-ip-binding'];
  $removehotspotuser = $_GET['remove-hotspot-user'];
  $removehotspotusers = $_GET['remove-hotspot-users'];
  $removeuserprofile = $_GET['remove-user-profile'];
  $resethotspotuser = $_GET['reset-hotspot-user'];
  $removehotspotuserbycomment = $_GET['remove-hotspot-user-by-comment'];
  $removeexpiredhotspotuser = $_GET['remove-hotspot-user-expired'];
  $enablehotspotuser = $_GET['enable-hotspot-user'];
  $disablehotspotuser = $_GET['disable-hotspot-user'];
  $enableipbinding = $_GET['enable-ip-binding'];
  $disableipbinding = $_GET['disable-ip-binding'];
  $userprofile = $_GET['user-profile'];
  $userprofilebyname = $_GET['user-profile'];
  $sys = $_GET['system'];
  $enablesch = $_GET['enable-scheduler'];
  $disablesch = $_GET['disable-scheduler'];
  $removesch = $_GET['remove-scheduler'];
  $macbinding = $_GET['mac'];
  $ipbinding = $_GET['addr'];

  $bindingip = $_GET['idipbinding'];
  $blockipbinding=$_GET['block-ip-binding'];
  $reguleripbinding=$_GET['reguler-ip-binding'];
  $bypassedipbinding=$_GET['bypassed-ip-binding'];
 
  $ppp = $_GET['ppp'];
  $idbayar = $_GET['bayar'];
  $secretbyname = $_GET['secret'];
  $enablesecr = $_GET['enable-pppsecret'];
  $disablesecr = $_GET['disable-pppsecret'];
  $removesecr = $_GET['remove-pppsecret'];
  $removepprofile = $_GET['remove-pprofile'];
  $removepactive = $_GET['remove-pactive'];

  $srv = $_GET['srv'];
  $prof = $_GET['profile'];
  $comm = $_GET['comment'];
  $serveractive = $_GET['server'];
  $report = $_GET['report'];
  $removereport = $_GET['remove-report'];

  $minterface = $_GET['interface'];
  $enableNetwatch = $_GET['enable-Netwatch'];
  $disableNetwatch = $_GET['disable-Netwatch'];
  $removeNetwatch = $_GET['remove-Netwatch'];
  $editNetwatch = $_GET['edit-Netwatch'];
  $onoffnotif = $_GET['on-off-notif'];
  $bbilling	= $_GET['bbilling'];
  $tripay= $_GET['tri-pay'];
  $info=$_GET['info'];
  $wagateway=isset($_GET['wagateway']) ? $_GET['wagateway'] : '';
  $telegrambot=isset($_GET['telegrambot']) ? $_GET['telegrambot'] : '';

  $pagehotspot = array('users','hosts','ipbinding','cookies','log','dhcp-leases');
  $pageppp = array('secrets','profiles','active','addsecret','billing','history');
  $pagereport = array('userlog','selling');
//  $hbilling = array('billing');
  include_once('./include/headhtml.php');

  include_once('./include/menu.php');

  $disable_sci = '<script>
  document.getElementById("comment").onkeypress = function(e) {
    var chr = String.fromCharCode(e.which);
    if (" _!@#$%^&*()+=;|?,.~".indexOf(chr) >= 0)
        return false;
};
</script>';


// logout
  if ($hotspot == "logout") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Logout...</b>";

    session_destroy();
    echo "<script>sessionStorage.clear();</script>";
    echo "<script>window.location='./admin.php?id=login'</script>";
  }
// redirect to home
  elseif (substr(explode("=", $url)[0],-9) == "/?session") {

    include_once('./dashboard/home.php');
    $_SESSION['ubn'] = "";
  }

// redirect to home
  elseif ($hotspot == "dashboard") {
    include_once('./dashboard/home.php');
    $_SESSION['ubn'] = "";

  }

// hotspot log
  elseif ($hotspot == "log") {
    include_once('./hotspot/log.php');
  }

// hotspot log
  elseif ($report == "userlog") {
    include_once('./report/userlog.php');
  }

// about
  elseif ($hotspot == "about") {
    include_once('./include/about.php');
  }

// bad request
  elseif (substr($url, -1) == "=") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Bad request! redirect to Home......</b>";

    echo "<script>window.location='./'</script>";
  }

// hotspot add users
  elseif ($hotspot == "add-user") {
    $_SESSION['hua'] = "";
    include_once('./hotspot/adduser.php');
  }

// hotspot users
  elseif ($hotspot == "users" && $prof == "all") {
    $_SESSION['ubp'] = "";
    $_SESSION['hua'] = "";
    $_SESSION['ubc'] = "";
    $_SESSION['vcr'] = "";
    include_once('./hotspot/users.php');
  }

// hotspot users filter by profile
  elseif ($hotspot == "users" && $prof != "") {
    $_SESSION['ubp'] = $prof;
    $_SESSION['hua'] = "";
    $_SESSION['ubc'] = "";
    $_SESSION['vcr'] = "";
    include_once('./hotspot/users.php');
  }

// hotspot users filter by comment
  elseif ($hotspot == "users" && $comm != "") {
    $_SESSION['ubc'] = $comm;
    $_SESSION['hua'] = "";
    $_SESSION['ubp'] = "";
    $_SESSION['vcr'] = "";
    include_once('./hotspot/users.php');
  }

// hotspot by profile
  elseif ($hotspot == "users-by-profile") {
    $_SESSION['ubp'] = "";
    $_SESSION['hua'] = "";
    $_SESSION['ubc'] = "";
    $_SESSION['vcr'] = "active";
    include_once('./hotspot/userbyprofile.php');
  }
// export hotspot users
  elseif ($hotspot == "export-users") {
    include_once('./hotspot/exportusers.php');
  }

// quick print
  elseif ($hotspot == "quick-print") {
    include_once('./hotspot/quickprint.php');
  }

// quick print
elseif ($hotspot == "list-quick-print") {
  include_once('./hotspot/listquickprint.php');
}  

// add hotspot user
  elseif ($hotspotuser == "add") {
    include_once('./hotspot/adduser.php');
    echo $disable_sci;
  }

// add hotspot user
  elseif ($hotspotuser == "generate") {
    include_once('./hotspot/generateuser.php');
    echo $disable_sci;
  }

// hotspot users filter by name
  elseif (substr($hotspotuser, 0, 1) == "*") {
    $_SESSION['ubn'] = $hotspotuser;
    $_SESSION['hua'] = "";
    include_once('./hotspot/userbyname.php');
  } elseif ($hotspotuser != "") {
    $_SESSION['ubn'] = $hotspotuser;
    include_once('./hotspot/userbyname.php');
  }

// remove hotspot user
  elseif ($removehotspotuser != "" || $removehotspotusers != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removehotspotuser.php');
  }

//tripay proses
  elseif ($tripay != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
    include_once('./tripay/proses.php');
  }
  elseif ($removehotspotuserbycomment != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removehotspotuserbycomment.php');
  }

// remove expired hotspot user
elseif ($removeexpiredhotspotuser != "") {
  echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

  include_once('./process/removeexpiredhotspotuser.php');
}  

// reset hotspot user
  elseif ($resethotspotuser != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/resethotspotuser.php');
  }

// enable hotspot user
  elseif ($enablehotspotuser != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/enablehotspotuser.php');
  }

// disable hotspot user
  elseif ($disablehotspotuser != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/disablehotspotuser.php');
  }

// user profile
  elseif ($hotspot == "user-profiles") {
    include_once('./hotspot/userprofile.php');
  }

// reseller
  elseif ($hotspot == "reseller") {
    include_once('./hotspot/reseller.php');
  }

// add  user profile
  elseif ($userprofile == "add") {
    include_once('./hotspot/adduserprofile.php');
  }

// User profile by name
  elseif (substr($userprofile, 0, 1) == "*") {
    include_once('./hotspot/userprofilebyname.php');
  } elseif ($userprofile != "") {
    include_once('./hotspot/userprofilebyname.php');
  }


// remove user profile
  elseif ($removeuserprofile != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removeuserprofile.php');
  }

// hotspot active
  elseif ($hotspot == "active") {
    $_SESSION['ubp'] = "";
    $_SESSION['hua'] = "hotspotactive";
    $_SESSION['ubc'] = "";
    include_once('./hotspot/hotspotactive.php');
  }

// dhcp leases
  elseif ($hotspot == "dhcp-leases") {
    include_once('./dhcp/dhcpleases.php');
  }

// traffic monitor
  elseif ($minterface == "traffic-monitor") {
  include_once('./traffic/trafficmonitor.php');
  }
  elseif ($minterface == "netwatch") {
  include_once('./traffic/netwatch.php');
  }
  elseif ($minterface == "add-netwatch") {
  $iphost=$_GET['iphost'];
  include_once('./traffic/inetwatch.php');
  }
  elseif ($minterface == "netwatchdown") {
  include_once('./traffic/netwatchdown.php');
  }
  elseif ($minterface == "netwatchup") {
  include_once('./traffic/netwatchup.php');
  }
  elseif ($minterface == "ping") {
  echo "<small style='display: none;'><i><i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small>";
  $idping=$_GET['idping'];
  include_once('./traffic/pinging.php');
  }

// remove enable disable Netwatch
  elseif ($removeNetwatch != "" || $enableNetwatch != "" || $disableNetwatch != "" || $editNetwatch != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
    include_once('./process/pnetwatch.php');
  }	

// hotspot hosts
  elseif ($hotspot == "hosts" || $hotspot == "hostp" || $hotspot == "hosta") {
    include_once('./hotspot/hosts.php');
  }

// hotspot bindings
  elseif ($hotspot == "binding") {
    include_once('./hotspot/binding.php');
  }

// template editor
  elseif ($hotspot == "template-editor") {
    include_once('./settings/template-editor.php');
  }

// whatsapp gateway
  elseif (isset($_GET['wagateway']) && $_GET['wagateway'] == "settings") {
    include_once('./wagateway/wagateway.php');
  }

// telegram bot
  elseif (isset($_GET['telegrambot']) && $_GET['telegrambot'] == "settings") {
    include_once('./telegrambot/telegrambot.php');
  }

// upload logo
  elseif ($hotspot == "uplogo") {
    include_once('./settings/uplogo.php');
  }

// hotspot Cookies
  elseif ($hotspot == "cookies") {
    include_once('./hotspot/cookies.php');
  }

// remove hotspot Cookies
  elseif ($removecookie != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removecookie.php');
  }

// hotspot Ip Bindings
  elseif ($hotspot == "ipbinding") {
    include_once('./hotspot/ipbinding.php');
  }
// add ip binding
  elseif ($hotspot == "add-ipbinding") {
    include_once('./hotspot/addipbinding.php');
  }
//make binding
  elseif ($hotspot == "binding-ip") {
	$idbinding=$_GET['idbinding'];
    include_once('./hotspot/bindingip.php');
  }
  elseif ($hotspot == "grafik") {
    $idpell=$_GET['idsecret'];
	include_once('./hotspot/trafficmonitor.php');
  }

// proses ipbinding
  elseif ($removeipbinding != "" || $enableipbinding != "" || $disableipbinding != "" || $blockipbinding != "" || $reguleripbinding != "" || $bypassedipbinding != "" ) {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/pipbinding.php');
  }

// remove user active
  elseif ($removeuseractive != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removeuseractive.php');
  }

// remove host
  elseif ($removehost != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removehost.php');
  }


// makebinding
  elseif ($macbinding != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/makebinding.php');
  }

// selling
  elseif ($report == "selling") {
    include_once('./report/selling.php');
  }

// selling
elseif ($report == "resume-report") {
  include_once('./report/resumereport.php');
}

// selling
elseif ($report == "export") {
  include_once('./report/export.php');
}

// selling
  elseif ($removereport != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removereport.php');
  }

// ppp secret
  elseif ($ppp == "secrets") {
    include_once('./ppp/pppsecrets.php');
  }

// ppp addsecret
  elseif ($ppp == "addsecret") {
    include_once('./ppp/addsecret.php');
  }

// ppp secretbyname
  elseif ($secretbyname != "") {
    include_once('./ppp/secretbyname.php');
  }

// proses ppp
  elseif ($removesecr != "" || $enablesecr != "" || $disablesecr != "" || $idbayar != "" || $removepprofile != "" || $onoffnotif !=""  ) {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
    include_once('./process/prosesppp.php');
  }

  elseif ($ppp == "billing") {
	$idpell=$_GET['idsecret'];
    include_once('./ppp/pppbilling.php');
  }
  elseif ($ppp == "grafik") {
	$idpell=$_GET['idsecret'];
    include_once('./ppp/trafficmonitor.php');
  }
  elseif ($ppp == "history") {
	$idpell=$_GET['idhistory'];
    include_once('./ppp/history.php');
  }


// ppp profile
  elseif ($ppp == "profiles") {
    include_once('./ppp/pppprofile.php');
  }

// add ppp profile
  elseif ($ppp == "add-profile") {
    include_once('./ppp/addpppprofile.php');
  }

// edit ppp profile
elseif ($ppp == "edit-profile") {
  include_once('./ppp/profilebyname.php');
}
// ppp active connection
  elseif ($ppp == "active") {
    include_once('./ppp/pppactive.php');
  }
  elseif ($ppp == "add-secret-by-profile") {
    include_once('./ppp/addsbyprofile.php');
  }
  // ppp non active connection
  elseif ($ppp == "nonactive") {
    include_once('./ppp/pppnonactive.php');
  }
  // ppp log connection
   elseif ($ppp == "ppplog") {
    include_once('./ppp/ppplog.php');
  }

// remove ppp active connection
  elseif ($removepactive != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

    include_once('./process/removepactive.php');
  }

  elseif ($bbilling == "bbilling") {
    include_once('./billing/billing.php');
  }

// sys scheduler
  elseif ($sys == "scheduler") {
    include_once('./system/scheduler.php');
  }
// info
  elseif ($info != "") {
	$pesan=$info;
    include_once('./info/info.php');
  }
// remove enable disable scheduler
  elseif ($removesch != "" || $enablesch != "" || $disablesch != "") {
    echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
    include_once('./process/pscheduler.php');
  }

  ?>

</div>
</div>
</div>
<script src="./js/highcharts/highcharts.js"></script>
<script src="./js/highcharts/themes/hc.<?= $theme; ?>.js"></script>
<script src="./js/mikhmon-ui.<?= $theme; ?>.min.js"></script>
<script src="./js/mikhmon.js?t=<?= str_replace(" ","_",date("Y-m-d H:i:s")); ?>"></script>

<?php
if ($hotspot == "dashboard" || substr(end(explode("/", $url)), 0, 8) == "?session") {
	echo '<script>
		$("#r_3").load("./dashboard/aload.php?session=' . $session . '&load=logs #r_3");  
		// Auto-refresh removed to prevent router log flooding
	';
	if ($livereport == "enable" || $livereport == "") {
		if($_SESSION[$session.'sdate'] != $_SESSION[$session.'idhr']){
			$_SESSION[$session.'totalHr'] = "0";
			echo '$("#r_4").load("./report/livereport.php?session=' . $session . ' #r_4");';
		}else if ($_SESSION[$session.'sdate'] == $_SESSION[$session.'idhr']){  
		}else{
			echo '$("#r_4").load("./report/livereport.php?session=' . $session . ' #r_4");';
		}
		echo  '
		// Live report auto-refresh removed to prevent log flooding
	';}
  echo ' 
  function cancelPage(){
    window.stop();
  }
</script>';

} elseif ($hotspot == "active" && $serveractive != "") {
  // Auto-refresh removed to prevent router log flooding
} elseif ($hotspot == "active" && $serveractive == "") {
  // Auto-refresh removed to prevent router log flooding
} elseif ($ppp == "active" ) {
  // Auto-refresh removed to prevent router log flooding
} elseif ($ppp == "nonactive" ) {
  // Auto-refresh removed to prevent router log flooding

} elseif ($minterface == "netwatch" ) {
  // Auto-refresh removed
} elseif ($minterface == "netwatchup" ) {
  // Auto-refresh removed
} elseif ($minterface == "netwatchdown" ) {
  // Auto-refresh removed
} elseif ($userprofile == "add" || substr($userprofile, 0, 1) == "*" || $userprofile != "") {
  echo "<script>
  //enable disable input on ready
	$(document).ready(function(){
    var exp = document.getElementById('expmode').value;
    var val = document.getElementById('validity').style;
    var vali = document.getElementById('validi');
    if (exp === 'rem' || exp === 'remc') {
      val.display= 'table-row';
      vali.type = 'text';
      $('#validi').focus();
    } else if (exp === 'ntf' || exp === 'ntfc') {
      val.display = 'table-row';
      vali.type = 'text';
      $('#validi').focus();
    } else {
      val.display = 'none';
      vali.type = 'hidden';
    }
	});
</script>";

} elseif (in_array($hotspot, $pagehotspot) || in_array($ppp, $pageppp) || in_array($report, $pagereport)   )  {
echo '
<script>
$(document).ready(function(){
  makeAllSortable();
  $("#filterTable").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#dataTable tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

</script>
';
} elseif ( $sys == "scheduler" || $bbilling == "bbilling" || $id == "websms"  ) {
echo '
<script>
$(document).ready(function(){
  makeAllSortable();
  $("#filterTable").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#dataTable tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

</script>
';
}

}

?>
</body>
</html>

