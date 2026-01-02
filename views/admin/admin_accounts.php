<?php
if(!isset($message)) $message = [];
if(!isset($admins)) $admins = [];
if(!isset($admin_id)) $admin_id = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Accounts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">admins account</h1>
   <div class="box-container">
   <div class="box" style="order: -2;">
      <p>register new admin</p>
      <a href="index.php?route=admin&action=register_admin" class="option-btn" style="margin-bottom: .5rem;">register</a>
   </div>

   <?php
      if(!empty($admins)){
         foreach($admins as $fetch_accounts){
   ?>
   <div class="box" style="order: <?php if($fetch_accounts['id'] == $admin_id){ echo '-1'; } ?>;">
      <p> admin id : <span><?= $fetch_accounts['id']; ?></span> </p>
      <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
      <p> total posts : <span><?= $fetch_accounts['posts_count']; ?></span> </p>
      <div class="flex-btn">
         <?php
            if($fetch_accounts['id'] == $admin_id){
         ?>
            <a href="index.php?route=admin&action=update_profile" class="option-btn" style="margin-bottom: .5rem;">update</a>
            <form action="index.php?route=admin&action=admin_accounts" method="POST">
               <input type="hidden" name="post_id" value="<?= $fetch_accounts['id']; ?>">
               <button type="submit" name="delete" onclick="return confirm('delete the account?');" class="delete-btn" style="margin-bottom: .5rem;">delete</button>
            </form>
         <?php
            }
         ?>
      </div>
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

