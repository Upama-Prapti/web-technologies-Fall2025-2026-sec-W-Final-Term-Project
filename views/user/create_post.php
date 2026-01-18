<?php
if(!isset($message)) $message = [];
if(!isset($user)) $user = ['name' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Create Post</title>
   <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
</head>
<body>
   
<?php include __DIR__ . '/../../components/user_header.php'; ?>

<section class="form-container">
   <form action="index.php?route=create_post" method="post" enctype="multipart/form-data">
      <h3>create new post</h3>
      <p>Your post will be reviewed by admin before being published</p>
      <p>post title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="add post title" class="box">
      <p>post content <span>*</span></p>
      <textarea name="content" class="box" required maxlength="10000" placeholder="write your content..." cols="30" rows="10"></textarea>
      <p>post category <span>*</span></p>
      <select name="category" class="box" required>
         <option value="" selected disabled>-- select category* </option>
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
      <div class="flex-btn">
         <input type="submit" value="submit for approval" name="submit" class="btn">
         <a href="index.php?route=home" class="option-btn">cancel</a>
      </div>
   </form>
</section>

<script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
</body>
</html>
