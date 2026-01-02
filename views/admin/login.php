<?php
if(!isset($message)) $message = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body style="padding-left: 0 !important;">

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">
   <form action="index.php?route=admin&action=login" method="POST" id="adminLoginForm">
      <h3>LOGIN</h3>
      
      <div class="login-tabs">
         <button type="button" class="tab-btn" data-tab="user">User Login</button>
         <button type="button" class="tab-btn active" data-tab="admin">Admin Login</button>
      </div>
      
      <input type="hidden" name="login_type" id="login_type" value="admin">
      
      <div id="user-login" class="tab-content" style="display: none;">
         <label>Username</label>
         <input type="text" name="email" id="user_email" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <label>Password</label>
         <input type="password" name="user_pass" id="user_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      
      <div id="admin-login" class="tab-content active">
         <label>Username</label>
         <input type="text" name="name" id="admin_name" required placeholder="Enter your username" class="box" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
         <label>Password</label>
         <input type="password" name="admin_pass" id="admin_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      
      <input type="submit" value="Login" name="submit" class="btn">
      
      <div style="text-align: center; margin-top: 1rem;">
         <a href="index.php?route=home" class="option-btn" style="display: inline-block;">
            <i class="fas fa-home"></i> Back to Home
         </a>
      </div>
   </form>
</section>

<script>
function switchTab(tab) {
   document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
   document.querySelectorAll('.tab-content').forEach(c => {
      c.classList.remove('active');
      c.style.display = 'none';
   });
   
   document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
   const activeTab = document.getElementById(tab + '-login');
   activeTab.classList.add('active');
   activeTab.style.display = 'block';
   document.getElementById('login_type').value = tab;
   
   // Update required attributes based on active tab
   if(tab === 'user') {
      document.getElementById('user_email').required = true;
      document.getElementById('user_pass').required = true;
      document.getElementById('admin_name').required = false;
      document.getElementById('admin_pass').required = false;
   } else {
      document.getElementById('user_email').required = false;
      document.getElementById('user_pass').required = false;
      document.getElementById('admin_name').required = true;
      document.getElementById('admin_pass').required = true;
   }
}

// Initialize tabs
document.addEventListener('DOMContentLoaded', function() {
   // Set initial state - admin tab active
   switchTab('admin');
   
   // Add click handlers
   document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
         const tab = this.getAttribute('data-tab');
         switchTab(tab);
      });
   });
   
   // Handle form submission
   document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
      const loginType = document.getElementById('login_type').value;
      
      if(loginType === 'user') {
         // Redirect to user login
         e.preventDefault();
         window.location.href = 'index.php?route=login';
         return;
      } else {
         // Copy admin_pass to pass for admin login
         const adminPass = document.getElementById('admin_pass').value;
         const passInput = document.createElement('input');
         passInput.type = 'hidden';
         passInput.name = 'pass';
         passInput.value = adminPass;
         this.appendChild(passInput);
         
         // Remove user fields from submission
         document.getElementById('user_email').removeAttribute('name');
         document.getElementById('user_pass').removeAttribute('name');
      }
   });
});
</script>

</body>
</html>

