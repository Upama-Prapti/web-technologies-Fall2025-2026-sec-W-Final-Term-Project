<?php
if(!isset($message)) $message = [];
if(!isset($post)) {
    echo '<p class="empty">no posts found!</p>';
    echo '<div class="flex-btn">
      <a href="index.php?route=admin&action=view_posts" class="option-btn">view posts</a>
      <a href="index.php?route=admin&action=add_post" class="option-btn">add posts</a>
   </div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Post</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin_style.css">
</head>
<body>

<?php include __DIR__ . '/../../components/admin_header.php' ?>

<section class="post-editor">
   <h1 class="heading">edit post</h1>
   <form action="index.php?route=admin&action=edit_post&id=<?= $post['id']; ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $post['image']; ?>">
      <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
      <p>post status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $post['status']; ?>" selected><?= $post['status']; ?></option>
         <option value="pending">pending</option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>post title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="add post title" class="box" value="<?= $post['title']; ?>">
      <p>post content <span>*</span></p>
      <textarea name="content" class="box" required maxlength="10000" placeholder="write your content..." cols="30" rows="10"><?= $post['content']; ?></textarea>
      <p>post category <span>*</span></p>
      <select name="category" class="box" required>
         <option value="<?= $post['category']; ?>" selected><?= $post['category']; ?></option>
         <option value="nature">nature</option>
         <option value="education">education</option>
         <option value="pets and animals">pets and animals</option>
         <option value="technology">technology</option>
         <option value="fashion">fashion</option>
         <option value="entertainment">entertainment</option>
         <option value="movies and animations">movies</option>
         <option value="gaming">gaming</option>
         <option value="music">music</option>
         <option value="sports">sports</option>
         <option value="news">news</option>
         <option value="travel">travel</option>
         <option value="comedy">comedy</option>
         <option value="design and development">design and development</option>
         <option value="food and drinks">food and drinks</option>
         <option value="lifestyle">lifestyle</option>
         <option value="personal">personal</option>
         <option value="health and fitness">health and fitness</option>
         <option value="business">business</option>
         <option value="shopping">shopping</option>
         <option value="animations">animations</option>
      </select>
      <p>post image</p>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <?php if($post['image'] != ''){ ?>
         <img src="<?php echo BASE_URL . UPLOADED_IMG_URL . $post['image']; ?>" class="image" alt="">
         <input type="submit" value="delete image" class="inline-delete-btn" name="delete_image">
      <?php } ?>
      <div class="flex-btn">
         <input type="submit" value="save post" name="save" class="btn">
         <a href="index.php?route=admin&action=view_posts" class="option-btn">go back</a>
         <input type="submit" value="delete post" class="delete-btn" name="delete_post">
      </div>
   </form>
</section>

<script src="<?php echo ASSETS_URL; ?>js/admin_script.js"></script>
</body>
</html>
