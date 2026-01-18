<?php
if(!isset($posts)) $posts = [];
if(!isset($user_id)) $user_id = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Posts</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="posts-container">
   <h1 class="heading">latest posts</h1>
   <div class="box-container">
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
         }else{
            echo '<p class="empty">no posts added yet!</p>';
         }
      ?>
   </div>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
