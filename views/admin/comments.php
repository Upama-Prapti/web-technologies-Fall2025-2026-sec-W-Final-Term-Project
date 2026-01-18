<?php
if(!isset($message)) $message = [];
if(!isset($comments)) $comments = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Comments</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="comments">
   <h1 class="heading">posts comments</h1>
   <p class="comment-title">post comments</p>
   <div class="box-container">
   <?php
      if(!empty($comments)){
         foreach($comments as $fetch_comments){
            if(isset($fetch_comments['post']) && $fetch_comments['post']){
   ?>
      <div class="post-title"> from : <span><?= $fetch_comments['post']['title']; ?></span> <a href="index.php?route=admin&action=read_post&post_id=<?= $fetch_comments['post']['id']; ?>" >view post</a></div>
   <?php } ?>
   <div class="box">
      <div class="user">
         <span style="font-weight: bold;">👤</span>
         <div class="user-info">
            <span><?= $fetch_comments['user_name']; ?></span>
            <div><?= $fetch_comments['date']; ?></div>
         </div>
      </div>
      <div class="text"><?= $fetch_comments['comment']; ?></div>
      <form action="index.php?route=admin&action=comments" method="POST">
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
