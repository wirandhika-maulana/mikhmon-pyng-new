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
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {
	$aaa=$_GET['session'];
	$file="notif/session.log";
	file_put_contents($file, $aaa, LOCK_EX);


// get MikroTik system clock
  $getclock = $API->comm("/system/clock/print");
  $clock = $getclock[0];
  $timezone = $getclock[0]['time-zone-name'];
  $_SESSION['timezone'] = $timezone;
  date_default_timezone_set($timezone);

// get system resource MikroTik
  $getresource = $API->comm("/system/resource/print");
  $resource = $getresource[0];

// get routeboard info
  $getrouterboard = $API->comm("/system/routerboard/print");
  $routerboard = $getrouterboard[0];
/*
// move hotspot log to disk *
  $getlogging = $API->comm("/system/logging/print", array("?prefix" => "->", ));
  $logging = $getlogging[0];
  if ($logging['prefix'] == "->") {
  } else {
    $API->comm("/system/logging/add", array("action" => "disk", "prefix" => "->", "topics" => "hotspot,info,debug", ));
  }

// get hotspot log
  $getlog = $API->comm("/log/print", array("?topics" => "hotspot,info,debug", ));
  $log = array_reverse($getlog);
  $THotspotLog = count($getlog);
  $getlog = $API->comm("/log/print", array("?topics" => "pppoe,ppp,info,account", ));
  $log = array_reverse($getlog);
  $THotspotLog = count($getlog);
*/
// get & counting hotspot users
  $countallusers = $API->comm("/ip/hotspot/user/print", array("count-only" => ""));
  if ($countallusers < 2) {
    $uunit = "item";
  } elseif ($countallusers > 1) {
    $uunit = "items";
  }

// get & counting hotspot active
  $counthotspotactive = $API->comm("/ip/hotspot/active/print", array("count-only" => ""));
  if ($counthotspotactive < 2) {
    $hunit = "item";
  } elseif ($counthotspotactive > 1) {
    $hunit = "items";
  }

  if ($livereport == "disable") {
    $logh = "600px";
    $lreport = "style='display:none;'";
  } else {
    $logh = "450px";
    $lreport = "style='display:block;'";
  }
  
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

    // Determine which API to use for PPPoE stats
    $API_FOR_PPP = $API;
    $ppp_connected = false;
    if (!empty($dual_router_ip)) {
        $API_PPP = new RouterosAPI();
        $API_PPP->debug = false;
        if ($API_PPP->connect($dual_router_ip, $dual_router_user, $dual_router_pass)) {
            $API_FOR_PPP = $API_PPP;
            $ppp_connected = true;
            // Get PPPoE router resource
            $getresource_ppp = $API_FOR_PPP->comm("/system/resource/print");
            $resource_ppp = $getresource_ppp[0];
            $getrb_ppp = $API_FOR_PPP->comm("/system/routerboard/print");
            $routerboard_ppp = $getrb_ppp[0];
        }
    }

    // get & counting ppp profiles optimized
    $countprofiles = $API_FOR_PPP->comm("/ppp/profile/print", array(
        "?default" => "false",
        "count-only" => ""
    ));
    if (!is_numeric($countprofiles)) {
        $countprofiles = 0;
    }
    if ($countprofiles < 2) {
        $uunit = "item";
    } else {
        $uunit = "items";
    }

    // get ppp active
    $getactive = $API_FOR_PPP->comm("/ppp/active/print", array(".proplist" => "name"));
    $countpppactive = is_array($getactive) ? count($getactive) : 0;
    $active_names = [];
    if (is_array($getactive)) {
        foreach ($getactive as $act) {
            $active_names[] = $act['name'];
        }
    }

    if ($countpppactive < 2) {
        $hunit = "item";
    } elseif ($countpppactive > 1) {
        $hunit = "items";
    }

    // get & counting ppp secrets and calculate income
    $getsecrets = $API_FOR_PPP->comm("/ppp/secret/print", array(".proplist" => "name,profile"));
    $countsecrets = is_array($getsecrets) ? count($getsecrets) : 0;
    
    $pppoe_income = 0;
    if (is_array($getsecrets)) {
        foreach ($getsecrets as $sec) {
            // Only calculate if user is active
            if (in_array($sec['name'], $active_names)) {
                $profUpper = strtoupper($sec['profile']);
                if (strpos($profUpper, 'BRONZE') !== false) {
                    $pppoe_income += 120000;
                } elseif (strpos($profUpper, 'SILVER') !== false) {
                    $pppoe_income += 150000;
                } elseif (strpos($profUpper, 'GOLD') !== false) {
                    $pppoe_income += 170000;
                } elseif (strpos($profUpper, 'DIAMOND') !== false) {
                    $pppoe_income += 200000;
                }
            }
        }
    }
    
    if ($countsecrets < 2) {
        $hunit = "item";
    } elseif ($countsecrets > 1) {
        $hunit = "items";
    }

    // Calculate non-active PPP secrets
    $countpppnonactive = $countsecrets - $countpppactive;
    if ($countpppnonactive < 2) {
        $hunit = "item";
    } elseif ($countpppnonactive > 1) {
        $hunit = "items";
    }

    // if ($ppp_connected) {
    //     $API_PPP->disconnect();
    // }

/*
// get selling report
    $thisD = date("d");
    $thisM = strtolower(date("M"));
    $thisY = date("Y");

    if (strlen($thisD) == 1) {
      $thisD = "0" . $thisD;
    } else {
      $thisD = $thisD;
    }

    $idhr = $thisM . "/" . $thisD . "/" . $thisY;
    $idbl = $thisM . $thisY;

    $getSRHr = $API->comm("/system/script/print", array(
      "?source" => "$idhr",
    ));
    $TotalRHr = count($getSRHr);
    $getSRBl = $API->comm("/system/script/print", array(
      "?owner" => "$idbl",
    ));
    $TotalRBl = count($getSRBl);

    for ($i = 0; $i < $TotalRHr; $i++) {

      $tHr += explode("-|-", $getSRHr[$i]['name'])[3];

    }
    for ($i = 0; $i < $TotalRBl; $i++) {

      $tBl += explode("-|-", $getSRBl[$i]['name'])[3];
    }
  }*/
}
?>
    
<div id="reloadHome">

    <div id="r_1" class="row">
      <div class="col-4">
        <div class="box bmh-75 box-bordered">
          <div class="box-group">
            <div class="box-group-icon"><i class="fa fa-calendar"></i></div>
              <div class="box-group-area">
                <span ><?= $_system_date_time ?><br>
                    <?php 
                    echo ucfirst($clock['date']) . " " . $clock['time'] . "<br>
                    ".$_uptime." : " . formatDTM($resource['uptime']);
                    $_SESSION[$session.'sdate'] = $clock['date'];
                    ?>
                </span>
              </div>
            </div>
          </div>
        </div>
      <div class="col-4">
        <div class="box bmh-75 box-bordered">
          <div class="box-group">
          <div class="box-group-icon"><i class="fa fa-info-circle"></i></div>
              <div class="box-group-area">
                <span >
                    <?php
                    $disp_board_name = ($ppp_connected && isset($resource_ppp['board-name'])) ? $resource_ppp['board-name'] : $resource['board-name'];
                    $disp_model = ($ppp_connected && isset($routerboard_ppp['model'])) ? $routerboard_ppp['model'] : $routerboard['model'];
                    $disp_version = ($ppp_connected && isset($resource_ppp['version'])) ? $resource_ppp['version'] : $resource['version'];
                    
                    echo $_board_name." : " . $disp_board_name . "<br/>
                    ".$_model." : " . $disp_model . "<br/>
                    Router OS : " . $disp_version;
                    ?>
                </span>
              </div>
            </div>
          </div>
        </div>
    <div class="col-4">
      <div class="box bmh-75 box-bordered">
        <div class="box-group">
          <div class="box-group-icon"><i class="fa fa-server"></i></div>
              <div class="box-group-area">
                <span >
                    <?php
                    $disp_cpu_load = ($ppp_connected && isset($resource_ppp['cpu-load'])) ? $resource_ppp['cpu-load'] : $resource['cpu-load'];
                    $disp_free_memory = ($ppp_connected && isset($resource_ppp['free-memory'])) ? $resource_ppp['free-memory'] : $resource['free-memory'];
                    $disp_free_hdd = ($ppp_connected && isset($resource_ppp['free-hdd-space'])) ? $resource_ppp['free-hdd-space'] : $resource['free-hdd-space'];

                    echo $_cpu_load." : " . $disp_cpu_load . "%<br/>
                    ".$_free_memory." : " . formatBytes($disp_free_memory, 2) . "<br/>
                    ".$_free_hdd." : " . formatBytes($disp_free_hdd, 2)
                    ?>
                </span>
                </div>
              </div>
            </div>
          </div> 
      </div>

        <div class="row">
          <div  class="col-8">
            <div id="r_2"class="row">
            <div class="card">
              <div class="card-header"><h3><i class="fa fa-wifi"></i> Hotspot</h3></div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-3 col-box-6">
                      <div class="box bg-blue bmh-75">
                        <a onclick="cancelPage()" href="./?hotspot=active&session=<?= $session; ?>">
                          <h1><?= $counthotspotactive; ?>
                              <span style="font-size: 15px;"><?= $hunit; ?></span>
                            </h1>
                          <div>
                            <i class="fa fa-laptop"></i> <?= $_hotspot_active ?>
                          </div>
                        </a>
                      </div>
                    </div>
                    <div class="col-3 col-box-6">
                    <div class="box bg-green bmh-75">
                      <a onclick="cancelPage()" href="./?hotspot=users&profile=all&session=<?= $session; ?>">
                            <h1><?= $countallusers; ?>
                              <span style="font-size: 15px;"><?= $uunit; ?></span>
                            </h1>
                      <div>
                            <i class="fa fa-users"></i> <?= $_hotspot_users ?>
                          </div>
                      </a>
                    </div>
                  </div>
                  <div class="col-3 col-box-6">
                    <div class="box bg-yellow bmh-75">
                      <a onclick="cancelPage()" href="./?hotspot=reseller&session=<?= $session; ?>">
                        <div>
                          <h1><i class="fa fa-building"></i>
                              <span style="font-size: 15px;">Add Reseller</span>
                          </h1>
                        </div>
                        <div>
                            <i class="fa fa-building"></i> Reseller
                        </div>
                      </a>
                    </div>
                  </div>
                  <div class="col-3 col-box-6">
                    <div class="box bg-red bmh-75">
                      <a onclick="cancelPage()" href="./?hotspot-user=generate&session=<?= $session; ?>">
                        <div>
                          <h1><i class="fa fa-user-plus"></i>
                              <span style="font-size: 15px;"><?= $_generate ?></span>
                          </h1>
                        </div>
                        <div>
                            <i class="fa fa-user-plus"></i> <?= $_hotspot_users ?>
                        </div>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
		  <div id="r_2"class="row">
			<div class="card">
				<div class="card-header">
					<h3><i class="fa fa-sitemap  "></i> PPP</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-3 col-box-6">
							<div class="box bg-blue bmh-75">
								<a onclick="cancelPage()" href="./?ppp=secrets&session=<?= $session; ?>">
									<h1><?= $countsecrets; ?>
										<span style="font-size: 15px;"><?= $uunit; ?></span>
									</h1>
									<div>
										<i class="fa fa-user-secret"></i> <?= $_ppp_secrets ?>
									</div>
								</a>
							</div>
						</div>
						<div class="col-3 col-box-6">
							<div class="box bg-green bmh-75">
								<a onclick="cancelPage()" href="./?ppp=active&session=<?= $session; ?>">
									<h1><?= $countpppactive; ?>
										<span style="font-size: 15px;"><?= $hunit; ?></span>
									</h1>
									<div>
										<i class="fa fa-laptop"></i> <?= $_ppp_active ?>
									</div>
								</a>
							</div>
						</div>
						<div class="col-3 col-box-6">
							<div class="box bg-yellow bmh-75">
                  <a onclick="cancelPage()" href="./?ppp=nonactive&session=<?= $session; ?>">
                      <h1><?= $countpppnonactive; ?>
                          <span style="font-size: 15px;"><?= $hunit; ?></span>
                      </h1>
                      <div>
                          <i class="fa fa-laptop"></i> <?= $_ppp_non_active ?>
									</div>
								</a>
							</div>
						</div>

						<div class="col-3 col-box-6">
              <div class="box bg-red bmh-75">
                        <a onclick="cancelPage()" href="./?ppp=addsecret&session=<?= $session; ?>">
                            <div>
                                <h1><i class="fa fa-user-plus"></i>
                                    <span style="font-size: 15px;">Add</span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-user-plus"></i> PPP Secret
                            </div>
                        </a>
							</div>
						</div>
						

						
					</div>
				</div>
			</div>           
          </div>
           
			<div class="card">
				<div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
					<h3><i class="fa fa-area-chart"></i> <?= $_traffic ?> </h3>
                    <div style="margin-top: 5px;">
                        <span style="font-size: 11px; color:#888; margin-right:8px;">*Live grafik memicu log API</span>
                        <button id="btnStartTraffic" class="btn bg-primary" style="padding: 4px 10px; font-size:13px;" onclick="startTraffic()"><i class="fa fa-play"></i> Live Traffic</button>
                        <button id="btnStopTraffic" class="btn bg-danger" style="padding: 4px 10px; font-size:13px; display:none;" onclick="stopTraffic()"><i class="fa fa-stop"></i> Stop</button>
                    </div>
				</div>


              <div class="card-body">
  
                  <?php $getinterface = $API_FOR_PPP->comm("/interface/print");
                  $interface = $getinterface[$iface - 1]['name']; 
                  /*$TotalReg = count($getinterface);
                  for ($i = 0; $i < $TotalReg; $i++) {
                    echo $getinterface[$i]['name'].'<br>';
                  }*/
                  ?>
                  
                  <script type="text/javascript"> 
                    var chart;
                    var sessiondata = "<?= $session ?>";
                    var interface = "<?= $interface ?>";
                    var n = 3000;
                    var trafficInterval;

                    function startTraffic() {
                        document.getElementById('btnStartTraffic').style.display = 'none';
                        document.getElementById('btnStopTraffic').style.display = 'inline-block';
                        // Fetch first point immediately
                        requestDatta(sessiondata,interface);
                        // Then interval
                        trafficInterval = setInterval(function () {
                            requestDatta(sessiondata,interface);
                        }, n);
                    }

                    function stopTraffic() {
                        document.getElementById('btnStopTraffic').style.display = 'none';
                        document.getElementById('btnStartTraffic').style.display = 'inline-block';
                        if(trafficInterval) {
                            clearInterval(trafficInterval);
                        }
                    }

                    function requestDatta(session,iface) {
                      $.ajax({
                        url: './traffic/traffic.php?session='+session+'&iface='+iface,
                        datatype: "json",
                        success: function(data) {
                          var midata = JSON.parse(data);
                          if( midata.length > 0 ) {
                            var TX=parseInt(midata[0].data);
                            var RX=parseInt(midata[1].data);
                            var x = (new Date()).getTime(); 
                            shift=chart.series[0].data.length > 19;
                            chart.series[0].addPoint([x, TX], true, shift);
                            chart.series[1].addPoint([x, RX], true, shift);
                          }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) { 
                          console.error("Status: " + textStatus + " request: " + XMLHttpRequest); console.error("Error: " + errorThrown); 
                        }       
                      });
                    }	

                    $(document).ready(function() {
                        Highcharts.setOptions({
                          global: {
                            useUTC: false
                          }
                        });

                        Highcharts.addEvent(Highcharts.Series, 'afterInit', function () {
	                        this.symbolUnicode = {
    	                    circle: '●',
                          diamond: '♦',
                          square: '■',
                          triangle: '▲',
                          'triangle-down': '▼'
                          }[this.symbol] || '●';
                        });

                          chart = new Highcharts.Chart({
                          chart: {
                          renderTo: 'trafficMonitor',
                          animation: Highcharts.svg,
                          type: 'areaspline',
                          events: {
                            load: function () {
                              // setInterval removed to prevent MikroTik API log flooding
                            }				
                          }
                        },
                        title: {
                          text: '<?= $_interface ?> ' + interface
                        },
                        
                        xAxis: {
                          type: 'datetime',
                          tickPixelInterval: 150,
                          maxZoom: 20 * 1000,
                        },
                        yAxis: {
                            minPadding: 0.2,
                            maxPadding: 0.2,
                            title: {
                              text: null
                            },
                            labels: {
                              formatter: function () {      
                                var bytes = this.value;                          
                                var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
                                if (bytes == 0) return '0 bps';
                                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                                return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];                    
                              },
                            },       
                        },
                        
                        series: [{
                          name: 'Tx',
                          data: [],
                          marker: {
                            symbol: 'circle'
                          }
                        }, {
                          name: 'Rx',
                          data: [],
                          marker: {
                            symbol: 'circle'
                          }
                        }],

                        tooltip: {
                          formatter: function () { 
                            // Inisialisasi array untuk menyimpan hasil format data
                            var s = [];
                            
                            // Mengambil elemen dengan class 'points' dan mengiterasi setiap elemen
                            $('points').each(function(index, element) {
                                // Mengambil nilai 'y' dari elemen saat ini
                                var value = element.y; 
                                // Daftar unit untuk kecepatan data
                                var units = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps']; 
                            
                                // Jika nilai adalah 0, tambahkan informasi ke dalam array s
                                if (value == 0) {
                                    s.push("<span style='color:" + element.color + "; font-size: 1.5em;'>" + element.series + 
                                            "</span><b>Name:</b> 0 bps");
                                } else {
                                    // Menghitung indeks ukuran berdasarkan nilai
                                    var sizeIndex = parseInt(Math.floor(Math.log(value) / Math.log(1024)));
                                    // Menambahkan informasi yang diformat ke dalam array s
                                    s.push("<span style='color:" + element.color + "; font-size: 1.5em;'>" + element.series + 
                                            "</span><b>Name:</b> " + parseFloat((value / Math.pow(1024, sizeIndex)).toFixed(2)) + 
                                            " " + units[sizeIndex]);
                                }
                            });
                            
                            // Mengembalikan string yang berisi waktu saat ini dan data yang telah dikumpulkan
                            return "Time: " + Highcharts.dateFormat("%H:%M:%S", new Date(this.timestamp)) + "<br>" + s.join("<br>");
                          },
                          shared: true                                                      
                        },
                      });
                    });
                  </script>
                  <div id="trafficMonitor"></div>
                </div> 
              </div>
            </div>  
            <div class="col-4">
            <div id="r_4" class="row">
              <div <?= $lreport; ?> class="box bmh-75 box-bordered">
                <div class="box-group">
                  <div class="box-group-icon"><i class="fa fa-money"></i></div>
                    <div class="box-group-area">
                      <span >
                        <div id="reloadLreport">
                          <?php 
                          if ($_SESSION[$session.'sdate'] == $_SESSION[$session.'idhr']){
                            echo $_income." Hotspot<br/>" . "
                          ".$_today." " . $_SESSION[$session.'totalHr'] . "vcr : " . $currency . " " . $_SESSION[$session.'dincome']. "<br/>
                          ".$_this_month." " . $_SESSION[$session.'totalBl'] . "vcr : " . $currency . " " . $_SESSION[$session.'mincome']; 
                          }else{
                            echo "<div id='loader' ><i><span> <i class='fa fa-circle-o-notch fa-spin'></i> ". $_processing." </i></div>";
                          }
                          ?>                       
                        </div>
                    </span>
                </div>
              </div>
            </div>
            </div>
            <div id="r_3" class="row">
            <div class="card">
              <div class="card-header">
                <h3><a onclick="cancelPage()" href="./?hotspot=log&session=<?= $session; ?>" title="Open Hotspot Log" ><i class="fa fa-align-justify"></i> <?= $_hotspot_log ?></a></h3></div>
                  <div class="card-body">
                    <div style="padding: 5px; height: <?= $logh; ?> ;" class="mr-t-10 overflow">
                      <table class="table table-sm table-bordered table-hover" style="font-size: 12px; td.padding:2px;">
                        <thead>
                          <tr>
                            <th><?= $_time ?></th>
                            <th><?= $_users ?> (IP)</th>
                            <th><?= $_messages ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td colspan="3" class="text-center">
                            <div id="loader" ><i><i class='fa fa-circle-o-notch fa-spin'></i> <?= $_processing ?> </i></div>
                            </td>
                          </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              </div>
            </div>
</div>
</div>
