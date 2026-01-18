<?php
if(!isset($message)) $message = [];
if(!isset($comments_with_posts)) $comments_with_posts = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>My Comments</title>
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
      global $conn;
      $stmt = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
      $stmt->bind_param("i", $comment_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $fetch_edit_comment = $result->fetch_assoc();
   ?>
   <form action="index.php?route=comments" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="please enter your comment"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">edit comment</button>
      <div class="inline-option-btn" onclick="window.location.href = 'index.php?route=comments';">cancel edit</div>
   </form>
   </section>
<?php
   }
?>

<section class="comments-container">
   <h1 class="heading">your comments</h1>
   <p class="comment-title">your comments on the posts</p>
   <div class="user-comments-container">
      <?php
         if(!empty($comments_with_posts)){
            foreach($comments_with_posts as $fetch_comments){
               if(isset($fetch_comments['post']) && $fetch_comments['post']){
      ?>
      <div class="show-comments">
         <div class="post-title"> from : <span><?= $fetch_comments['post']['title']; ?></span> <a href="index.php?route=post&post_id=<?= $fetch_comments['post']['id']; ?>" >view post</a></div>
         <div class="comment-box"><?= $fetch_comments['comment']; ?></div>
         <form action="index.php?route=comments" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">edit comment</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
               }
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
