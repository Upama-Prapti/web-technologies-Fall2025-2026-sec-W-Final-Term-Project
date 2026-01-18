<?php
if(!isset($message)) $message = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="form-container">
   <form action="index.php?route=register" method="post">
      <h3>register now</h3>
      
      <?php
      if(!empty($message)){
         foreach($message as $msg){
            echo '<div class="message" style="background: #dc3545; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
               <span>'.$msg.'</span>
            </div>';
         }
      }
      ?>
      
      <input type="text" name="name" required placeholder="enter your username" class="box" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="email" name="email" required placeholder="enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="enter your password (min 8 characters)" class="box" maxlength="50" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="confirm your password" class="box" maxlength="50" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
      <p>already have an account? <a href="index.php?route=login">login now</a></p>
   </form>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
