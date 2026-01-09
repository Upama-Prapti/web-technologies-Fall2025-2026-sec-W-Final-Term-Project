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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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
         <button type="button" class="tab-btn" data-tab="admin">Admin Login</button>
      </div>
      
      <input type="hidden" name="login_type" id="login_type" value="user">
      
      <div id="user-login" class="tab-content active">
         <label>Username</label>
         <input type="text" name="name" id="user_name" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <label>Password</label>
         <input type="password" name="user_pass" id="user_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      
      <div id="admin-login" class="tab-content" style="display: none;">
         <label>Username</label>
         <input type="text" name="admin_name" id="admin_name" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <label>Password</label>
         <input type="password" name="admin_pass" id="admin_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      </div>
      
      <input type="submit" value="Login" name="submit" class="btn">
      <p>Don't have an account? <a href="index.php?route=register">Sign Up</a></p>
      <p style="margin-top: 1rem;"><a href="index.php?route=admin&action=login" style="color: var(--main-color);"><i class="fas fa-user-shield"></i> Go to Admin Panel</a></p>
   </form>
</section>

<script>
function switchTab(tab) {
   document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
   document.querySelectorAll('.tab-content').forEach(c => {
      c.classList.remove('active');
      c.style.display = 'none';
   });
   
   document.querySelector('[data-tab="' + tab + '"]').classList.add('active');
   var activeTab = document.getElementById(tab + '-login');
   activeTab.classList.add('active');
   activeTab.style.display = 'block';
   document.getElementById('login_type').value = tab;
   
   if(tab === 'user') {
      document.getElementById('user_name').required = true;
      document.getElementById('user_pass').required = true;
      document.getElementById('admin_name').required = false;
      document.getElementById('admin_pass').required = false;
   } else {
      document.getElementById('user_name').required = false;
      document.getElementById('user_pass').required = false;
      document.getElementById('admin_name').required = true;
      document.getElementById('admin_pass').required = true;
   }
}

document.addEventListener('DOMContentLoaded', function() {
   switchTab('user');
   
   document.querySelectorAll('.tab-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
         var tab = this.getAttribute('data-tab');
         switchTab(tab);
      });
   });
   
   document.getElementById('loginForm').addEventListener('submit', function(e) {
      var loginType = document.getElementById('login_type').value;
      
      if(loginType === 'user') {
         var userPass = document.getElementById('user_pass').value;
         var userName = document.getElementById('user_name').value;
         
         var passInput = document.createElement('input');
         passInput.type = 'hidden';
         passInput.name = 'pass';
         passInput.value = userPass;
         this.appendChild(passInput);
         
         document.getElementById('admin_name').removeAttribute('name');
         document.getElementById('admin_pass').removeAttribute('name');
      } else {
         var adminPass = document.getElementById('admin_pass').value;
         var adminName = document.getElementById('admin_name').value;
         
         var passInput = document.createElement('input');
         passInput.type = 'hidden';
         passInput.name = 'pass';
         passInput.value = adminPass;
         this.appendChild(passInput);
         
         var nameInput = document.createElement('input');
         nameInput.type = 'hidden';
         nameInput.name = 'name';
         nameInput.value = adminName;
         this.appendChild(nameInput);
         
         document.getElementById('user_name').removeAttribute('name');
         document.getElementById('user_pass').removeAttribute('name');
      }
   });
});
</script>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
