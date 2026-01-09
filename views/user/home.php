<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<div class="home-layout">
   <aside class="home-sidebar">
      <div class="sidebar-box">
         <?php if($fetch_profile){ ?>
         <div class="sidebar-profile">
            <div class="profile-info">
               <i class="fas fa-user-circle"></i>
               <div>
                  <p class="profile-name"><?= $fetch_profile['name']; ?></p>
                  <p class="profile-stats">Comments: <?= $total_user_comments; ?> | Likes: <?= $total_user_likes; ?></p>
               </div>
            </div>
            <a href="index.php?route=create_post" class="sidebar-btn"><i class="fas fa-plus"></i> Create Post</a>
            <a href="index.php?route=update" class="sidebar-btn"><i class="fas fa-user-edit"></i> Update Profile</a>
            <div class="sidebar-links">
               <a href="index.php?route=likes" class="sidebar-link"><i class="fas fa-heart"></i> Likes</a>
               <a href="index.php?route=comments" class="sidebar-link"><i class="fas fa-comments"></i> Comments</a>
            </div>
         </div>
         <?php } else { ?>
         <div class="sidebar-login">
            <div class="sidebar-btn-group">
               <a href="index.php?route=login" class="sidebar-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
               <a href="index.php?route=register" class="sidebar-btn"><i class="fas fa-user-plus"></i> Register</a>
            </div>
         </div>
         <?php } ?>
      </div>

      <div class="sidebar-box">
         <div class="sidebar-title">
            <i class="fas fa-th"></i>
            <span>Categories</span>
         </div>
         <div class="sidebar-content">
            <a href="index.php?route=category&category=nature" class="sidebar-item"><i class="fas fa-leaf"></i> Nature</a>
            <a href="index.php?route=category&category=education" class="sidebar-item"><i class="fas fa-graduation-cap"></i> Education</a>
            <a href="index.php?route=category&category=business" class="sidebar-item"><i class="fas fa-briefcase"></i> Business</a>
            <a href="index.php?route=category&category=travel" class="sidebar-item"><i class="fas fa-plane"></i> Travel</a>
            <a href="index.php?route=category&category=news" class="sidebar-item"><i class="fas fa-newspaper"></i> News</a>
            <a href="index.php?route=category&category=gaming" class="sidebar-item"><i class="fas fa-gamepad"></i> Gaming</a>
            <a href="index.php?route=category&category=sports" class="sidebar-item"><i class="fas fa-football-ball"></i> Sports</a>
            <a href="index.php?route=category&category=design" class="sidebar-item"><i class="fas fa-palette"></i> Design</a>
            <a href="index.php?route=category&category=fashion" class="sidebar-item"><i class="fas fa-tshirt"></i> Fashion</a>
            <a href="index.php?route=category&category=personal" class="sidebar-item"><i class="fas fa-user"></i> Personal</a>
            <a href="index.php?route=categories" class="sidebar-item view-all"><i class="fas fa-arrow-right"></i> View All</a>
         </div>
      </div>

      <div class="sidebar-box">
         <div class="sidebar-title">
            <i class="fas fa-users"></i>
            <span>Authors</span>
         </div>
         <div class="sidebar-content">
            <?php
               if(!empty($authors)){
                  foreach($authors as $author_name) { 
            ?>
            <a href="index.php?route=author&author=<?= $author_name; ?>" class="sidebar-item"><i class="fas fa-user"></i> <?= $author_name; ?></a>
            <?php } ?>
            <a href="index.php?route=authors" class="sidebar-item view-all"><i class="fas fa-arrow-right"></i> View All</a>
            <?php } else { ?>
            <p class="sidebar-empty">no posts added yet!</p>
            <?php } ?>  
         </div>
      </div>
   </aside>

   <main class="home-main">
      <div class="main-header">
         <h1 class="main-title">Today Trending</h1>
      </div>
      
      <div class="posts-grid">
      <?php
         if(!empty($posts)){
            foreach($posts as $fetch_posts){
               $post_id = $fetch_posts['id'];
      ?>
      <form class="box" method="post">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
         <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
               <a href="index.php?route=author&author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
               <div><?= $fetch_posts['date']; ?></div>
            </div>
         </div>
         
         <?php if($fetch_posts['image'] != ''){ ?>  
         <img src="<?php echo BASE_URL . UPLOADED_IMG_URL . $fetch_posts['image']; ?>" class="post-image" alt="">
         <?php } ?>
         
         <div class="post-title"><?= $fetch_posts['title']; ?></div>
         <div class="post-content content-150"><?= substr($fetch_posts['content'], 0, 150); ?>...</div>
         <a href="index.php?route=post&post_id=<?= $post_id; ?>" class="inline-btn">read more</a>
         <a href="index.php?route=category&category=<?= $fetch_posts['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
         <div class="icons">
            <a href="index.php?route=post&post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $fetch_posts['comments_count']; ?>)</span></a>
            <button type="button" onclick="likePost(this, <?= $post_id; ?>, <?= $fetch_posts['admin_id']; ?>)"><i class="fas fa-heart" style="<?php if($fetch_posts['has_liked']){ echo 'color:var(--red);'; } ?>"></i><span>(<?= $fetch_posts['likes_count']; ?>)</span></button>
         </div>
      </form>
         <?php
            }
         } else {
            echo '<p class="empty">no posts added yet!</p>';
         }
         ?>
      </div>
      
      <div class="more-btn" style="text-align: center; margin-top:2rem;">
         <a href="index.php?route=posts" class="inline-btn">view all posts</a>
      </div>
   </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
