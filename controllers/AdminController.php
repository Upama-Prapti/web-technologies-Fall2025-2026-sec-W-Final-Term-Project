<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

function admin_check_auth() {
    $admin_id = $_SESSION['admin_id'] ?? null;
    if(!$admin_id) {
        header('location: index.php?route=admin&action=login');
        exit;
    }
    return $admin_id;
}

function admin_get_profile($admin_id) {
    global $conn;
    // Check which table the admin is from based on session
    $is_from_admin_table = $_SESSION['is_from_admin_table'] ?? 1;
    
    if($is_from_admin_table == 1) {
        // Admin from admin table
        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
    } else {
        // Admin from users table
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ? AND is_admin = 1");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
    }
    
    return $profile;
}

function admin_ensure_superadmin_exists() {
    global $conn;
    $superadmin_name = SUPERADMIN_USERNAME;
    $default_password = sha1(SUPERADMIN_DEFAULT_PASSWORD);
    
    $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
    $stmt->bind_param("s", $superadmin_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0) {
        // Create admin if doesn't exist
        $stmt = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?, ?)");
        $stmt->bind_param("ss", $superadmin_name, $default_password);
        $stmt->execute();
    } else {
        // Ensure password is correct (update if wrong)
        $row = $result->fetch_assoc();
        if($row['password'] != $default_password) {
            $stmt = $conn->prepare("UPDATE `admin` SET password = ? WHERE name = ?");
            $stmt->bind_param("ss", $default_password, $superadmin_name);
            $stmt->execute();
        }
    }
}

function admin_check_default_password() {
    global $conn;
    $superadmin_name = SUPERADMIN_USERNAME;
    $default_password = sha1(SUPERADMIN_DEFAULT_PASSWORD);
    
    $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $superadmin_name, $default_password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

function admin_login() {
    global $conn;
    $message = [];
    
    admin_ensure_superadmin_exists();
    
    if(isset($_POST['submit'])){
        $name = trim($_POST['name'] ?? '');
        $pass_input = $_POST['pass'] ?? '';
        
        if(empty($name) || empty($pass_input)){
            $message[] = 'please enter both username and password!';
        } else {
            $pass = sha1($pass_input);
            
            // First check admin table
            $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
            $stmt->bind_param("ss", $name, $pass);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                // Clear user session variables when logging in as admin
                unset($_SESSION['user_id']);
                unset($_SESSION['user_name']);
                unset($_SESSION['is_admin']);
                
                // Set admin session variables
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['is_from_admin_table'] = 1;
                header('location: index.php?route=admin&action=dashboard');
                exit;
            } else {
                // Check users table with admin privileges
                $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND password = ? AND is_admin = 1");
                $stmt->bind_param("ss", $name, $pass);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    // Clear user session variables when logging in as admin
                    unset($_SESSION['user_id']);
                    unset($_SESSION['user_name']);
                    unset($_SESSION['is_admin']);
                    
                    // Set admin session variables
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_name'] = $row['name'];
                    $_SESSION['is_from_admin_table'] = 0;
                    header('location: index.php?route=admin&action=dashboard');
                    exit;
                } else {
                    $message[] = 'incorrect username or password!';
                }
            }
        }
    }
    
    include __DIR__ . '/../views/admin/login.php';
}

function admin_dashboard() {
    global $conn;
    $admin_id = admin_check_auth();
    $fetch_profile = admin_get_profile($admin_id);
    
    $stats = [];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `posts`");
    $stats['total_posts'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `posts` WHERE status = 'pending'");
    $stats['pending_posts'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `posts` WHERE status = 'active'");
    $stats['active_posts'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `posts` WHERE status = 'deactive'");
    $stats['deactive_posts'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `users`");
    $stats['total_users'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `admin`");
    $admin_table_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `users` WHERE is_admin = 1");
    $user_admin_count = $result->fetch_assoc()['count'];
    
    $stats['total_admins'] = $admin_table_count + $user_admin_count;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `comments`");
    $stats['total_comments'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM `likes`");
    $stats['total_likes'] = $result->fetch_assoc()['count'];
    
    include __DIR__ . '/../views/admin/dashboard.php';
}

function admin_add_post() {
    global $conn;
    $admin_id = admin_check_auth();
    $fetch_profile = admin_get_profile($admin_id);
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
            $stmt = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
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
            $stmt = $conn->prepare("INSERT INTO `posts`(admin_id, name, title, content, category, image, status) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("issssss", $admin_id, $name, $title, $content, $category, $image, $status);
            if($stmt->execute()){
                $message[] = isset($_POST['publish']) ? 'post published!' : 'draft saved!';
            } else {
                $message[] = 'failed to save post!';
            }
        }
    }
    
    include __DIR__ . '/../views/admin/add_posts.php';
}

function admin_view_posts() {
    global $conn;
    $admin_id = admin_check_auth();
    $message = [];
    
    $status_filter = $_GET['status'] ?? 'all';
    
    if(isset($_POST['delete'])){
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_delete_image = $result->fetch_assoc();
        
        if($fetch_delete_image && $fetch_delete_image['image'] != ''){
            @unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        $message[] = 'post deleted successfully!';
    }
    
    if(isset($_POST['approve'])){
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("UPDATE `posts` SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        if($stmt->execute()){
            $message[] = 'post approved successfully!';
            header('location: index.php?route=admin&action=view_posts&status=' . $status_filter);
            exit;
        }
    }
    
    if(isset($_POST['reject'])){
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("UPDATE `posts` SET status = 'deactive' WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        if($stmt->execute()){
            $message[] = 'post rejected successfully!';
            header('location: index.php?route=admin&action=view_posts&status=' . $status_filter);
            exit;
        }
    }
    
    if($status_filter == 'active') {
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE status = 'active' ORDER BY date DESC");
    } elseif($status_filter == 'deactive') {
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE status = 'deactive' ORDER BY date DESC");
    } elseif($status_filter == 'pending') {
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE status = 'pending' ORDER BY date DESC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM `posts` ORDER BY date DESC");
    }
    $stmt->execute();
    
    $result = $stmt->get_result();
    $posts = [];
    while($row = $result->fetch_assoc()) {
        $post_id = $row['id'];
        
        $row['comments_count'] = count(model_comment_get_by_post($conn, $post_id));
        $row['likes_count'] = model_like_get_count($conn, $post_id);
        
        $posts[] = $row;
    }
    
    include __DIR__ . '/../views/admin/view_posts.php';
}

function admin_edit_post() {
    global $conn;
    $admin_id = admin_check_auth();
    $post_id = intval($_GET['id'] ?? 0);
    $message = [];
    
    $post = model_post_get_by_id($conn, $post_id);
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
                move_uploaded_file($image_tmp_name, __DIR__ . '/../uploaded_img/' . $image);
                $stmt = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ?, image = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $title, $content, $category, $status, $image, $post_id);
                $stmt->execute();
                if($old_image != $image && $old_image != ''){
                    @unlink(__DIR__ . '/../uploaded_img/' . $old_image);
                }
                $message[] = 'post updated!';
            }
        } else {
            $stmt = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $title, $content, $category, $status, $post_id);
            $stmt->execute();
            $message[] = 'post updated!';
        }
        
        $post = model_post_get_by_id($conn, $post_id);
    }
    
    if(isset($_POST['delete_post'])){
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_delete_image = $result->fetch_assoc();
        
        if($fetch_delete_image && $fetch_delete_image['image'] != ''){
            @unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        header('location: index.php?route=admin&action=view_posts');
        exit;
    }
    
    if(isset($_POST['delete_image'])){
        $empty_image = '';
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_delete_image = $result->fetch_assoc();
        
        if($fetch_delete_image && $fetch_delete_image['image'] != ''){
            @unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
        }
        
        $stmt = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $empty_image, $p_id);
        $stmt->execute();
        $message[] = 'image deleted successfully!';
        
        $post = model_post_get_by_id($conn, $post_id);
    }
    
    include __DIR__ . '/../views/admin/edit_post.php';
}

function admin_read_post() {
    global $conn;
    $admin_id = admin_check_auth();
    $post_id = intval($_GET['post_id'] ?? 0);
    $message = [];
    
    $post = model_post_get_by_id($conn, $post_id);
    if(!$post) {
        header('location: index.php?route=admin&action=view_posts');
        exit;
    }
    
    if(isset($_POST['delete'])){
        $p_id = intval($_POST['post_id']);
        $stmt = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch_delete_image = $result->fetch_assoc();
        
        if($fetch_delete_image && $fetch_delete_image['image'] != ''){
            @unlink(__DIR__ . '/../uploaded_img/' . $fetch_delete_image['image']);
        }
        
        $stmt = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        
        header('location: index.php?route=admin&action=view_posts');
        exit;
    }
    
    if(isset($_POST['delete_comment'])){
        $comment_id = intval($_POST['comment_id']);
        model_comment_delete($conn, $comment_id);
        $message[] = 'comment deleted!';
    }
    
    $post['comments_count'] = count(model_comment_get_by_post($conn, $post_id));
    $post['likes_count'] = model_like_get_count($conn, $post_id);
    $comments = model_comment_get_by_post($conn, $post_id);
    
    include __DIR__ . '/../views/admin/read_post.php';
}

function admin_update_profile() {
    global $conn;
    $admin_id = admin_check_auth();
    $fetch_profile = admin_get_profile($admin_id);
    $message = [];
    
    if(isset($_POST['submit'])){
        $name = trim($_POST['name'] ?? '');
        $old_pass_input = trim($_POST['old_pass'] ?? '');
        $new_pass_input = trim($_POST['new_pass'] ?? '');
        $confirm_pass_input = trim($_POST['confirm_pass'] ?? '');
        
        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $is_admin_table = $result->num_rows > 0;
        
        if(!empty($name) && $name != $fetch_profile['name']){
            if($is_admin_table){
                $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND id != ?");
                $stmt->bind_param("si", $name, $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0){
                    $message[] = 'username already taken!';
                }else{
                    $stmt = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
                    $stmt->bind_param("si", $name, $admin_id);
                    $stmt->execute();
                    $_SESSION['admin_name'] = $name;
                    $message[] = 'username updated successfully!';
                }
            } else {
                $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND id != ?");
                $stmt->bind_param("si", $name, $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0){
                    $message[] = 'username already taken!';
                }else{
                    $stmt = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
                    $stmt->bind_param("si", $name, $admin_id);
                    $stmt->execute();
                    $_SESSION['admin_name'] = $name;
                    $message[] = 'username updated successfully!';
                }
            }
        }
        
        if(!empty($old_pass_input)){
            if($is_admin_table){
                $stmt = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch_prev_pass = $result->fetch_assoc();
                $prev_pass = $fetch_prev_pass['password'];
            } else {
                $stmt = $conn->prepare("SELECT password FROM `users` WHERE id = ? AND is_admin = 1");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch_prev_pass = $result->fetch_assoc();
                $prev_pass = $fetch_prev_pass['password'];
            }
            
            $old_pass_hashed = sha1($old_pass_input);
            
            if($old_pass_hashed != $prev_pass){
                $message[] = 'old password not matched!';
            }elseif(empty($new_pass_input)){
                $message[] = 'please enter a new password!';
            }elseif(strlen($new_pass_input) < 8){
                $message[] = 'password must be at least 8 characters!';
            }elseif($new_pass_input != $confirm_pass_input){
                $message[] = 'confirm password not matched!';
            }else{
                $new_pass_hashed = sha1($new_pass_input);
                if($is_admin_table){
                    $stmt = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                } else {
                    $stmt = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                }
                $stmt->bind_param("si", $new_pass_hashed, $admin_id);
                if($stmt->execute()){
                    $message[] = 'password updated successfully!';
                } else {
                    $message[] = 'failed to update password!';
                }
            }
        }
        
        $fetch_profile = admin_get_profile($admin_id);
    }
    
    include __DIR__ . '/../views/admin/update_profile.php';
}

function admin_users_accounts() {
    global $conn;
    $admin_id = admin_check_auth();
    $message = [];
    
    if(isset($_POST['assign_admin'])){
        $user_id = intval($_POST['user_id']);
        $is_admin = intval($_POST['is_admin']);
        
        $stmt = $conn->prepare("UPDATE `users` SET is_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_admin, $user_id);
        if($stmt->execute()){
            $message[] = 'User admin status updated successfully!';
        } else {
            $message[] = 'Failed to update user admin status!';
        }
    }
    
    $result = $conn->query("SELECT * FROM `users` ORDER BY id DESC");
    $users = [];
    while($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        
        $row['comments_count'] = count(model_comment_get_by_user($conn, $user_id));
        $row['likes_count'] = count(model_like_get_by_user($conn, $user_id));
        
        $users[] = $row;
    }
    
    include __DIR__ . '/../views/admin/users_accounts.php';
}

function admin_admin_accounts() {
    global $conn;
    $admin_id = admin_check_auth();
    $current_admin_name = $_SESSION['admin_name'] ?? '';
    $is_superadmin = ($current_admin_name == SUPERADMIN_USERNAME);
    $message = [];
    
    if($is_superadmin && isset($_POST['delete'])){
        $delete_id = intval($_POST['admin_id']);
        $delete_from = $_POST['delete_from'] ?? '';
        
        if($delete_from == 'admin_table') {
            $stmt = $conn->prepare("SELECT name FROM `admin` WHERE id = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin_to_delete = $result->fetch_assoc();
            
            if($admin_to_delete && $admin_to_delete['name'] != SUPERADMIN_USERNAME) {
                $stmt = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()){
                    if($row['image'] != ''){
                        @unlink(__DIR__ . '/../uploaded_img/' . $row['image']);
                    }
                }
                
                $stmt = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                
                $stmt = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                
                $stmt = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                
                $stmt = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                
                $message[] = 'Admin deleted successfully!';
            } else {
                $message[] = 'Cannot delete superadmin account!';
            }
        }
    }
    
    if($is_superadmin && isset($_POST['remove_admin'])){
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("UPDATE `users` SET is_admin = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if($stmt->execute()){
            $message[] = 'Admin access removed successfully!';
        }
    }
    
    $result = $conn->query("SELECT * FROM `admin` ORDER BY id DESC");
    $admins = [];
    while($row = $result->fetch_assoc()) {
        $admin_id_check = $row['id'];
        
        $row['posts_count'] = count(model_post_get_by_admin($conn, $admin_id_check));
        $row['is_registered_admin'] = true;
        $row['is_superadmin'] = ($row['name'] == SUPERADMIN_USERNAME);
        
        $admins[] = $row;
    }
    
    $result = $conn->query("SELECT * FROM `users` WHERE is_admin = 1 ORDER BY id DESC");
    while($row = $result->fetch_assoc()) {
        $user_id_check = $row['id'];
        
        $row['posts_count'] = count(model_post_get_by_admin($conn, $user_id_check));
        $row['is_registered_admin'] = false;
        $row['is_superadmin'] = false;
        
        $admins[] = $row;
    }
    
    include __DIR__ . '/../views/admin/admin_accounts.php';
}

function admin_comments() {
    global $conn;
    $admin_id = admin_check_auth();
    $message = [];
    
    if(isset($_POST['delete_comment'])){
        $comment_id = intval($_POST['comment_id']);
        model_comment_delete($conn, $comment_id);
        $message[] = 'comment deleted!';
    }
    
    $result = $conn->query("SELECT * FROM `comments` ORDER BY date DESC");
    $comments = [];
    while($row = $result->fetch_assoc()) {
        $post = model_post_get_by_id($conn, $row['post_id']);
        $row['post'] = $post;
        $comments[] = $row;
    }
    
    include __DIR__ . '/../views/admin/comments.php';
}

function admin_register_admin() {
    global $conn;
    $admin_id = admin_check_auth();
    $message = [];
    
    if(isset($_POST['submit'])){
        $name = trim($_POST['name'] ?? '');
        $pass = sha1($_POST['pass'] ?? '');
        $cpass = sha1($_POST['cpass'] ?? '');
        
        $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $message[] = 'username already taken!';
        }else{
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $user_result = $stmt->get_result();
            
            if($user_result->num_rows > 0){
                $message[] = 'username already taken!';
            }else{
                if(strlen($_POST['pass']) < 8){
                    $message[] = 'password must be at least 8 characters!';
                }elseif($pass != $cpass){
                    $message[] = 'confirm password not matched!';
                }else{
                    $stmt = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
                    $stmt->bind_param("ss", $name, $cpass);
                    if($stmt->execute()){
                        $message[] = 'new admin registered!';
                    }
                }
            }
        }
    }
    
    include __DIR__ . '/../views/admin/register_admin.php';
}

function admin_logout() {
    session_unset();
    session_destroy();
    header('location: index.php?route=admin&action=login');
    exit;
}
?>
