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
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body style="padding-left: 0 !important;">

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <span style="cursor: pointer; font-weight: bold; font-size: 1.5rem;" onclick="this.parentElement.remove();">×</span>
      </div>
      ';
   }
}
?>

<section class="form-container">
   <form action="index.php?route=admin&action=login" method="POST" id="adminLoginForm">
      <h3>ADMIN LOGIN</h3>
      
      
      <input type="hidden" name="login_type" id="login_type" value="admin">
      
      <label>Username</label>
      <input type="text" name="name" id="admin_name" required placeholder="Enter your username" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <label>Password</label>
      <input type="password" name="pass" id="admin_pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <input type="submit" value="Login" name="submit" class="btn">
      
      <div style="text-align: center; margin-top: 1.5rem;">
         <a href="index.php?route=login" class="option-btn" style="display: inline-block; margin-right: 1rem;">
            User Login
         </a>
         <a href="index.php?route=home" class="option-btn" style="display: inline-block;">
            Back to Home
         </a>
      </div>
   </form>
</section>

</body>
</html>
