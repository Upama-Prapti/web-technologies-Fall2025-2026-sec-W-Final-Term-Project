<?php
if(!isset($message)) $message = [];
if(!isset($fetch_profile)) $fetch_profile = ['name' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="form-container">
   <form action="index.php?route=admin&action=update_profile" method="POST">
      <h3>update profile</h3>
      
      <?php
      if(!empty($message)){
         foreach($message as $msg){
            $bg_color = (strpos($msg, 'successfully') !== false) ? '#28a745' : '#dc3545';
            echo '<div class="message" style="background: '.$bg_color.'; color: white; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center; font-size: 1.4rem;">
               <span>'.$msg.'</span>
            </div>';
         }
      }
      ?>
      
      <p style="color: var(--light-color); margin-bottom: 1rem; font-size: 1.4rem;">Current username: <strong style="color: var(--main-color);"><?= $fetch_profile['name']; ?></strong></p>
      
      <input type="text" name="name" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="enter new username (leave empty to keep current)">
      
      <p style="color: var(--light-color); margin: 1.5rem 0 1rem; font-size: 1.4rem; border-top: 1px solid var(--light-bg); padding-top: 1.5rem;">Change Password:</p>
      
      <input type="password" name="old_pass" maxlength="50" placeholder="enter your current password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" maxlength="50" placeholder="new password (min 8 characters)" class="box" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" maxlength="50" placeholder="confirm your new password" class="box" minlength="8" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
