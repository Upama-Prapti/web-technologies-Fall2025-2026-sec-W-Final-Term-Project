<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="dashboard">
   <h1 class="heading">dashboard</h1>
   <div class="box-container">
      <div class="box">
         <h3>welcome!</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="index.php?route=admin&action=update_profile" class="btn">update profile</a>
      </div>

      <div class="box">
         <h3><?= $stats['total_posts']; ?></h3>
         <p>total posts</p>
         <a href="index.php?route=admin&action=add_post" class="btn">add new post</a>
      </div>

      <div class="box">
         <h3><?= $stats['pending_posts']; ?></h3>
         <p>pending posts</p>
         <a href="index.php?route=admin&action=view_posts&status=pending" class="btn">see pending</a>
      </div>

      <div class="box">
         <h3><?= $stats['active_posts']; ?></h3>
         <p>active posts</p>
         <a href="index.php?route=admin&action=view_posts&status=active" class="btn">see active</a>
      </div>

      <div class="box">
         <h3><?= $stats['deactive_posts']; ?></h3>
         <p>deactive posts</p>
         <a href="index.php?route=admin&action=view_posts&status=deactive" class="btn">see deactive</a>
      </div>

      <div class="box">
         <h3><?= $stats['total_users']; ?></h3>
         <p>users account</p>
         <a href="index.php?route=admin&action=users_accounts" class="btn">see users</a>
      </div>

      <div class="box">
         <h3><?= $stats['total_admins']; ?></h3>
         <p>admins account</p>
         <a href="index.php?route=admin&action=admin_accounts" class="btn">see admins</a>
      </div>
      
      <div class="box">
         <h3><?= $stats['total_comments']; ?></h3>
         <p>total comments</p>
         <a href="index.php?route=admin&action=comments" class="btn">see comments</a>
      </div>

      <div class="box">
         <h3><?= $stats['total_likes']; ?></h3>
         <p>total likes</p>
         <a href="index.php?route=admin&action=view_posts" class="btn">see posts</a>
      </div>
   </div>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
