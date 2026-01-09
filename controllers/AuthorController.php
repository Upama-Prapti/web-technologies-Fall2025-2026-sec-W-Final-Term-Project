<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

function author_all_authors() {
    global $conn;
    
    $admins = model_admin_get_all($conn);
    $authors = [];
    
    foreach($admins as $admin) {
        $posts = model_post_get_by_admin($conn, $admin['id']);
        $active_posts = array_filter($posts, function($p) { return $p['status'] == 'active'; });
        
        $total_likes = 0;
        $total_comments = 0;
        foreach($active_posts as $post) {
            $total_likes += model_like_get_count($conn, $post['id']);
            $total_comments += count(model_comment_get_by_post($conn, $post['id']));
        }
        
        $authors[] = [
            'id' => $admin['id'],
            'name' => $admin['name'],
            'posts_count' => count($active_posts),
            'likes_count' => $total_likes,
            'comments_count' => $total_comments
        ];
    }
    
    include __DIR__ . '/../views/user/authors.php';
}
?>
