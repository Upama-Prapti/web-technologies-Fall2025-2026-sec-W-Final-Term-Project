<?php
if(!isset($post)) {
    echo '<p class="empty">no posts found!</p>';
    exit;
}
if(!isset($message)) $message = [];
if(!isset($fetch_profile)) $fetch_profile = null;
if(!isset($user_id)) $user_id = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Post</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<?php
   if(isset($_POST['open_edit_box'])){
   $comment_id = intval($_POST['comment_id'] ?? 0);
?>
   <section class="comment-edit-form">
   <p>edit your comment</p>
   <?php
      $stmt = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
      $stmt->bind_param("i", $comment_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $fetch_edit_comment = $result->fetch_assoc();
   ?>
   <form action="index.php?route=post&post_id=<?= $post['id']; ?>" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">edit comment</button>
      <div class="inline-option-btn" onclick="window.location.href = 'index.php?route=post&post_id=<?= $post['id']; ?>';">cancel edit</div>
   </form>
   </section>
<?php
   }
?>

<section class="posts-container" style="padding-bottom: 0;">
   <div class="box-container">
      <form class="box" method="post">
         <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
         <input type="hidden" name="admin_id" value="<?= $post['admin_id']; ?>">
         <div class="post-admin">
            <span style="font-weight: bold;">👤</span>
            <div>
               <a href="index.php?route=author&author=<?= $post['name']; ?>"><?= $post['name']; ?></a>
               <div><?= $post['date']; ?></div>
            </div>
         </div>
         
         <?php if($post['image'] != ''){ ?>  
         <img src="<?php echo BASE_URL . UPLOADED_IMG_URL . $post['image']; ?>" class="post-image" alt="">
         <?php } ?>
         
         <div class="post-title"><?= $post['title']; ?></div>
         <div class="post-content"><?= $post['content']; ?></div>
         <div class="icons">
            <div><span style="font-weight: bold;">💬</span><span>(<?= $post['comments_count']; ?>)</span></div>
            <button type="button" onclick="likePost(this, <?= $post['id']; ?>, <?= $post['admin_id']; ?>)"><span style="font-weight: bold; <?php if($post['has_liked']){ echo 'color:var(--red);'; } ?>">❤</span><span>(<?= $post['likes_count']; ?>)</span></button>
         </div>
      </form>
   </div>
</section>

<section class="comments-container">
   <p class="comment-title">add comment</p>
   <?php
      if($user_id != '' && $fetch_profile){  
   ?>
   <form action="index.php?route=post&post_id=<?= $post['id']; ?>" method="post" class="add-comment">
      <input type="hidden" name="admin_id" value="<?= $post['admin_id']; ?>">
      <input type="hidden" name="user_name" value="<?= $fetch_profile['name']; ?>">
      <p class="user"><span style="font-weight: bold;">👤</span><a href="index.php?route=update"><?= $fetch_profile['name']; ?></a></p>
      <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="write your comment" required></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>
   <?php
   }else{
   ?>
   <div class="add-comment">
      <p>please login to add or edit your comment</p>
      <a href="index.php?route=login" class="inline-btn">login now</a>
   </div>
   <?php
      }
   ?>
   
   <p class="comment-title">post comments</p>
   <div class="user-comments-container">
      <?php
         if(!empty($post['comments'])){
            foreach($post['comments'] as $fetch_comments){
      ?>
      <div class="show-comments" style="<?php if($fetch_comments['user_id'] == $user_id){echo 'order:-1;'; } ?>">
         <div class="comment-user">
            <span style="font-weight: bold;">👤</span>
            <div>
               <span><?= $fetch_comments['user_name']; ?></span>
               <div><?= $fetch_comments['date']; ?></div>
            </div>
         </div>
         <div class="comment-box" style="<?php if($fetch_comments['user_id'] == $user_id){echo 'color:var(--white); background:var(--black);'; } ?>"><?= $fetch_comments['comment']; ?></div>
         <?php
            if($fetch_comments['user_id'] == $user_id){  
         ?>
         <form action="index.php?route=post&post_id=<?= $post['id']; ?>" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">edit comment</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">no comments added yet!</p>';
         }
      ?>
   </div>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
