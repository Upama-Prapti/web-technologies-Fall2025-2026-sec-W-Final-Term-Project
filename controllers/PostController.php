<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/LikeModel.php';

class PostController {
    private $postModel;
    private $commentModel;
    private $likeModel;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->postModel = new PostModel($conn);
        $this->commentModel = new CommentModel($conn);
        $this->likeModel = new LikeModel($conn);
    }
    function post_home() {
    global $conn;
    $user_id = $_SESSION['user_id'] ?? '';
    
    $postModel = new PostModel($conn);
    $commentModel = new CommentModel($conn);
    $likeModel = new LikeModel($conn);
    
    $fetch_profile = null;
    $total_user_comments = 0;
    $total_user_likes = 0;
    if($user_id) {
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel($conn);
        $fetch_profile = $userModel->getUserById($user_id);
        if($fetch_profile) {
            $total_user_comments = count($commentModel->getCommentsByUser($user_id));
            $total_user_likes = count($likeModel->getLikesByUser($user_id));
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
    
    $posts = $postModel->getPostsByStatus('active', 6);
    
    foreach($posts as &$post) {
        $post['comments_count'] = count($commentModel->getCommentsByPost($post['id']));
        $post['likes_count'] = $likeModel->getLikeCount($post['id']);
        $post['has_liked'] = $user_id ? $likeModel->hasLiked($user_id, $post['id']) : false;
    }
    
    require_once __DIR__ . '/../components/like_post.php';
    include __DIR__ . '/../views/user/home.php';
}

    public function allPosts() {
        $user_id = $_SESSION['user_id'] ?? '';
        
        require_once __DIR__ . '/../components/like_post.php';
        
        $posts = $this->postModel->getPostsByStatus('active');
        
        foreach($posts as &$post) {
            $post['comments_count'] = count($this->commentModel->getCommentsByPost($post['id']));
            $post['likes_count'] = $this->likeModel->getLikeCount($post['id']);
            $post['has_liked'] = $user_id ? $this->likeModel->hasLiked($user_id, $post['id']) : false;
        }
        
        include __DIR__ . '/../views/user/posts.php';
    }
    
    public function viewPost() {
        global $conn;
        $user_id = $_SESSION['user_id'] ?? '';
        $post_id = intval($_GET['post_id'] ?? 0);
        
        $post = $this->postModel->getPostById($post_id);
        if(!$post || $post['status'] != 'active') {
            header('location: index.php?route=home');
            exit;
        }
        
        $post['comments'] = $this->commentModel->getCommentsByPost($post_id);
        $post['comments_count'] = count($post['comments']);
        $post['likes_count'] = $this->likeModel->getLikeCount($post_id);
        $post['has_liked'] = $user_id ? $this->likeModel->hasLiked($user_id, $post_id) : false;
        
        // Get user profile if logged in
        $fetch_profile = null;
        if($user_id) {
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel($conn);
            $fetch_profile = $userModel->getUserById($user_id);
        }
        
        // Handle comment submission
        $message = [];
        if(isset($_POST['add_comment']) && $user_id) {
            $admin_id = intval($_POST['admin_id'] ?? 0);
            $user_name = $fetch_profile['name'] ?? '';
            $comment = trim($_POST['comment'] ?? '');
            
            if(!empty($comment)) {
                $this->commentModel->createComment($post_id, $admin_id, $user_id, $user_name, $comment);
                $message[] = 'new comment added!';
                header('location: index.php?route=post&post_id=' . $post_id);
                exit;
            }
        }
        
        // Handle edit comment
        if(isset($_POST['edit_comment']) && $user_id) {
            $edit_comment_id = intval($_POST['edit_comment_id'] ?? 0);
            $comment_edit_box = trim($_POST['comment_edit_box'] ?? '');
            
            global $conn;
            $stmt = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $comment_edit_box, $edit_comment_id, $user_id);
            if($stmt->execute()){
                $message[] = 'your comment edited successfully!';
                header('location: index.php?route=post&post_id=' . $post_id);
                exit;
            }
        }
        
        // Handle delete comment
        if(isset($_POST['delete_comment']) && $user_id) {
            $delete_comment_id = intval($_POST['comment_id'] ?? 0);
            global $conn;
            $stmt = $conn->prepare("DELETE FROM `comments` WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $delete_comment_id, $user_id);
            if($stmt->execute()){
                $message[] = 'comment deleted successfully!';
                header('location: index.php?route=post&post_id=' . $post_id);
                exit;
            }
        }
        
        // Refresh comments after actions
        $post['comments'] = $this->commentModel->getCommentsByPost($post_id);
        $post['comments_count'] = count($post['comments']);
        
        include __DIR__ . '/../views/user/view_post.php';
    }
    
    public function category() {
        $user_id = $_SESSION['user_id'] ?? '';
        $category = $_GET['category'] ?? '';
        
        require_once __DIR__ . '/../components/like_post.php';
        
        $posts = $this->postModel->getPostsByCategory($category);
        
        foreach($posts as &$post) {
            $post['comments_count'] = count($this->commentModel->getCommentsByPost($post['id']));
            $post['likes_count'] = $this->likeModel->getLikeCount($post['id']);
            $post['has_liked'] = $user_id ? $this->likeModel->hasLiked($user_id, $post['id']) : false;
        }
        
        include __DIR__ . '/../views/user/category.php';
    }
    
    public function authorPosts() {
        $user_id = $_SESSION['user_id'] ?? '';
        $author = $_GET['author'] ?? '';
        
        require_once __DIR__ . '/../components/like_post.php';
        
        $posts = $this->postModel->getPostsByAuthor($author);
        
        foreach($posts as &$post) {
            $post['comments_count'] = count($this->commentModel->getCommentsByPost($post['id']));
            $post['likes_count'] = $this->likeModel->getLikeCount($post['id']);
            $post['has_liked'] = $user_id ? $this->likeModel->hasLiked($user_id, $post['id']) : false;
        }
        
        include __DIR__ . '/../views/user/author_posts.php';
    }
    
    public function search() {
        $user_id = $_SESSION['user_id'] ?? '';
        $searchTerm = $_POST['search_box'] ?? '';
        
        require_once __DIR__ . '/../components/like_post.php';
        
        $posts = $this->postModel->searchPosts($searchTerm);
        
        foreach($posts as &$post) {
            $post['comments_count'] = count($this->commentModel->getCommentsByPost($post['id']));
            $post['likes_count'] = $this->likeModel->getLikeCount($post['id']);
            $post['has_liked'] = $user_id ? $this->likeModel->hasLiked($user_id, $post['id']) : false;
        }
        
        include __DIR__ . '/../views/user/search.php';
    }
}
?>

