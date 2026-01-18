<?php
if(!isset($authors)) $authors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Authors</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="authors">
   <h1 class="heading">authors</h1>
   <div class="box-container">
   <?php
      if(!empty($authors)){
         foreach($authors as $author){
   ?>
   <div class="box">
      <p>author : <span><?= $author['name']; ?></span></p>
      <p>total posts : <span><?= $author['posts_count']; ?></span></p>
      <p>posts likes : <span><?= $author['likes_count']; ?></span></p>
      <p>posts comments : <span><?= $author['comments_count']; ?></span></p>
      <a href="index.php?route=author&author=<?= $author['name']; ?>" class="btn">view posts</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no authors found</p>';
      }
   ?>
   </div>
</section>

<?php include __DIR__ . '/../../components/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
