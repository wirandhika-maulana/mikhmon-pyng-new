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

<div class="modern-login-wrapper">
  <!-- Theme Toggle Button -->
  <div style="position: absolute; top: 20px; right: 20px;">
    <button id="themeToggle" class="btn" style="background: rgba(128,128,128,0.1); color: var(--modern-text); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
      <i class="fa fa-moon-o" id="themeIcon"></i>
    </button>
  </div>

  <div class="modern-login-card">
    <div class="login-header">
      <img src="img/favicon.png" alt="MIKHMON Logo">
      <h2>MIKHMON</h2>
    </div>

    <?php if(!empty($error)): ?>
      <!-- Display error from PHP (usually contains HTML, so we just wrap or echo it) -->
      <div class="error-badge">
        <i class="fa fa-exclamation-triangle"></i>
        <span><?= strip_tags($error) ?></span>
      </div>
    <?php endif; ?>

    <form autocomplete="off" action="" method="post" onsubmit="return validateForm()">
      
      <div class="modern-input-group">
        <i class="fa fa-user"></i>
        <input class="modern-input" type="text" name="user" id="_username" placeholder="Username" required="1" autofocus>
      </div>
      
      <div class="modern-input-group">
        <i class="fa fa-lock"></i>
        <input class="modern-input" type="password" name="pass" placeholder="Password" required="1">
      </div>
      
      <button class="modern-login-btn" type="submit" name="login">
        Login <i class="fa fa-arrow-right" style="margin-left: 5px; font-size: 12px;"></i>
      </button>

      <div class="text-center" style="margin-top: 15px;">
        <span id="error-message" style="color: #f86c6b; font-size: 13px; font-weight: 500;"></span>
      </div>
    </form>
  </div>

  <div class="login-footer">
    &copy; <span id="year"></span> Designed By MarginLab. All rights reserved.
  </div>
</div>

<script>
  document.getElementById("year").innerHTML = new Date().getFullYear();

  function validateForm() {
    var username = document.getElementById('_username').value;
    // Client-side validation if needed
    return true; 
  }

  // Theme Toggle Logic for Login Page
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon = document.getElementById('themeIcon');
  const body = document.body;

  // Initialize from localStorage or default to light
  let currentTheme = localStorage.getItem('mikhmon_login_theme') || 'light';
  
  function applyTheme(theme) {
    if(theme === 'dark') {
      body.classList.remove('theme-light');
      body.classList.add('theme-dark');
      themeIcon.classList.remove('fa-moon-o');
      themeIcon.classList.add('fa-sun-o');
    } else {
      body.classList.remove('theme-dark');
      body.classList.add('theme-light');
      themeIcon.classList.remove('fa-sun-o');
      themeIcon.classList.add('fa-moon-o');
    }
  }

  // Apply initial theme
  applyTheme(currentTheme);

  themeToggle.addEventListener('click', () => {
    currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('mikhmon_login_theme', currentTheme);
    applyTheme(currentTheme);
  });

</script>

<script id="intergram" type="text/javascript" src="https://www.intergram.xyz/js/widget.js"></script>
