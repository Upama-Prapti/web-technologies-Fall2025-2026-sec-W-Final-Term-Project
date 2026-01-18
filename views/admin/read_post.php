<?php
if(!isset($message)) $message = [];
if(!isset($post)) {
    echo '<p class="empty">no posts found!</p>';
    exit;
}
if(!isset($comments)) $comments = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Read Post</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="read-post">
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
      <div class="status" style="background-color:<?php if($post['status'] == 'active'){echo 'limegreen'; }elseif($post['status'] == 'pending'){echo 'orange';}else{echo 'coral';}; ?>;"><?= $post['status']; ?></div>
      <?php if($post['image'] != ''){ ?>
         <img src="<?php echo BASE_URL . UPLOADED_IMG_URL . $post['image']; ?>" class="image" alt="">
      <?php } ?>
      <div class="title"><?= $post['title']; ?></div>
      <div class="content"><?= $post['content']; ?></div>
      <div class="icons">
         <div class="likes"><span style="color: var(--red); font-weight: bold;">❤</span><span><?= $post['likes_count']; ?></span></div>
         <div class="comments"><span style="color: var(--main-color); font-weight: bold;">💬</span><span><?= $post['comments_count']; ?></span></div>
      </div>
      <div class="flex-btn">
         <a href="index.php?route=admin&action=edit_post&id=<?= $post['id']; ?>" class="inline-option-btn">edit</a>
         <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('delete this post?');">delete</button>
         <a href="index.php?route=admin&action=view_posts" class="inline-option-btn">go back</a>
      </div>
   </form>
</section>

<section class="comments" style="padding-top: 0;">
   <p class="comment-title">post comments</p>
   <div class="box-container">
   <?php
      if(!empty($comments)){
         foreach($comments as $fetch_comments){
   ?>
   <div class="box">
      <div class="user">
         <span style="font-weight: bold;">👤</span>
         <div class="user-info">
            <span><?= $fetch_comments['user_name']; ?></span>
            <div><?= $fetch_comments['date']; ?></div>
         </div>
      </div>
      <div class="text"><?= $fetch_comments['comment']; ?></div>
      <form action="" method="POST">
         <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
         <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
   ?>
   </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
