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
?>

<div style="padding-top: 5%;" class="login-box">
  <div class="card">
    <div class="card-header">
      <h3><?= $_please_login ?></h3>
    </div>
    <div class="card-body">
      <div class="text-center pd-5">
        <img src="img/favicon.png" alt="MIKHMON Logo">
      </div>
      <div class="text-center">
        <span style="font-size: 24px; margin: 10px;">MIKHMON</span><br>
        <!--<b style="font-size:16px;font-weight:bolder;">Mikrotik Monitoring Assistant </b>-->
        <center>
          <form autocomplete="off" action="" method="post" onsubmit="return validateForm()">
            <!-- Call validateForm on form submission -->
            <table class="table" style="width:90%">
              <tr>
                <td class="align-middle text-center">
                  <input style="width: 100%; height: 35px; font-size: 16px;" class="form-control" type="text" name="user" id="_username" placeholder="Username" required="1" autofocus>
                </td>
              </tr>
              <tr>
                <td class="align-middle text-center">
                  <input style="width: 100%; height: 35px; font-size: 16px;" class="form-control" type="password" name="pass" placeholder="Password" required="1">
                </td>
              </tr>
              <tr>
                <td class="align-middle text-center">
                  <input style="width: 100%; margin-top:20px; height: 35px; font-weight: bold; font-size: 17px;" class="btn-login bg-primary pointer" type="submit" name="login" value="Login">
                </td>
              </tr>
              <tr>
                <td class="align-middle text-center">
                  <span id="error-message" style="color: red;"></span> <!-- Placeholder for error message -->
                </td>
              </tr>
            </table>
          </form>
          <!-- <div style="margin-top: 20px;">
            <p style="display: inline;">Don't have an account? <a href="https://mikhmon.wiran.my.id/register/index.php?id=register"><b>Sign Up</b></a></p>
            <span style="margin-left: 2px;">Or</span> 
            <p style="display: inline; margin-left: 2px;"><a href="https://mikhmon.wiran.my.id/register/index.php?id=renew"><b>Renew</b></a></p>
          </div> -->
        </center>
      </div>
    </div>
  </div>
</div>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Center Copyright</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    #copyright {
      text-align: center;
    }
  </style>
</head>
<body>
  <p id="copyright">
    &copy; <span id="year"></span> Designed By MarginLab. All rights reserved.
  </p>
  <script>
    document.getElementById("year").innerHTML = new Date().getFullYear();
  </script>
</body>
</html>

<!-- Add a scrolling effect to the footer -->
<!-- <footer style="overflow: hidden;"> 
  <marquee direction="left">Mod Designed By MarginLab, Powered By PT. MarginLab Network Solutions</marquee> 
</footer> -->

<script>
  function validateForm() {
    var username = document.getElementById('_username').value;
    var errorMessage = "<?= $error ?>"; // Get the error message from PHP

    if (errorMessage) {
      alert(errorMessage); // Display the error message in a popup
      return false; // Prevent form submission
    }

    return true; // Allow form submission if no error message
  }
</script>
	<!--<script type="text/javascript">
		// 1 detik = 1000
		window.setTimeout("waktu()",1000);  
		function waktu() {   
		var tanggal = new Date();  
		setTimeout("waktu()",1000);  
		document.getElementById("jam").innerHTML = tanggal.getHours()+":"+tanggal.getMinutes()+":"+tanggal.getSeconds();
		}

		window.intergramId = "1103412366";
		window.intergramCustomizations = {
			titleClosed: 'Chat',
			titleOpen: 'Admin Payung.Net',
			introMessage: 'Selamat datang di Support Payung.Net! Kami siap membantu Anda. Ada yang bisa kami bantu?',
			autoResponse: 'Mohon tunggu,..',
			autoNoResponse: 'Om Admin, sedang tidak ada ditempat / ' +
                        'tidak pegang HP.' + 'Silahkan hubungi via WhatsApp di 0821-1314-3826'+'Terimakasih.',
			mainColor: "green", 
			alwaysUseFloatingButton: false 
		};
	</script> -->
	<script id="intergram" type="text/javascript" src="https://www.intergram.xyz/js/widget.js"></script>
