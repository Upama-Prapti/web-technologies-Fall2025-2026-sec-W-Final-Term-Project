<?php
if(!isset($message)) $message = [];
if(!isset($posts)) $posts = [];
$admin_id = $_SESSION['admin_id'] ?? 0;
$status_filter = $_GET['status'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Posts</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="show-posts">
   <h1 class="heading">posts management</h1>
   
   <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
      <a href="index.php?route=admin&action=view_posts&status=all" class="option-btn" style="<?php echo ($status_filter == 'all') ? 'background: var(--main-color); color: white;' : ''; ?>">
         All Posts
      </a>
      <a href="index.php?route=admin&action=view_posts&status=active" class="option-btn" style="<?php echo ($status_filter == 'active') ? 'background: #28a745; color: white;' : ''; ?>">
         Active Posts
      </a>
      <a href="index.php?route=admin&action=view_posts&status=pending" class="option-btn" style="<?php echo ($status_filter == 'pending') ? 'background: #ffc107; color: white;' : ''; ?>">
         Pending Posts
      </a>
      <a href="index.php?route=admin&action=view_posts&status=deactive" class="option-btn" style="<?php echo ($status_filter == 'deactive') ? 'background: #dc3545; color: white;' : ''; ?>">
         Deactive Posts
      </a>
   </div>
   
   <p style="text-align: center; margin-bottom: 2rem; color: var(--light-color); font-size: 1.6rem;">
      <?php
         if($status_filter == 'active') echo 'Showing only active posts';
         elseif($status_filter == 'pending') echo 'Showing only pending posts - need approval';
         elseif($status_filter == 'deactive') echo 'Showing only deactive/rejected posts';
         else echo 'Showing all posts';
      ?>
   </p>
   
   <div class="box-container">
      <?php
         if(!empty($posts)){
            foreach($posts as $fetch_posts){
               $post_id = $fetch_posts['id'];
      ?>
      <form method="post" class="box" style="position: relative;">
         <input type="hidden" name="post_id" value="<?= $post_id; ?>">
         <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo '#28a745'; }elseif($fetch_posts['status'] == 'pending'){echo '#ffc107';}else{echo '#dc3545';}; ?>; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.4rem; font-weight: 600;">
               <?= strtoupper($fetch_posts['status']); ?>
            </div>
            <?php if(isset($fetch_posts['is_user_post']) && $fetch_posts['is_user_post'] == 1){ ?>
            <div style="background-color: #007bff; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 1.4rem; font-weight: 600;">
               USER POST
            </div>
            <?php } ?>
         </div>
         <?php if($fetch_posts['image'] != ''){ ?>
            <img src="<?php echo BASE_URL . UPLOADED_IMG_URL . $fetch_posts['image']; ?>" class="image" alt="" style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.5rem; margin-bottom: 1rem;">
         <?php } ?>
         <div class="title" style="font-size: 2rem; font-weight: 600; margin-bottom: 1rem; color: var(--black);"><?= $fetch_posts['title']; ?></div>
         <div class="posts-content" style="color: var(--light-color); margin-bottom: 1rem; line-height: 1.6;"><?= substr($fetch_posts['content'], 0, 200); ?><?= strlen($fetch_posts['content']) > 200 ? '...' : ''; ?></div>
         <div style="margin: 1rem 0; padding: 0.8rem; background-color: var(--light-bg); border-radius: 0.5rem;">
            <small style="color: var(--black); font-weight: 500;">Author: <?= $fetch_posts['name']; ?></small>
            <small style="color: var(--light-color); margin-left: 1rem;"><?= $fetch_posts['date']; ?></small>
         </div>
         <div class="icons" style="display: flex; gap: 2rem; margin: 1rem 0; padding: 1rem; background-color: var(--light-bg); border-radius: 0.5rem;">
            <div class="likes" style="display: flex; align-items: center; gap: 0.5rem;"><span style="color: var(--red); font-weight: bold;">❤</span><span style="color: var(--black); font-weight: 500;"><?= $fetch_posts['likes_count']; ?></span></div>
            <div class="comments" style="display: flex; align-items: center; gap: 0.5rem;"><span style="color: var(--main-color); font-weight: bold;">💬</span><span style="color: var(--black); font-weight: 500;"><?= $fetch_posts['comments_count']; ?></span></div>
         </div>
         <div class="flex-btn" style="margin-top: 1.5rem;">
            <?php if($fetch_posts['status'] == 'pending'){ ?>
               <button type="submit" name="approve" class="option-btn" style="background-color: #28a745; color: white; flex: 1;">✓ Approve</button>
               <button type="submit" name="reject" class="option-btn" style="background-color: #dc3545; color: white; flex: 1;">✗ Reject</button>
            <?php } ?>
            <?php if($fetch_posts['status'] == 'deactive'){ ?>
               <button type="submit" name="approve" class="option-btn" style="background-color: #28a745; color: white; flex: 1;">✓ Reactivate</button>
            <?php } ?>
            <a href="index.php?route=admin&action=edit_post&id=<?= $post_id; ?>" class="option-btn" style="flex: 1;">Edit</a>
            <button type="submit" name="delete" class="delete-btn" style="flex: 1;" onclick="return confirm('delete this post?');">Delete</button>
         </div>
         <a href="index.php?route=admin&action=read_post&post_id=<?= $post_id; ?>" class="btn" style="margin-top: 1rem;">View Post</a>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">no posts found for this filter! <a href="index.php?route=admin&action=add_post" class="btn" style="margin-top:1.5rem;">add post</a></p>';
         }
      ?>
   </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
