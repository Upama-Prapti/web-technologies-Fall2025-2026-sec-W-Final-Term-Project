<?php
if(!isset($message)) $message = [];
if(!isset($user)) $user = ['name' => '', 'email' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="form-container">
   <form action="index.php?route=update" method="post">
      <h3>update profile</h3>
      
      <?php
      if(!empty($message)){
         foreach($message as $msg){
            $bg_color = (strpos($msg, 'successfully') !== false) ? '#28a745' : '#dc3545';
            echo '<div class="message" style="background: '.$bg_color.'; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
               <span>'.$msg.'</span>
            </div>';
         }
      }
      ?>
      
      <input type="text" name="name" placeholder="<?= $user['name']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $user['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="new password (min 8 characters)" class="box" maxlength="50" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
