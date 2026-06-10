<?php
session_start();
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {

  // Fetch Board Name and Interfaces for Router A
  $getresource_a = $API->comm("/system/resource/print");
  $board_name_a = isset($getresource_a[0]['board-name']) ? $getresource_a[0]['board-name'] : 'Unknown';
  $getinterface_a = $API->comm("/interface/print");
  $TotalReg_a = count($getinterface_a);

  // Connect to Router B and Fetch
  $use_dual = false;
  $board_name_b = "Router B";
  $getinterface_b = array();
  $TotalReg_b = 0;

  if (file_exists('./include/dual_router_config.php')) {
      include('./include/dual_router_config.php');
      if (isset($dual_router[$session]) && !empty($dual_router[$session]['ip'])) {
          $dual_router_ip = $dual_router[$session]['ip'];
          $dual_router_user = $dual_router[$session]['user'];
          $dual_router_pass = decrypt($dual_router[$session]['pass']);
          
          $API_B = new RouterosAPI();
          $API_B->debug = false;
          if ($API_B->connect($dual_router_ip, $dual_router_user, $dual_router_pass)) {
              $use_dual = true;
              $getresource_b = $API_B->comm("/system/resource/print");
              if (isset($getresource_b[0]['board-name'])) {
                  $board_name_b = $getresource_b[0]['board-name'];
              }
              $getinterface_b = $API_B->comm("/interface/print");
              $TotalReg_b = count($getinterface_b);
              $API_B->disconnect();
          }
      }
  }

}
?>
          <!-- ROUTER A (HOTSPOT) -->
          <div class="card mb-3">
            <div class="card-header"><h3><i class="fa fa-area-chart"></i> Traffic Monitor <?= $board_name_a ?> (Hotspot) </h3></div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                  <select id="d_interface_a" class="dropd pd-5" name="iface_a">
                    <option value=""><?= $_select_interface ?></option>
                    <?php 
                      for ($i = 0; $i < $TotalReg_a; $i++) {
                        $no=$i+1;
						echo '<option value="' . $getinterface_a[$i]['name'] . '"> [ '.$no.'] - ' . $getinterface_a[$i]['name'] . '</option>';
					}
                    ?>
                  </select>
                  </div>
                  <div class="col-12" id="trafficMonitorA"></div>
                </div>
              </div>  
          </div>

          <?php if ($use_dual): ?>
          <!-- ROUTER B (PPPOE) -->
          <div class="card">
            <div class="card-header"><h3><i class="fa fa-area-chart"></i> Traffic Monitor <?= $board_name_b ?> (PPPoE) </h3></div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                  <select id="d_interface_b" class="dropd pd-5" name="iface_b">
                    <option value=""><?= $_select_interface ?></option>
                    <?php 
                      for ($i = 0; $i < $TotalReg_b; $i++) {
                        $no=$i+1;
						echo '<option value="' . $getinterface_b[$i]['name'] . '"> [ '.$no.'] - ' . $getinterface_b[$i]['name'] . '</option>';
					}
                    ?>
                  </select>
                  </div>
                  <div class="col-12" id="trafficMonitorB"></div>
                </div>
              </div>  
          </div>
          <?php endif; ?>

<script type="text/javascript"> 
  var chartA, chartB;
  var sessiondata = "<?= $session ?>";
  var intervalA, intervalB;

  function initChartA() {
      Highcharts.setOptions({
        global: { useUTC: false },
        chart: { height: 500 }
      });

      chartA = new Highcharts.Chart({
        chart: {
          renderTo: 'trafficMonitorA',
          animation: Highcharts.svg,
          type: 'areaspline'
        },
        title: { text: 'Loading interface...' },
        xAxis: { type: 'datetime', tickPixelInterval: 150, maxZoom: 20 * 1000 },
        yAxis: {
            minPadding: 0.2, maxPadding: 0.2, title: { text: null },
            labels: {
              formatter: function () {      
                var bytes = this.value;                          
                var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
                if (bytes == 0) return '0 bps';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];                    
              }
            }       
        },
        series: [{ name: 'Tx', data: [], marker: { symbol: 'circle' } }, 
                 { name: 'Rx', data: [], marker: { symbol: 'circle' } }],
        tooltip: {
          formatter: function () { 
            var s = [];
            $.each(this.points, function(i, point) {
                var bytes = point.y;
                var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
                if(bytes == 0) {
                    s.push('<span style="color:'+point.series.color+'; font-size: 1.5em;">\u25CF</span><b>'+point.series.name+':</b> 0 bps');
                } else {
                    var iSize = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                    s.push('<span style="color:'+point.series.color+'; font-size: 1.5em;">\u25CF</span><b>'+point.series.name+':</b> '+parseFloat((bytes / Math.pow(1024, iSize)).toFixed(2))+' '+sizes[iSize]);
                }
            });
            return '<b>Mikhmon Traffic Monitor (Hotspot)</b><br /><b>Time: </b>'+Highcharts.dateFormat('%H:%M:%S', new Date(this.x))+' <br/> '+s.join(' <br/> ');
          },
          shared: true                                                      
        }
      });
  }

  function requestDattaA(iface) {
    if (!iface || iface === "Select Interface" || iface === "") return;
    $.ajax({
      url: './traffic/traffic.php?session='+sessiondata+'&iface='+iface+'&router=A',
      datatype: "json",
      success: function(data) {
        try {
          var midata = JSON.parse(data);
          if( midata.length > 0 ) {
            var TX=parseInt(midata[0].data);
            var RX=parseInt(midata[1].data);
            var x = (new Date()).getTime(); 
            shift=chartA.series[0].data.length > 19;
            chartA.series[0].addPoint([x, TX], true, shift);
            chartA.series[1].addPoint([x, RX], true, shift);
            chartA.setTitle({ text: 'Interface: ' + iface });
          }
        } catch(e) {}
      }
    });
  }

  function initChartB() {
      chartB = new Highcharts.Chart({
        chart: {
          renderTo: 'trafficMonitorB',
          animation: Highcharts.svg,
          type: 'areaspline'
        },
        title: { text: 'Loading interface...' },
        xAxis: { type: 'datetime', tickPixelInterval: 150, maxZoom: 20 * 1000 },
        yAxis: {
            minPadding: 0.2, maxPadding: 0.2, title: { text: null },
            labels: {
              formatter: function () {      
                var bytes = this.value;                          
                var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
                if (bytes == 0) return '0 bps';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];                    
              }
            }       
        },
        series: [{ name: 'Tx', data: [], marker: { symbol: 'circle' }, color: '#f45b5b' }, 
                 { name: 'Rx', data: [], marker: { symbol: 'circle' }, color: '#8085e9' }],
        tooltip: {
          formatter: function () { 
            var s = [];
            $.each(this.points, function(i, point) {
                var bytes = point.y;
                var sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
                if(bytes == 0) {
                    s.push('<span style="color:'+point.series.color+'; font-size: 1.5em;">\u25CF</span><b>'+point.series.name+':</b> 0 bps');
                } else {
                    var iSize = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                    s.push('<span style="color:'+point.series.color+'; font-size: 1.5em;">\u25CF</span><b>'+point.series.name+':</b> '+parseFloat((bytes / Math.pow(1024, iSize)).toFixed(2))+' '+sizes[iSize]);
                }
            });
            return '<b>Mikhmon Traffic Monitor (PPPoE)</b><br /><b>Time: </b>'+Highcharts.dateFormat('%H:%M:%S', new Date(this.x))+' <br/> '+s.join(' <br/> ');
          },
          shared: true                                                      
        }
      });
  }

  function requestDattaB(iface) {
    if (!iface || iface === "Select Interface" || iface === "") return;
    $.ajax({
      url: './traffic/traffic.php?session='+sessiondata+'&iface='+iface+'&router=B',
      datatype: "json",
      success: function(data) {
        try {
          var midata = JSON.parse(data);
          if( midata.length > 0 ) {
            var TX=parseInt(midata[0].data);
            var RX=parseInt(midata[1].data);
            var x = (new Date()).getTime(); 
            shift=chartB.series[0].data.length > 19;
            chartB.series[0].addPoint([x, TX], true, shift);
            chartB.series[1].addPoint([x, RX], true, shift);
            chartB.setTitle({ text: 'Interface: ' + iface });
          }
        } catch(e) {}
      }
    });
  }

  $(document).ready(function() {
      initChartA();
      if ($('#trafficMonitorB').length) {
          initChartB();
      }

      // Restore selections
      var savedIfaceA = sessionStorage.getItem('Interface_A_' + sessiondata);
      if (savedIfaceA) {
          $('#d_interface_a').val(savedIfaceA);
          intervalA = setInterval(function() { requestDattaA(savedIfaceA); }, 3000);
          requestDattaA(savedIfaceA);
      } else {
          chartA.setTitle({ text: 'Please select an interface' });
      }

      var savedIfaceB = sessionStorage.getItem('Interface_B_' + sessiondata);
      if (savedIfaceB) {
          $('#d_interface_b').val(savedIfaceB);
          if ($('#trafficMonitorB').length) {
              intervalB = setInterval(function() { requestDattaB(savedIfaceB); }, 3000);
              requestDattaB(savedIfaceB);
          }
      } else {
          if (chartB) chartB.setTitle({ text: 'Please select an interface' });
      }

      // Change events
      $('#d_interface_a').on('change', function() {
          var val = $(this).val();
          if(!val) return;
          sessionStorage.setItem('Interface_A_' + sessiondata, val);
          chartA.series[0].setData([]);
          chartA.series[1].setData([]);
          if(intervalA) clearInterval(intervalA);
          intervalA = setInterval(function() { requestDattaA(val); }, 3000);
          requestDattaA(val);
      });

      $('#d_interface_b').on('change', function() {
          var val = $(this).val();
          if(!val) return;
          sessionStorage.setItem('Interface_B_' + sessiondata, val);
          chartB.series[0].setData([]);
          chartB.series[1].setData([]);
          if(intervalB) clearInterval(intervalB);
          intervalB = setInterval(function() { requestDattaB(val); }, 3000);
          requestDattaB(val);
      });
  });
</script>