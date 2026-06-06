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
}
?>
<style>
:root {
    --primary-color: #2196F3;
    --primary-hover: #1976D2;
    --card-bg: #fff;
    --card-header-bg: #f9f9f9;
    --text-color: #222;
    --text-muted: #777;
    --border-color: #eee;
    --shadow-color: rgba(0,0,0,.08);
}
body.dark-theme {
    --primary-color: #2196F3;
    --primary-hover: #42A5F5;
    --card-bg: #111;
    --card-header-bg: #181818;
    --text-color: #e0e0e0;
    --text-muted: #aaa;
    --border-color: #444;
    --shadow-color: rgba(0,0,0,.2);
}
.card {
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 10px var(--shadow-color);
    margin-bottom: 24px;
    transition: all 0.3s;
    overflow: hidden;
}
.card-header {
    padding: 16px 20px;
    background-color: var(--card-header-bg);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 500;
    display: flex;
    align-items: center;
    color: var(--text-color);
}
.card-header h3 i {
    margin-right: 8px;
    color: var(--primary-color);
}
.card-body {
    padding: 20px;
    color: var(--text-color);
}
.about-section {
    text-align: left;
    margin-bottom: 20px;
}
.about-section h3 {
    margin-bottom: 15px;
    font-size: 22px;
    font-weight: 600;
    color: var(--primary-color);
}
.about-section ul {
    padding-left: 18px;
    margin-bottom: 10px;
}
.about-section ul li {
    margin-bottom: 6px;
    color: var(--text-color);
    font-size: 15px;
}
.about-section a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.2s;
}
.about-section a:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}
.about-footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
    color: var(--text-muted);
    font-size: 14px;
}
.changelog-container {
    margin-top: 20px;
}
.iFWrapper {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 25px;
    height: 0;
    margin-bottom: 20px;
}
.iFWrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 8px;
    background: var(--card-bg);
    box-shadow: 0 2px 10px var(--shadow-color);
}
@media (max-width: 768px) {
    .card-body, .card-header {
        padding: 12px;
    }
    .about-section h3 {
        font-size: 18px;
    }
}
</style>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-info-circle"></i> About</h3>
      </div>
      <div class="card-body">
        <div class="about-section">
          <h3>MIKHMON V<?= $_SESSION['v']; ?></h3>
          <p>
            Aplikasi ini dipersembahkan untuk pengusaha hotspot di manapun Anda berada.
            Semoga makin sukses.
          </p>
          <div>
            <ul>
              <li>Author : Laksamadi Guko</li>
              <li>Licence : <a href="https://github.com/laksa19/mikhmonv2/blob/master/LICENSE">GPLv2</a></li>
              <li>API Class : <a href="https://github.com/BenMenking/routeros-api">routeros-api</a></li>
              <li>Website : <a href="https://laksa19.github.io">laksa19.github.io</a></li>
              <li>Facebook : <a href="https://fb.com/laksamadi">fb.com/laksamadi</a></li>
            </ul>
          </div>
          <p>
            Terima kasih untuk semua yang telah mendukung pengembangan MIKHMON.
          </p>
        </div>
        
        <div class="about-footer">
          <div><i>Copyright &copy; 2018 Laksamadi Guko</i></div>
          <div><i>Mod Design By Margin.Lab</i></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-12 changelog-container">
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-info-circle"></i> Changelog</h3>
      </div>
      <div class="card-body">
        <div class="iFWrapper">
          <iframe src="https://laksa19.github.io/mikhmonv3"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
