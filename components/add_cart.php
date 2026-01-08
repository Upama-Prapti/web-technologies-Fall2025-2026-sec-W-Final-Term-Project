<?php
if(isset($_POST['add_to_cart'])){

   $user_id = $_SESSION['user_id'] ?? '';
   
   if($user_id == ''){
      header('location:index.php?route=login');
      exit;
   }else{
      global $conn;
      
      $pid = intval($_POST['pid']);
      $name = trim($_POST['name']);
      $price = floatval($_POST['price']);
      $image = trim($_POST['image']);
      $qty = intval($_POST['qty']);

      $stmt = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
      $stmt->bind_param("is", $user_id, $name);
      $stmt->execute();
      $result = $stmt->get_result();

      if($result->num_rows > 0){
         $message[] = 'already added to cart!';
      }else{
         $stmt = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $stmt->bind_param("iisdis", $user_id, $pid, $name, $price, $qty, $image);
         if($stmt->execute()){
            $message[] = 'added to cart!';
         }
      }
   }
}
?>
