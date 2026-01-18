<?php
if(!isset($message)) $message = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Admin</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="form-container">
   <form action="index.php?route=admin&action=register_admin" method="POST">
      <h3>register new admin</h3>
      
      <?php
      if(!empty($message)){
         foreach($message as $msg){
            $bg_color = (strpos($msg, 'registered') !== false) ? '#28a745' : '#dc3545';
            echo '<div class="message" style="background: '.$bg_color.'; color: white; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center; font-size: 1.4rem;">
               <span>'.$msg.'</span>
            </div>';
         }
      }
      ?>
      
      <input type="text" name="name" maxlength="50" required placeholder="enter username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="50" required placeholder="enter password (min 8 characters)" class="box" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" maxlength="50" required placeholder="confirm password" class="box" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
   </form>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
