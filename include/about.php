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
.modern-about-card {
    background: var(--modern-card-bg);
    border: 1px solid var(--modern-border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 8px 32px var(--modern-shadow);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    margin-bottom: 24px;
    color: var(--modern-text);
    transition: all 0.3s ease;
}

.modern-about-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px var(--modern-shadow);
}

.modern-about-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--modern-border);
}

.modern-about-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    background: linear-gradient(135deg, #20a8d8, #6f42c1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modern-about-header h3 i {
    -webkit-text-fill-color: #20a8d8;
}

.modern-about-content {
    line-height: 1.6;
}

.modern-about-content h4 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--modern-text);
}

.modern-about-list {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.modern-about-list li {
    padding: 8px 0;
    border-bottom: 1px dashed rgba(128,128,128,0.2);
    display: flex;
    align-items: center;
}

.modern-about-list li:last-child {
    border-bottom: none;
}

.modern-about-list li strong {
    min-width: 100px;
    color: #20a8d8;
}

.modern-about-list a {
    color: var(--modern-text);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
}

.modern-about-list a:hover {
    color: #6f42c1;
}

.modern-changelog-item {
    background: rgba(128,128,128,0.05);
    border-left: 4px solid #6f42c1;
    padding: 15px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 20px;
}

.modern-changelog-date {
    font-size: 12px;
    color: #888;
    margin-bottom: 8px;
    font-weight: 600;
}

.modern-changelog-item ul {
    margin: 0;
    padding-left: 20px;
}

.modern-changelog-item li {
    margin-bottom: 5px;
    font-size: 14px;
}

.modern-footer {
    margin-top: 30px;
    padding-top: 20px;
    text-align: center;
    font-size: 13px;
    color: #888;
    border-top: 1px solid var(--modern-border);
}

.iFWrapper {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 25px;
    height: 0;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--modern-border);
}
.iFWrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}
</style>

<div class="row">
  <div class="col-12">
    <div class="modern-about-card">
      <div class="modern-about-header">
        <h3><i class="fa fa-info-circle"></i> About MIKHMON</h3>
      </div>
      <div class="modern-about-content">
        <h4>MIKHMON V<?= $_SESSION['v']; ?> (Modernized)</h4>
        <p>Aplikasi ini dipersembahkan untuk pengusaha hotspot di manapun Anda berada. Semoga makin sukses dengan tampilan yang lebih modern, responsif, dan futuristik.</p>
        
        <ul class="modern-about-list">
          <li><strong>Author</strong> <span>Laksamadi Guko</span></li>
          <li><strong>Mod Design</strong> <span>Margin.Lab & AI Agent</span></li>
          <li><strong>Licence</strong> <span><a href="https://github.com/laksa19/mikhmonv2/blob/master/LICENSE" target="_blank">GPLv2</a></span></li>
          <li><strong>Website</strong> <span><a href="https://laksa19.github.io" target="_blank">laksa19.github.io</a></span></li>
        </ul>
        
        <div class="modern-footer">
          <div><i>Copyright &copy; 2018 Laksamadi Guko</i></div>
          <div><i>Modern Glassmorphism Design by Antigravity</i></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-12">
    <div class="modern-about-card">
      <div class="modern-about-header">
        <h3><i class="fa fa-history"></i> Changelog</h3>
      </div>
      <div class="modern-about-content">
        
        <div class="modern-changelog-item">
          <div class="modern-changelog-date">Version 3.0 (Modern Redesign) - June 2026</div>
          <ul>
            <li><strong>Added</strong>: Full Modern Glassmorphism UI integration globally.</li>
            <li><strong>Added</strong>: Theme switcher toggle button on the Login page (Light/Dark support).</li>
            <li><strong>Added</strong>: Redesigned About & Changelog page.</li>
            <li><strong>Fixed</strong>: Visibility of the MIKHMON logo/brand in the Light Theme.</li>
            <li><strong>Improved</strong>: Responsive Dashboard layout and Report Tables redesign.</li>
            <li><strong>Improved</strong>: Card styling, input fields, and pill-shaped buttons with hover effects.</li>
          </ul>
        </div>

        <div class="modern-changelog-item" style="opacity: 0.8; border-left-color: #888;">
          <div class="modern-changelog-date">Version 2.0 (Legacy)</div>
          <ul>
            <li><strong>Initial Release</strong>: Original Mikhmon interface by Laksamadi Guko.</li>
            <li><strong>Features</strong>: Hotspot User Generation, Session Management, Quick Print.</li>
          </ul>
        </div>
        
      </div>
    </div>
  </div>
</div>
