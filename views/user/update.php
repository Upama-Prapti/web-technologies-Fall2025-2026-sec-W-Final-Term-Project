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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="form-container">
   <form action="index.php?route=update" method="post">
      <h3>update profile</h3>
      <input type="text" name="name" placeholder="<?= $user['name']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $user['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>

