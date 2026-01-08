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
$is_superadmin = ($_SESSION['is_superadmin'] ?? 0) == 1;
?>

<header class="header">

   <a href="index.php?route=admin&action=dashboard" class="logo">upos_blog</a>

   <div class="profile">
      <?php
         global $conn;
         $admin_id = $_SESSION['admin_id'] ?? '';
         
         if($admin_id != '' && isset($conn) && $conn){
            $stmt = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $header_profile = $result->fetch_assoc();
            
            if(!$header_profile){
               $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ? AND is_admin = 1");
               $stmt->bind_param("i", $admin_id);
               $stmt->execute();
               $result = $stmt->get_result();
               $header_profile = $result->fetch_assoc();
            }
            
            if($header_profile){
      ?>
      <p><?= $header_profile['name']; ?></p>
      <a href="index.php?route=admin&action=update_profile" class="btn">update profile</a>
      <?php
            }else{
      ?>
      <p>Admin not found</p>
      <?php
            }
         }else{
      ?>
      <p>Please login</p>
      <?php
         }
      ?>
   </div>

   <nav class="navbar">
      <a href="index.php?route=admin&action=dashboard"><i class="fas fa-home"></i> <span>home</span></a>
      <a href="index.php?route=admin&action=add_post"><i class="fas fa-pen"></i> <span>add posts</span></a>
      <a href="index.php?route=admin&action=view_posts"><i class="fas fa-eye"></i> <span>view posts</span></a>
      <?php if($is_superadmin){ ?>
      <a href="index.php?route=admin&action=admin_accounts"><i class="fas fa-user"></i> <span>accounts</span></a>
      <?php } ?>
      <?php if($admin_id != ''){ ?>
      <a href="index.php?route=admin&action=logout" style="color:var(--red);" onclick="return confirm('logout from the website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
      <?php } ?>
   </nav>

   <?php if($admin_id == ''){ ?>
   <div class="flex-btn">
      <a href="index.php?route=admin&action=login" class="option-btn">login</a>
   </div>
   <?php } ?>

</header>

<div id="menu-btn" class="fas fa-bars"></div>
