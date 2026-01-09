<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

function post_home() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    
    $fetch_profile = null;
    $total_user_comments = 0;
    $total_user_likes = 0;
    if($user_id) {
        require_once __DIR__ . '/../models/UserModel.php';
        $fetch_profile = model_user_get_by_id($conn, $user_id);
        if($fetch_profile) {
            $total_user_comments = count(model_comment_get_by_user($conn, $user_id));
            $total_user_likes = count(model_like_get_by_user($conn, $user_id));
        }
    }
    
    $authors = [];
    
    $authors_result = $conn->query("SELECT DISTINCT name FROM `posts` WHERE status = 'active' ORDER BY name ASC");
    while($row = $authors_result->fetch_assoc()) {
        if(!in_array($row['name'], $authors)){
            $authors[] = $row['name'];
        }
    }
    
    $authors = array_slice($authors, 0, 20);
    
    $posts = model_post_get_by_status($conn, 'active', 6);
    
    foreach($posts as &$post) {
        $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
        $post['likes_count'] = model_like_get_count($conn, $post['id']);
        $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
    }
    
    require_once __DIR__ . '/../components/like_post.php';
    include __DIR__ . '/../views/user/home.php';
}

function post_all_posts() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    
    require_once __DIR__ . '/../components/like_post.php';
    
    $posts = model_post_get_by_status($conn, 'active');
    
    foreach($posts as &$post) {
        $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
        $post['likes_count'] = model_like_get_count($conn, $post['id']);
        $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
    }
    
    include __DIR__ . '/../views/user/posts.php';
}

function post_view_post() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    $post_id = intval($_GET['post_id'] ?? 0);
    
    $post = model_post_get_by_id($conn, $post_id);
    if(!$post || $post['status'] != 'active') {
        header('location: index.php?route=home');
        exit;
    }
    
    $post['comments'] = model_comment_get_by_post($conn, $post_id);
    $post['comments_count'] = count($post['comments']);
    $post['likes_count'] = model_like_get_count($conn, $post_id);
    $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post_id) : false;
    
    $fetch_profile = null;
    if($user_id) {
        require_once __DIR__ . '/../models/UserModel.php';
        $fetch_profile = model_user_get_by_id($conn, $user_id);
    }
    
    $message = [];
    if(isset($_POST['add_comment']) && $user_id) {
        $admin_id = intval($_POST['admin_id'] ?? 0);
        $user_name = $fetch_profile['name'] ?? '';
        $comment = trim($_POST['comment'] ?? '');
        
        if(!empty($comment)) {
            model_comment_create($conn, $post_id, $admin_id, $user_id, $user_name, $comment);
            $message[] = 'new comment added!';
            header('location: index.php?route=post&post_id=' . $post_id);
            exit;
        }
    }
    
    if(isset($_POST['edit_comment']) && $user_id) {
        $edit_comment_id = intval($_POST['edit_comment_id'] ?? 0);
        $comment_edit_box = trim($_POST['comment_edit_box'] ?? '');
        
        $stmt = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $comment_edit_box, $edit_comment_id, $user_id);
        if($stmt->execute()){
            $message[] = 'your comment edited successfully!';
            header('location: index.php?route=post&post_id=' . $post_id);
            exit;
        }
    }
    
    if(isset($_POST['delete_comment']) && $user_id) {
        $delete_comment_id = intval($_POST['comment_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM `comments` WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_comment_id, $user_id);
        if($stmt->execute()){
            $message[] = 'comment deleted successfully!';
            header('location: index.php?route=post&post_id=' . $post_id);
            exit;
        }
    }
    
    $post['comments'] = model_comment_get_by_post($conn, $post_id);
    $post['comments_count'] = count($post['comments']);
    
    include __DIR__ . '/../views/user/view_post.php';
}

function post_category() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    $category = $_GET['category'] ?? '';
    
    require_once __DIR__ . '/../components/like_post.php';
    
    $posts = model_post_get_by_category($conn, $category);
    
    foreach($posts as &$post) {
        $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
        $post['likes_count'] = model_like_get_count($conn, $post['id']);
        $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
    }
    
    include __DIR__ . '/../views/user/category.php';
}

function post_author_posts() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    $author = $_GET['author'] ?? '';
    
    require_once __DIR__ . '/../components/like_post.php';
    
    $posts = model_post_get_by_author($conn, $author);
    
    foreach($posts as &$post) {
        $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
        $post['likes_count'] = model_like_get_count($conn, $post['id']);
        $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
    }
    
    include __DIR__ . '/../views/user/author_posts.php';
}

function post_search() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    $searchTerm = $_POST['search_box'] ?? '';
    
    require_once __DIR__ . '/../components/like_post.php';
    
    $posts = model_post_search($conn, $searchTerm);
    
    foreach($posts as &$post) {
        $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
        $post['likes_count'] = model_like_get_count($conn, $post['id']);
        $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
    }
    
    include __DIR__ . '/../views/user/search.php';
}
?>
