<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

class AdminController {
    private $adminModel;
    private $userModel;
    private $postModel;
    private $commentModel;
    private $likeModel;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->adminModel = new AdminModel($conn);
        $this->userModel = new UserModel($conn);
        $this->postModel = new PostModel($conn);
        $this->commentModel = new CommentModel($conn);
        $this->likeModel = new LikeModel($conn);
    }
    
    private function checkAdminAuth() {
        $admin_id = $_SESSION['admin_id'] ?? null;
        if(!$admin_id) {
            header('location: index.php?route=admin&action=login');
            exit;
        }
        return $admin_id;
    }
    
    private function getAdminProfile($admin_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `admin` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        
        if(!$profile) {
            $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE id = ? AND is_admin = 1");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $profile = $result->fetch_assoc();
        }
        return $profile;
    }
    
    public function login() {
        global $conn;
        $message = [];
        
        if(isset($_POST['submit'])){
            $loginType = $_POST['login_type'] ?? 'admin';
            
            if($loginType === 'admin'){
                $name = trim($_POST['name']);
                $pass = sha1($_POST['pass']);
                
                // First check admin table
                $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
                $stmt->bind_param("ss", $name, $pass);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_name'] = $row['name'];
                    header('location: index.php?route=admin&action=dashboard');
                    exit;
                } else {
                    // Check users table for users with is_admin=1
                    $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND password = ? AND is_admin = 1");
                    $stmt->bind_param("ss", $name, $pass);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if($result->num_rows > 0){
                        $row = $result->fetch_assoc();
                        $_SESSION['admin_id'] = $row['id'];
                        $_SESSION['admin_name'] = $row['name'];
                        header('location: index.php?route=admin&action=dashboard');
                        exit;
                    } else {
                        $message[] = 'incorrect username or password!';
                    }
                }
            } else {
                // User login - redirect to user login page
                header('location: index.php?route=login');
                exit;
            }
        }
        
        include __DIR__ . '/../views/admin/login.php';
    }
    
    public function dashboard() {
        $admin_id = $this->checkAdminAuth();
        $fetch_profile = $this->getAdminProfile($admin_id);
        
        // Check if user is admin from users table
        $stmt = $this->conn->prepare("SELECT is_admin FROM `users` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $is_user_admin = false;
        if($user_result->num_rows > 0){
            $user_data = $user_result->fetch_assoc();
            $is_user_admin = $user_data['is_admin'] == 1;
        }
        
        // Get statistics
        $stats = [];
        
        // Posts count
        if($is_user_admin){
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts`");
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
        }
        $result = $stmt->get_result();
        $stats['total_posts'] = $result->fetch_assoc()['count'];
        
        // Pending posts
        if($is_user_admin){
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE status = 'pending'");
            $stmt->execute();
            $result = $stmt->get_result();
            $stats['pending_posts'] = $result->fetch_assoc()['count'];
        }
        
        // Active posts
        if($is_user_admin){
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE status = 'active'");
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE admin_id = ? AND status = 'active'");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
        }
        $result = $stmt->get_result();
        $stats['active_posts'] = $result->fetch_assoc()['count'];
        
        // Deactive posts
        if($is_user_admin){
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE status = 'deactive'");
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE admin_id = ? AND status = 'deactive'");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
        }
        $result = $stmt->get_result();
        $stats['deactive_posts'] = $result->fetch_assoc()['count'];
        
        // Users count
        $result = $this->conn->query("SELECT COUNT(*) as count FROM `users`");
        $stats['total_users'] = $result->fetch_assoc()['count'];
        
        // Admins count - count from admin table + users with is_admin=1
        $result = $this->conn->query("SELECT COUNT(*) as count FROM `admin`");
        $admin_table_count = $result->fetch_assoc()['count'];
        
        $result = $this->conn->query("SELECT COUNT(*) as count FROM `users` WHERE is_admin = 1");
        $user_admin_count = $result->fetch_assoc()['count'];
        
        $stats['total_admins'] = $admin_table_count + $user_admin_count;
        
        // Comments count
        if($is_user_admin){
            $result = $this->conn->query("SELECT COUNT(*) as count FROM `comments`");
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `comments` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        $stats['total_comments'] = $result->fetch_assoc()['count'];
        
        // Likes count
        if($is_user_admin){
            $result = $this->conn->query("SELECT COUNT(*) as count FROM `likes`");
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `likes` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        $stats['total_likes'] = $result->fetch_assoc()['count'];
        
        // Make variables available to views
        global $conn;
        
        include __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function addPost() {
        $admin_id = $this->checkAdminAuth();
        $fetch_profile = $this->getAdminProfile($admin_id);
        $message = [];
        
        if(isset($_POST['publish']) || isset($_POST['draft'])){
            $name = $fetch_profile['name'];
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $status = isset($_POST['publish']) ? 'active' : 'pending';
            
            $image = $_FILES['image']['name'] ?? '';
            $image_size = $_FILES['image']['size'] ?? 0;
            $image_tmp_name = $_FILES['image']['tmp_name'] ?? '';
            
            if($image && $image != ''){
                $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
                $stmt->bind_param("si", $image, $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($result->num_rows > 0){
                    $message[] = 'image name repeated!';
                }elseif($image_size > 2000000){
                    $message[] = 'images size is too large!';
                }else{
                    move_uploaded_file($image_tmp_name, __DIR__ . '/../uploaded_img/' . $image);
                }
            }else{
                $image = '';
            }
            
            if(empty($message) || !in_array('image name repeated!', $message)){
                $stmt = $this->conn->prepare("INSERT INTO `posts`(admin_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
                $stmt->bind_param("issssss", $admin_id, $name, $title, $content, $category, $image, $status);
                if($stmt->execute()){
                    $message[] = isset($_POST['publish']) ? 'post published!' : 'draft saved!';
                } else {
                    $message[] = 'failed to save post!';
                }
            }
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/add_posts.php';
    }
    
    public function viewPosts() {
        $admin_id = $this->checkAdminAuth();
        $message = [];
        
        // Check if user is admin
        $stmt = $this->conn->prepare("SELECT is_admin FROM `users` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $is_user_admin = false;
        if($user_result->num_rows > 0){
            $user_data = $user_result->fetch_assoc();
            $is_user_admin = $user_data['is_admin'] == 1;
        }
        
        // Handle actions
        if(isset($_POST['delete'])){
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch_delete_image = $result->fetch_assoc();
            
            if($fetch_delete_image && $fetch_delete_image['image'] != ''){
                unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
            }
            
            $stmt = $this->conn->prepare("DELETE FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            $message[] = 'post deleted successfully!';
        }
        
        if(isset($_POST['approve'])){
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("UPDATE `posts` SET status = 'active' WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            if($stmt->execute()){
                $message[] = 'post approved successfully!';
                // Redirect to prevent duplicate submissions
                header('location: index.php?route=admin&action=view_posts');
                exit;
            }
        }
        
        if(isset($_POST['reject'])){
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("UPDATE `posts` SET status = 'deactive' WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            if($stmt->execute()){
                $message[] = 'post rejected successfully!';
                // Redirect to prevent duplicate submissions
                header('location: index.php?route=admin&action=view_posts');
                exit;
            }
        }
        
        // Get posts - show all posts for user_admin, including user posts for approval
        if($is_user_admin){
            $stmt = $this->conn->prepare("SELECT * FROM `posts` ORDER BY date DESC");
            $stmt->execute();
        } else {
            // Regular admin sees their own posts and pending user posts
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? OR (is_user_post = 1 AND status = 'pending') ORDER BY date DESC");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
        }
        $result = $stmt->get_result();
        $posts = [];
        while($row = $result->fetch_assoc()) {
            $post_id = $row['id'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `comments` WHERE post_id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $comment_result = $stmt->get_result();
            $row['comments_count'] = $comment_result->fetch_assoc()['count'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `likes` WHERE post_id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $like_result = $stmt->get_result();
            $row['likes_count'] = $like_result->fetch_assoc()['count'];
            
            $posts[] = $row;
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/view_posts.php';
    }
    
    public function editPost() {
        $admin_id = $this->checkAdminAuth();
        $post_id = intval($_GET['id'] ?? 0);
        $message = [];
        
        $post = $this->postModel->getPostById($post_id);
        if(!$post) {
            header('location: index.php?route=admin&action=view_posts');
            exit;
        }
        
        if(isset($_POST['save'])){
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $status = trim($_POST['status'] ?? '');
            
            $old_image = $_POST['old_image'] ?? '';
            $image = $_FILES['image']['name'] ?? '';
            $image_size = $_FILES['image']['size'] ?? 0;
            $image_tmp_name = $_FILES['image']['tmp_name'] ?? '';
            
            if(!empty($image)){
                if($image_size > 2000000){
                    $message[] = 'images size is too large!';
                }else{
                    $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ? AND id != ?");
                    $stmt->bind_param("sii", $image, $admin_id, $post_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        $message[] = 'please rename your image!';
                    }else{
                        move_uploaded_file($image_tmp_name, __DIR__ . '/../uploaded_img/' . $image);
                        $stmt = $this->conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ?, image = ? WHERE id = ?");
                        $stmt->bind_param("sssssi", $title, $content, $category, $status, $image, $post_id);
                        $stmt->execute();
                        if($old_image != $image && $old_image != ''){
                            unlink(__DIR__ . '/../uploaded_img/' . $old_image);
                        }
                        $message[] = 'post updated!';
                    }
                }
            } else {
                $stmt = $this->conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $content, $category, $status, $post_id);
                $stmt->execute();
                $message[] = 'post updated!';
            }
            
            $post = $this->postModel->getPostById($post_id);
        }
        
        if(isset($_POST['delete_post'])){
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch_delete_image = $result->fetch_assoc();
            
            if($fetch_delete_image && $fetch_delete_image['image'] != ''){
                unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
            }
            
            $stmt = $this->conn->prepare("DELETE FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            header('location: index.php?route=admin&action=view_posts');
            exit;
        }
        
        if(isset($_POST['delete_image'])){
            $empty_image = '';
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch_delete_image = $result->fetch_assoc();
            
            if($fetch_delete_image && $fetch_delete_image['image'] != ''){
                unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
            }
            
            $stmt = $this->conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $empty_image, $p_id);
            $stmt->execute();
            $message[] = 'image deleted successfully!';
            
            $post = $this->postModel->getPostById($post_id);
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/edit_post.php';
    }
    
    public function readPost() {
        $admin_id = $this->checkAdminAuth();
        $post_id = intval($_GET['post_id'] ?? 0);
        $message = [];
        
        $post = $this->postModel->getPostById($post_id);
        if(!$post) {
            header('location: index.php?route=admin&action=view_posts');
            exit;
        }
        
        if(isset($_POST['delete'])){
            $p_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch_delete_image = $result->fetch_assoc();
            
            if($fetch_delete_image && $fetch_delete_image['image'] != ''){
                unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
            }
            
            $stmt = $this->conn->prepare("DELETE FROM `posts` WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            
            header('location: index.php?route=admin&action=view_posts');
            exit;
        }
        
        if(isset($_POST['delete_comment'])){
            $comment_id = intval($_POST['comment_id']);
            $this->commentModel->deleteComment($comment_id);
            $message[] = 'comment delete!';
        }
        
        $post['comments_count'] = count($this->commentModel->getCommentsByPost($post_id));
        $post['likes_count'] = $this->likeModel->getLikeCount($post_id);
        $comments = $this->commentModel->getCommentsByPost($post_id);
        
        global $conn;
        include __DIR__ . '/../views/admin/read_post.php';
    }
    
    public function updateProfile() {
        $admin_id = $this->checkAdminAuth();
        $fetch_profile = $this->getAdminProfile($admin_id);
        $message = [];
        
        if(isset($_POST['submit'])){
            $name = trim($_POST['name'] ?? '');
            
            // Check if user is from admin table or users table
            $stmt = $this->conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $is_admin_table = $result->num_rows > 0;
            
            if(!empty($name)){
                if($is_admin_table){
                    $stmt = $this->conn->prepare("SELECT * FROM `admin` WHERE name = ? AND id != ?");
                    $stmt->bind_param("si", $name, $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        $message[] = 'username already taken!';
                    }else{
                        $stmt = $this->conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
                        $stmt->bind_param("si", $name, $admin_id);
                        $stmt->execute();
                    }
                } else {
                    $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE name = ? AND id != ?");
                    $stmt->bind_param("si", $name, $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        $message[] = 'username already taken!';
                    }else{
                        $stmt = $this->conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
                        $stmt->bind_param("si", $name, $admin_id);
                        $stmt->execute();
                    }
                }
            }
            
            // Password update
            $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
            
            if($is_admin_table){
                $stmt = $this->conn->prepare("SELECT password FROM `admin` WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch_prev_pass = $result->fetch_assoc();
                $prev_pass = $fetch_prev_pass['password'];
            } else {
                $stmt = $this->conn->prepare("SELECT password FROM `users` WHERE id = ? AND is_admin = 1");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch_prev_pass = $result->fetch_assoc();
                $prev_pass = $fetch_prev_pass['password'];
            }
            
            $old_pass = sha1($_POST['old_pass'] ?? '');
            $new_pass = sha1($_POST['new_pass'] ?? '');
            $confirm_pass = sha1($_POST['confirm_pass'] ?? '');
            
            if($old_pass != $empty_pass){
                if($old_pass != $prev_pass){
                    $message[] = 'old password not matched!';
                }elseif($new_pass != $confirm_pass){
                    $message[] = 'confirm password not matched!';
                }else{
                    if($new_pass != $empty_pass){
                        if($is_admin_table){
                            $stmt = $this->conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                        } else {
                            $stmt = $this->conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                        }
                        $stmt->bind_param("si", $confirm_pass, $admin_id);
                        if($stmt->execute()){
                            $message[] = 'password updated successfully!';
                        } else {
                            $message[] = 'failed to update password!';
                        }
                    }else{
                        $message[] = 'please enter a new password!';
                    }
                }
            }
            
            $fetch_profile = $this->getAdminProfile($admin_id);
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/update_profile.php';
    }
    
    public function usersAccounts() {
        $admin_id = $this->checkAdminAuth();
        $message = [];
        
        // Assign admin role
        if(isset($_POST['assign_admin'])){
            $user_id = intval($_POST['user_id']);
            $is_admin = intval($_POST['is_admin']);
            
            $stmt = $this->conn->prepare("UPDATE `users` SET is_admin = ? WHERE id = ?");
            $stmt->bind_param("ii", $is_admin, $user_id);
            if($stmt->execute()){
                $message[] = 'User admin status updated successfully!';
            } else {
                $message[] = 'Failed to update user admin status!';
            }
        }
        
        $result = $this->conn->query("SELECT * FROM `users` ORDER BY id DESC");
        $users = [];
        while($row = $result->fetch_assoc()) {
            $user_id = $row['id'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `comments` WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $comment_result = $stmt->get_result();
            $row['comments_count'] = $comment_result->fetch_assoc()['count'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `likes` WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $like_result = $stmt->get_result();
            $row['likes_count'] = $like_result->fetch_assoc()['count'];
            
            $users[] = $row;
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/users_accounts.php';
    }
    
    public function adminAccounts() {
        $admin_id = $this->checkAdminAuth();
        $message = [];
        
        if(isset($_POST['delete'])){
            $delete_id = intval($_POST['post_id']);
            $stmt = $this->conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                if($row['image'] != ''){
                    unlink(__DIR__ . '/../uploaded_img/' . $row['image']);
                }
            }
            
            $stmt = $this->conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM `admin` WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            
            header('location: components/admin_logout.php');
            exit;
        }
        
        // Get admins from admin table
        $result = $this->conn->query("SELECT * FROM `admin` ORDER BY id DESC");
        $admins = [];
        while($row = $result->fetch_assoc()) {
            $admin_id_check = $row['id'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE admin_id = ?");
            $stmt->bind_param("i", $admin_id_check);
            $stmt->execute();
            $post_result = $stmt->get_result();
            $row['posts_count'] = $post_result->fetch_assoc()['count'];
            
            $admins[] = $row;
        }
        
        // Also get users with is_admin = 1
        $result = $this->conn->query("SELECT * FROM `users` WHERE is_admin = 1 ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            $user_id_check = $row['id'];
            
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `posts` WHERE admin_id = ?");
            $stmt->bind_param("i", $user_id_check);
            $stmt->execute();
            $post_result = $stmt->get_result();
            $row['posts_count'] = $post_result->fetch_assoc()['count'];
            
            $admins[] = $row;
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/admin_accounts.php';
    }
    
    public function comments() {
        $admin_id = $this->checkAdminAuth();
        $message = [];
        
        if(isset($_POST['delete_comment'])){
            $comment_id = intval($_POST['comment_id']);
            $this->commentModel->deleteComment($comment_id);
            $message[] = 'comment delete!';
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM `comments` WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while($row = $result->fetch_assoc()) {
            $post = $this->postModel->getPostById($row['post_id']);
            $row['post'] = $post;
            $comments[] = $row;
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/comments.php';
    }
    
    public function registerAdmin() {
        $admin_id = $this->checkAdminAuth();
        $message = [];
        
        if(isset($_POST['submit'])){
            $name = trim($_POST['name'] ?? '');
            $pass = sha1($_POST['pass'] ?? '');
            $cpass = sha1($_POST['cpass'] ?? '');
            
            $stmt = $this->conn->prepare("SELECT * FROM `admin` WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
                $message[] = 'username already taken!';
            }else{
                if($pass != $cpass){
                    $message[] = 'confirm password not matched!';
                }else{
                    $stmt = $this->conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
                    $stmt->bind_param("ss", $name, $cpass);
                    if($stmt->execute()){
                        $message[] = 'new admin registered!';
                    }
                }
            }
        }
        
        global $conn;
        include __DIR__ . '/../views/admin/register_admin.php';
    }
    
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('location: index.php?route=admin&action=login');
        exit;
    }
}
?>

