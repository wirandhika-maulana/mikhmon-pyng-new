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

// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {
	include_once('./voucher/temp.php');
	if (isset($genu) && $genu != "") {
		$urlprint = explode("|", decrypt($genu))[0];
	} else {
		$urlprint = "";
	}
?>
<style>
/* Modern Card Styles */
.gen-container {
    padding: 10px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.gen-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
    border: 1px solid #f1f5f9;
}
.gen-header {
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.gen-header.blue { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
.gen-header::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz48L3N2Zz4=');
    mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
}
.gen-header-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
    backdrop-filter: blur(10px);
    z-index: 1;
}
.gen-header-text {
    z-index: 1;
    display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: center;
}
.gen-header-text h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.gen-body {
    padding: 24px 30px;
}
.btn {
	transition: all 0.3s ease;
}
.btn:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media screen and (max-width: 768px) {
    .gen-body .col-6 {
        width: 100% !important;
        float: none !important;
        padding: 0 !important;
        margin-bottom: 15px;
    }
}
</style>

<div class="gen-container">
<!-- Quick Print Panel -->
<div class="row" style="margin-bottom: 15px;">
<div class="col-12">
	<div class="gen-card">
		<div class="gen-header blue">
            <div class="gen-header-icon">
                <i class="fa fa-print"></i>
            </div>
            <div class="gen-header-text">
                <h3>Quick Print</h3>
			</div>
		</div>
		<div class="gen-body" id="quickPrintBody">
			<div class="row">
				<!-- Print Baru (Last Generate) -->
				<div class="col-6">
					<div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
						<label style="font-weight: 600; font-size: 14px; color: #17a2b8; display: block; margin-bottom: 10px;"><i class="fa fa-bolt"></i> Print by Comment (Baru)</label>
						<div style="display: flex; gap: 8px; margin-bottom: 12px;">
							<select id="qpCommentBaru" class="form-control" style="flex: 1; border-radius: 6px; padding: 8px 12px;">
								<?php if ($urlprint) { ?>
									<option value="<?= htmlspecialchars($urlprint) ?>" selected><?= htmlspecialchars($urlprint) ?> (Baru)</option>
								<?php } else { ?>
									<option value="">Belum ada generate baru</option>
								<?php } ?>
							</select>
						</div>
						<div style="display: flex; gap: 8px;">
							<button class="btn" style="flex: 1; background: #007bff; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'no', 'no')"><i class="fa fa-print"></i> Default</button>
							<button class="btn" style="flex: 1; background: #dc3545; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'yes', 'no')"><i class="fa fa-qrcode"></i> QR</button>
							<button class="btn" style="flex: 1; background: #28a745; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'no', 'yes')"><i class="fa fa-print"></i> Small</button>
						</div>
					</div>
				</div>
				
				<!-- Print Semua (History) -->
				<div class="col-6">
					<div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
						<label style="font-weight: 600; font-size: 14px; color: #17a2b8; display: block; margin-bottom: 10px;"><i class="fa fa-history"></i> Print by Comment (Semua)</label>
						<div style="display: flex; gap: 8px; margin-bottom: 12px;">
							<select id="qpCommentSemua" class="form-control" style="flex: 1; border-radius: 6px; padding: 8px 12px;">
								<option value="">Pilih Comment...</option>
							</select>
							<button class="btn bg-secondary" onclick="loadCommentsForPrint()" title="Refresh Comments" style="border-radius: 6px;"><i class="fa fa-refresh"></i></button>
						</div>
						<div style="display: flex; gap: 8px;">
							<button class="btn" style="flex: 1; background: #007bff; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'no', 'no')"><i class="fa fa-print"></i> Default</button>
							<button class="btn" style="flex: 1; background: #dc3545; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'yes', 'no')"><i class="fa fa-qrcode"></i> QR</button>
							<button class="btn" style="flex: 1; background: #28a745; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'no', 'yes')"><i class="fa fa-print"></i> Small</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>

<script>
function loadCommentsForPrint() {
  var select = document.getElementById('qpCommentSemua');
  select.innerHTML = '<option value="">Loading...</option>';
  
  var xhr = new XMLHttpRequest();
  xhr.open('GET', './process/getcomments.php?session=<?= $session; ?>', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        var resp = JSON.parse(xhr.responseText);
        select.innerHTML = '<option value="">Pilih Comment...</option>';
        var currentGen = '<?= $urlprint ?>';
        
        for (var j = 0; j < resp.length; j++) {
          var item = resp[j];
          var opt = document.createElement('option');
          opt.value = item.comment;
          opt.textContent = item.comment + ' [' + item.count + ' user]';
          if (item.comment === currentGen) {
            opt.selected = true;
          }
          select.appendChild(opt);
        }
      } catch (e) {
        select.innerHTML = '<option value="">Gagal memuat</option>';
      }
    }
  };
  xhr.send();
}

function doQuickPrint(by, qr, small) {
  var val = '';
  var url = '';
  
  if (by === 'baru') {
    val = document.getElementById('qpCommentBaru').value;
    if (!val) {
      alert('Belum ada generate baru!');
      return;
    }
    url = "./voucher/print.php?id=" + encodeURIComponent(val) + "&qr=" + qr + "&small=" + small + "&session=<?= $session; ?>";
  } else if (by === 'semua') {
    val = document.getElementById('qpCommentSemua').value;
    if (!val) {
      alert('Pilih comment terlebih dahulu!');
      return;
    }
    url = "./voucher/print.php?id=" + encodeURIComponent(val) + "&qr=" + qr + "&small=" + small + "&session=<?= $session; ?>";
  }
  
  var win = window.open(url, '_blank');
  win.focus();
}

// Auto load history comments on page load
$(document).ready(function() {
  loadCommentsForPrint();
});
</script>
<?php 
}
?>