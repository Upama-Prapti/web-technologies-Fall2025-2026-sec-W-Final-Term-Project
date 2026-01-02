<?php

global $conn;
$user_id = $_SESSION['user_id'] ?? '';

if(isset($_POST['like_post'])){

   if($user_id != ''){
      
      $post_id = intval($_POST['post_id']);
      $admin_id = intval($_POST['admin_id']);
      
      if(isset($conn) && $conn){
         $stmt = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ? AND user_id = ?");
         $stmt->bind_param("ii", $post_id, $user_id);
         $stmt->execute();
         $result = $stmt->get_result();

         if($result->num_rows > 0){
            $stmt = $conn->prepare("DELETE FROM `likes` WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $user_id);
            $stmt->execute();
            $message[] = 'removed from likes';
         }else{
            $stmt = $conn->prepare("INSERT INTO `likes`(user_id, post_id, admin_id) VALUES(?,?,?)");
            $stmt->bind_param("iii", $user_id, $post_id, $admin_id);
            $stmt->execute();
            $message[] = 'added to likes';
         }
         
         // Redirect to prevent duplicate submissions on refresh
         // Get the current page URL without query string to avoid resubmission
         $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
         $host = $_SERVER['HTTP_HOST'];
         $script = $_SERVER['SCRIPT_NAME'];
         $query_string = '';
         
         // Preserve GET parameters
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