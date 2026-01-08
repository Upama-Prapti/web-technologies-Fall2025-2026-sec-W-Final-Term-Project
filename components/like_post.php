<?php

global $conn;
$user_id = $_SESSION['user_id'] ?? '';

if(isset($_POST['like_post'])){

   if($user_id != ''){
      
      $post_id = intval($_POST['post_id']);
      $admin_id = intval($_POST['admin_id']);
      
      if(isset($conn) && $conn){
         require_once __DIR__ . '/../models/LikeModel.php';
         
         $result = model_like_toggle($conn, $user_id, $admin_id, $post_id);
         $message[] = $result == 'added' ? 'added to likes' : 'removed from likes';
         
         $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
         $host = $_SERVER['HTTP_HOST'];
         $script = $_SERVER['SCRIPT_NAME'];
         $query_string = '';
         
         if(!empty($_GET)){
            $query_string = '?' . http_build_query($_GET);
         }
         
         $redirect_url = $protocol . '://' . $host . $script . $query_string;
         header('location: ' . $redirect_url);
         exit;
      }
      
   }else{
      $message[] = 'please login first!';
   }

}

?>
