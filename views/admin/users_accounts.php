<?php
if(!isset($message)) $message = [];
if(!isset($users)) $users = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users Accounts</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">users account</h1>
   <div class="box-container">
   <?php
      if(!empty($users)){
         foreach($users as $fetch_accounts){
            $user_id = $fetch_accounts['id'];
   ?>
   <div class="box">
      <p> users id : <span><?= $user_id; ?></span> </p>
      <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
      <p> email : <span><?= $fetch_accounts['email']; ?></span> </p>
      <p> admin status : <span><?= $fetch_accounts['is_admin'] == 1 ? 'Yes' : 'No'; ?></span> </p>
      <p> total comments : <span><?= $fetch_accounts['comments_count']; ?></span> </p>
      <p> total likes : <span><?= $fetch_accounts['likes_count']; ?></span> </p>
      <form action="index.php?route=admin&action=users_accounts" method="POST" style="margin-top: 1rem;">
         <input type="hidden" name="user_id" value="<?= $user_id; ?>">
         <input type="hidden" name="is_admin" value="<?= $fetch_accounts['is_admin'] == 1 ? '0' : '1'; ?>">
         <button type="submit" name="assign_admin" class="option-btn" onclick="return confirm('<?= $fetch_accounts['is_admin'] == 1 ? 'Remove' : 'Assign'; ?> admin access for this user?');">
            <?= $fetch_accounts['is_admin'] == 1 ? 'Remove Admin' : 'Assign Admin'; ?>
         </button>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no accounts available</p>';
      }
   ?>
   </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
