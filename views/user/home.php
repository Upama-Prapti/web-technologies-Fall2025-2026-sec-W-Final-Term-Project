<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>
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
               <span style="font-size: 3rem;">👤</span>
               <div>
                  <p class="profile-name"><?= $fetch_profile['name']; ?></p>
                  <p class="profile-stats">Comments: <?= $total_user_comments; ?> | Likes: <?= $total_user_likes; ?></p>
               </div>
            </div>
            <a href="index.php?route=create_post" class="sidebar-btn">+ Create Post</a>
            <a href="index.php?route=update" class="sidebar-btn">✏️ Update Profile</a>
            <div class="sidebar-links">
               <a href="index.php?route=likes" class="sidebar-link">❤ Likes</a>
               <a href="index.php?route=comments" class="sidebar-link">💬 Comments</a>
            </div>
         </div>
         <?php } else { ?>
         <div class="sidebar-login">
            <div class="sidebar-btn-group">
               <a href="index.php?route=login" class="sidebar-btn">Login</a>
               <a href="index.php?route=register" class="sidebar-btn">Register</a>
            </div>
         </div>
         <?php } ?>
      </div>

      <div class="sidebar-box">
         <div class="sidebar-title">
            <span>Categories</span>
         </div>
         <div class="sidebar-content">
            <a href="index.php?route=category&category=nature" class="sidebar-item">Nature</a>
            <a href="index.php?route=category&category=education" class="sidebar-item">Education</a>
            <a href="index.php?route=category&category=business" class="sidebar-item">Business</a>
            <a href="index.php?route=category&category=travel" class="sidebar-item">Travel</a>
            <a href="index.php?route=category&category=news" class="sidebar-item">News</a>
            <a href="index.php?route=category&category=gaming" class="sidebar-item">Gaming</a>
            <a href="index.php?route=category&category=sports" class="sidebar-item">Sports</a>
            <a href="index.php?route=category&category=design" class="sidebar-item">Design</a>
            <a href="index.php?route=category&category=fashion" class="sidebar-item">Fashion</a>
            <a href="index.php?route=category&category=personal" class="sidebar-item">Personal</a>
            <a href="index.php?route=categories" class="sidebar-item view-all">> View All</a>
         </div>
      </div>

      <div class="sidebar-box">
         <div class="sidebar-title">
            <span>Authors</span>
         </div>
         <div class="sidebar-content">
            <?php
               if(!empty($authors)){
                  foreach($authors as $author_name) { 
            ?>
            <a href="index.php?route=author&author=<?= $author_name; ?>" class="sidebar-item"><?= $author_name; ?></a>
            <?php } ?>
            <a href="index.php?route=authors" class="sidebar-item view-all">> View All</a>
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
            <span style="font-weight: bold;">👤</span>
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
         <a href="index.php?route=category&category=<?= $fetch_posts['category']; ?>" class="post-cat"> <span><?= $fetch_posts['category']; ?></span></a>
         <div class="icons">
            <a href="index.php?route=post&post_id=<?= $post_id; ?>"><span style="font-weight: bold;">💬</span><span>(<?= $fetch_posts['comments_count']; ?>)</span></a>
            <button type="button" onclick="likePost(this, <?= $post_id; ?>, <?= $fetch_posts['admin_id']; ?>)"><span style="font-weight: bold; <?php if($fetch_posts['has_liked']){ echo 'color:var(--red);'; } ?>">❤</span><span>(<?= $fetch_posts['likes_count']; ?>)</span></button>
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
