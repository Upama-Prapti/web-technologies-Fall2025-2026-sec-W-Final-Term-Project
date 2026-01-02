<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

class UserController {
    private $userModel;
    private $postModel;
    private $commentModel;
    private $likeModel;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userModel = new UserModel($conn);
        $this->postModel = new PostModel($conn);
        $this->commentModel = new CommentModel($conn);
        $this->likeModel = new LikeModel($conn);
    }
    
    public function updateProfile() {
        global $conn;
        $user_id = $_SESSION['user_id'] ?? '';
        if(!$user_id) {
            header('location: index.php?route=home');
            exit;
        }
        
        $message = [];
        $user = $this->userModel->getUserById($user_id);
        
        if(isset($_POST['submit'])) {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $old_pass = $_POST['old_pass'] ?? '';
            $new_pass = $_POST['new_pass'] ?? '';
            $confirm_pass = $_POST['confirm_pass'] ?? '';
            
            if(!empty($name)) {
                $this->userModel->updateUser($user_id, $name, $user['email']);
            }
            
            if(!empty($email) && $email != $user['email']) {
                $existing = $this->userModel->getUserByEmail($email);
                if($existing) {
                    $message[] = 'email already taken!';
                } else {
                    $this->userModel->updateUser($user_id, $user['name'], $email);
                }
            }
            
            if(!empty($old_pass)) {
                if(sha1($old_pass) != $user['password']) {
                    $message[] = 'old password not matched!';
                } elseif($new_pass != $confirm_pass) {
                    $message[] = 'confirm password not matched!';
                } elseif(!empty($new_pass)) {
                    $this->userModel->changePassword($user_id, $new_pass);
                    $message[] = 'password updated successfully!';
                }
            }
            
            $user = $this->userModel->getUserById($user_id);
        }
        
        include __DIR__ . '/../views/user/update.php';
    }
    
    public function userLikes() {
        global $conn;
        $user_id = $_SESSION['user_id'] ?? '';
        if(!$user_id) {
            header('location: index.php?route=home');
            exit;
        }
        
        require_once __DIR__ . '/../components/like_post.php';
        
        $likes = $this->likeModel->getLikesByUser($user_id);
        $liked_posts = [];
        
        foreach($likes as $like) {
            $post = $this->postModel->getPostById($like['post_id']);
            if($post && $post['status'] != 'deactive') {
                $post['likes_count'] = $this->likeModel->getLikeCount($post['id']);
                $post['comments_count'] = count($this->commentModel->getCommentsByPost($post['id']));
                $liked_posts[] = $post;
            }
        }
        
        include __DIR__ . '/../views/user/user_likes.php';
    }
    
    public function userComments() {
        global $conn;
        $user_id = $_SESSION['user_id'] ?? '';
        if(!$user_id) {
            header('location: index.php?route=home');
            exit;
        }
        
        $message = [];
        
        if(isset($_POST['edit_comment'])) {
            $comment_id = intval($_POST['edit_comment_id'] ?? 0);
            $comment_text = trim($_POST['comment_edit_box'] ?? '');
            
            // Update comment logic here
            $message[] = 'your comment edited successfully!';
        }
        
        if(isset($_POST['delete_comment'])) {
            $comment_id = intval($_POST['comment_id'] ?? 0);
            $this->commentModel->deleteComment($comment_id);
            $message[] = 'comment deleted successfully!';
        }
        
        // Handle edit comment
        if(isset($_POST['edit_comment'])){
            $edit_comment_id = intval($_POST['edit_comment_id'] ?? 0);
            $comment_edit_box = trim($_POST['comment_edit_box'] ?? '');
            
            global $conn;
            $stmt = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $comment_edit_box, $edit_comment_id, $user_id);
            if($stmt->execute()){
                $message[] = 'your comment edited successfully!';
            }
        }
        
        $comments = $this->commentModel->getCommentsByUser($user_id);
        $comments_with_posts = [];
        
        foreach($comments as $comment) {
            $post = $this->postModel->getPostById($comment['post_id']);
            $comment['post'] = $post;
            $comments_with_posts[] = $comment;
        }
        
        include __DIR__ . '/../views/user/user_comments.php';
    }
    
    public function createPost() {
        global $conn;
        $user_id = $_SESSION['user_id'] ?? '';
        if(!$user_id) {
            header('location: index.php?route=login');
            exit;
        }
        
        $message = [];
        $user = $this->userModel->getUserById($user_id);
        
        if(isset($_POST['submit'])){
            $name = $user['name'];
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            
            $image = $_FILES['image']['name'] ?? '';
            $image_size = $_FILES['image']['size'] ?? 0;
            $image_tmp_name = $_FILES['image']['tmp_name'] ?? '';
            
            if($image && $image != ''){
                if($image_size > 2000000){
                    $message[] = 'images size is too large!';
                }else{
                    move_uploaded_file($image_tmp_name, __DIR__ . '/../uploaded_img/' . $image);
                }
            }else{
                $image = '';
            }
            
            if(empty($message)){
                // Create post with is_user_post = 1 and status = 'pending'
                $stmt = $conn->prepare("INSERT INTO `posts`(admin_id, name, title, content, category, image, status, is_user_post) VALUES(?,?,?,?,?,?,'pending',1)");
                $stmt->bind_param("isssss", $user_id, $name, $title, $content, $category, $image);
                if($stmt->execute()){
                    $message[] = 'post submitted for approval!';
                    header('location: index.php?route=home');
                    exit;
                } else {
                    $message[] = 'failed to submit post!';
                }
            }
        }
        
        include __DIR__ . '/../views/user/create_post.php';
    }
}
?>

