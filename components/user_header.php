<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="index.php?route=home" class="logo">upos_blog</a>

      <form action="index.php?route=search" method="POST" class="search-form">
         <input type="text" name="search_box" class="box" maxlength="100" placeholder="search for blogs" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <nav class="navbar">
         <a href="index.php?route=home"> <i class="fas fa-angle-right"></i> home</a>
         <a href="index.php?route=posts"> <i class="fas fa-angle-right"></i> posts</a>
         <a href="index.php?route=categories"> <i class="fas fa-angle-right"></i> category</a>
         <a href="index.php?route=authors"> <i class="fas fa-angle-right"></i> authors</a>
         <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != ''){ ?>
         <a href="index.php?route=create_post"> <i class="fas fa-angle-right"></i> create post</a>
         <?php } else { ?>
         <a href="index.php?route=login"> <i class="fas fa-angle-right"></i> login</a>
         <a href="index.php?route=register"> <i class="fas fa-angle-right"></i> register</a>
         <?php } ?>
      </nav>

      <div class="profile">
         <?php
            global $conn;
            $user_id = $_SESSION['user_id'] ?? '';
            if($user_id != '' && isset($conn) && $conn){
               $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $stmt->bind_param("i", $user_id);
               $stmt->execute();
               $result = $stmt->get_result();
               if($result->num_rows > 0){
                  $fetch_profile = $result->fetch_assoc();
         ?>
         <p class="name"><?= $fetch_profile['name']; ?></p>
         <a href="index.php?route=update" class="btn">update profile</a>
         <a href="index.php?route=logout" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php
               }else{
         ?>
            <p class="name">please login first!</p>
            <a href="index.php?route=login" class="option-btn">login</a>
         <?php
               }
            }else{
         ?>
            <p class="name">please login first!</p>
            <a href="index.php?route=login" class="option-btn">login</a>
         <?php
            }
         ?>
      </div>

   </section>

</header>