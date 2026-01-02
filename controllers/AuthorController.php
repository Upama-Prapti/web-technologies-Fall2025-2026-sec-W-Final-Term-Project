<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

class AuthorController {
    private $adminModel;
    private $postModel;
    private $commentModel;
    private $likeModel;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->adminModel = new AdminModel($conn);
        $this->postModel = new PostModel($conn);
        $this->commentModel = new CommentModel($conn);
        $this->likeModel = new LikeModel($conn);
    }
    
    public function allAuthors() {
        $admins = $this->adminModel->getAllAdmins();
        $authors = [];
        
        foreach($admins as $admin) {
            $posts = $this->postModel->getPostsByAdmin($admin['id']);
            $active_posts = array_filter($posts, function($p) { return $p['status'] == 'active'; });
            
            $total_likes = 0;
            $total_comments = 0;
            foreach($active_posts as $post) {
                $total_likes += $this->likeModel->getLikeCount($post['id']);
                $total_comments += count($this->commentModel->getCommentsByPost($post['id']));
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
}
?>

