<?php
if(!isset($message)) $message = [];
if(!isset($show_default_credentials)) $show_default_credentials = false;
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
      <h3>ADMIN LOGIN</h3>
      
      <?php if($show_default_credentials){ ?>
      <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 1rem; margin-bottom: 2rem; text-align: center;">
         <p style="font-size: 1.4rem; margin-bottom: 0.5rem;"><i class="fas fa-info-circle"></i> First Time Login</p>
         <p style="font-size: 1.6rem; font-weight: bold;">Username: <span style="color: #ffd700;">upama</span></p>
         <p style="font-size: 1.6rem; font-weight: bold;">Password: <span style="color: #ffd700;">1234</span></p>
         <p style="font-size: 1.2rem; margin-top: 1rem; opacity: 0.9;"><i class="fas fa-exclamation-triangle"></i> Please update your password after first login!</p>
      </div>
      <?php } ?>
      
      <input type="hidden" name="login_type" id="login_type" value="admin">
      
      <label>Username</label>
      <input type="text" name="name" id="admin_name" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <label>Password</label>
      <input type="password" name="admin_pass" id="admin_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <input type="submit" value="Login" name="submit" class="btn">
      
      <div style="text-align: center; margin-top: 1.5rem;">
         <a href="index.php?route=login" class="option-btn" style="display: inline-block; margin-right: 1rem;">
            <i class="fas fa-user"></i> User Login
         </a>
         <a href="index.php?route=home" class="option-btn" style="display: inline-block;">
            <i class="fas fa-home"></i> Back to Home
         </a>
      </div>
   </form>
</section>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
   var adminPass = document.getElementById('admin_pass').value;
   var passInput = document.createElement('input');
   passInput.type = 'hidden';
   passInput.name = 'pass';
   passInput.value = adminPass;
   this.appendChild(passInput);
});
</script>

</body>
</html>
