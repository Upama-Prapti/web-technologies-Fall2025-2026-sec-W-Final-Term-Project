<?php
if(!isset($message)) $message = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="form-container">
   <form action="index.php?route=login" method="post" id="loginForm">
      <h3>Login</h3>
      
      <?php
      if(!empty($message)){
         foreach($message as $msg){
            echo '<div class="message" style="background: #dc3545; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
               <span>'.$msg.'</span>
            </div>';
         }
      }
      ?>
      
      <div class="login-tabs">
         <button type="button" class="tab-btn active" data-tab="user">User Login</button>
         <a href="index.php?route=admin&action=login" class="tab-btn" style="text-decoration: none; display: inline-block;">Admin Login</a>
      </div>
      
      <input type="hidden" name="login_type" id="login_type" value="user">
      
      <div id="user-login" class="tab-content active">
         <label>Username</label>
         <input type="text" name="name" id="user_name" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <label>Password</label>
         <input type="password" name="user_pass" id="user_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      
      <div id="admin-login" class="tab-content" style="display: none;">
         <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
            <p style="color: #856404; font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem;">⚠️ Admin login has been moved to a dedicated panel</p>
            <a href="index.php?route=admin&action=login" class="btn" style="margin-top: 0.5rem; display: inline-block; padding: 0.8rem 2rem;">Go to Admin Login Panel</a>
         </div>
      </div>
      
      <input type="submit" value="Login" name="submit" class="btn">
      <p>Don't have an account? <a href="index.php?route=register">Sign Up</a></p>
      <p style="margin-top: 1rem;"><a href="index.php?route=admin&action=login" style="color: var(--main-color); font-weight: bold;">Go to Admin Login Panel</a></p>
   </form>
</section>

<script>
function switchTab(tab) {
   document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
   document.querySelectorAll('.tab-content').forEach(c => {
      c.classList.remove('active');
      c.style.display = 'none';
   });
   
   var tabBtn = document.querySelector('[data-tab="' + tab + '"]');
   if(tabBtn) {
      tabBtn.classList.add('active');
   }
   
   var activeTab = document.getElementById(tab + '-login');
   if(activeTab) {
      activeTab.classList.add('active');
      activeTab.style.display = 'block';
   }
   
   var loginTypeInput = document.getElementById('login_type');
   if(loginTypeInput) {
      loginTypeInput.value = tab;
   }
   
   // Only set required attributes if elements exist
   var userName = document.getElementById('user_name');
   var userPass = document.getElementById('user_pass');
   var adminName = document.getElementById('admin_name');
   var adminPass = document.getElementById('admin_pass');
   
   if(tab === 'user') {
      if(userName) userName.required = true;
      if(userPass) userPass.required = true;
      if(adminName) adminName.required = false;
      if(adminPass) adminPass.required = false;
   } else {
      if(userName) userName.required = false;
      if(userPass) userPass.required = false;
      if(adminName) adminName.required = true;
      if(adminPass) adminPass.required = true;
   }
}

document.addEventListener('DOMContentLoaded', function() {
   switchTab('user');
   
   document.querySelectorAll('.tab-btn[data-tab]').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
         var tab = this.getAttribute('data-tab');
         if(tab) {
            switchTab(tab);
         }
      });
   });
   
   var loginForm = document.getElementById('loginForm');
   if(loginForm) {
      loginForm.addEventListener('submit', function(e) {
         var loginTypeInput = document.getElementById('login_type');
         var loginType = loginTypeInput ? loginTypeInput.value : 'user';
         
         if(loginType === 'user') {
            var userPass = document.getElementById('user_pass');
            var userName = document.getElementById('user_name');
            
            if(userPass && userName) {
               var passInput = document.createElement('input');
               passInput.type = 'hidden';
               passInput.name = 'pass';
               passInput.value = userPass.value;
               this.appendChild(passInput);
            }
            
            var adminName = document.getElementById('admin_name');
            var adminPass = document.getElementById('admin_pass');
            if(adminName) adminName.removeAttribute('name');
            if(adminPass) adminPass.removeAttribute('name');
         } else {
            // Admin login - redirect handled by link, but just in case
            var adminPass = document.getElementById('admin_pass');
            var adminName = document.getElementById('admin_name');
            
            if(adminPass && adminName) {
               var passInput = document.createElement('input');
               passInput.type = 'hidden';
               passInput.name = 'pass';
               passInput.value = adminPass.value;
               this.appendChild(passInput);
               
               var nameInput = document.createElement('input');
               nameInput.type = 'hidden';
               nameInput.name = 'name';
               nameInput.value = adminName.value;
               this.appendChild(nameInput);
            }
            
            var userName = document.getElementById('user_name');
            var userPass = document.getElementById('user_pass');
            if(userName) userName.removeAttribute('name');
            if(userPass) userPass.removeAttribute('name');
         }
      });
   }
});
</script>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
