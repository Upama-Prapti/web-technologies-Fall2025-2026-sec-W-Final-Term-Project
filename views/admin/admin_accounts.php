<?php
if(!isset($message)) $message = [];
if(!isset($admins)) $admins = [];
$current_admin_id = $_SESSION['admin_id'] ?? 0;
$current_admin_name = $_SESSION['admin_name'] ?? '';
$is_superadmin = ($current_admin_name == SUPERADMIN_USERNAME);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Accounts</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">Admins Account</h1>
   
   <?php
   if(!empty($message)){
      foreach($message as $msg){
         $bg_color = (strpos($msg, 'successfully') !== false || strpos($msg, 'registered') !== false) ? '#28a745' : '#dc3545';
         echo '<div style="background: '.$bg_color.'; color: white; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center; font-size: 1.4rem; max-width: 50rem; margin-left: auto; margin-right: auto;">
            <span>'.$msg.'</span>
         </div>';
      }
   }
   ?>
   
   <div class="box-container">
   
   <div class="box" style="order: -2;">
      <p>register new admin</p>
      <a href="index.php?route=admin&action=register_admin" class="option-btn" style="margin-bottom: .5rem;">Register</a>
   </div>

   <?php
      if(!empty($admins)){
         foreach($admins as $fetch_accounts){
            $is_current_admin = ($fetch_accounts['id'] == $current_admin_id);
            $is_registered_admin = isset($fetch_accounts['is_registered_admin']) ? $fetch_accounts['is_registered_admin'] : true;
            $is_superadmin_account = isset($fetch_accounts['is_superadmin']) ? $fetch_accounts['is_superadmin'] : false;
   ?>
   <div class="box" style="order: <?php if($is_superadmin_account){ echo '-1'; } ?>;">
      <p> admin id : <span><?= $fetch_accounts['id']; ?></span> </p>
      <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
      <p> total posts : <span><?= $fetch_accounts['posts_count']; ?></span> </p>
      <?php if($is_superadmin_account){ ?>
      <p style="color: #ffd700; font-size: 1.2rem; margin-top: 0.5rem;">👑 Super Admin</p>
      <?php } elseif(!$is_registered_admin){ ?>
      <p style="color: #17a2b8; font-size: 1.2rem; margin-top: 0.5rem;">✓ User Admin</p>
      <?php } else { ?>
      <p style="color: #28a745; font-size: 1.2rem; margin-top: 0.5rem;">🛡️ Registered Admin</p>
      <?php } ?>
      
      <?php if($is_superadmin && !$is_superadmin_account){ ?>
      <div class="flex-btn" style="margin-top: 1rem;">
         <?php if($is_registered_admin){ ?>
            <form action="index.php?route=admin&action=admin_accounts" method="POST" style="display: inline;">
               <input type="hidden" name="admin_id" value="<?= $fetch_accounts['id']; ?>">
               <input type="hidden" name="delete_from" value="admin_table">
               <button type="submit" name="delete" onclick="return confirm('Delete this admin account? All their posts will also be deleted!');" class="delete-btn" style="margin-bottom: .5rem;">Delete</button>
            </form>
         <?php } else { ?>
            <form action="index.php?route=admin&action=admin_accounts" method="POST" style="display: inline;">
               <input type="hidden" name="user_id" value="<?= $fetch_accounts['id']; ?>">
               <input type="hidden" name="delete_from" value="users_table">
               <button type="submit" name="remove_admin" onclick="return confirm('Remove admin access from this user?');" class="delete-btn" style="margin-bottom: .5rem;">Remove Admin</button>
            </form>
         <?php } ?>
      </div>
      <?php } ?>
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
