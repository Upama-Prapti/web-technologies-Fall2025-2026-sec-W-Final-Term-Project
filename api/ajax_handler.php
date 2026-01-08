<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/LikeModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/PostModel.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

$response = ['success' => false, 'message' => ''];

switch($action) {
    case 'like_post':
        if(empty($user_id)) {
            $response['message'] = 'Please login first!';
            $response['redirect'] = 'index.php?route=login';
        } else {
            $post_id = intval($_POST['post_id'] ?? 0);
            $admin_id = intval($_POST['admin_id'] ?? 0);
            
            if($post_id > 0) {
                $result = model_like_toggle($conn, $user_id, $admin_id, $post_id);
                $new_count = model_like_get_count($conn, $post_id);
                $has_liked = model_like_has_liked($conn, $user_id, $post_id);
                
                $response['success'] = true;
                $response['action'] = $result;
                $response['likes_count'] = $new_count;
                $response['has_liked'] = $has_liked;
                $response['message'] = $result == 'added' ? 'Added to likes!' : 'Removed from likes!';
            } else {
                $response['message'] = 'Invalid post!';
            }
        }
        break;
        
    case 'add_comment':
        if(empty($user_id)) {
            $response['message'] = 'Please login first!';
            $response['redirect'] = 'index.php?route=login';
        } else {
            $post_id = intval($_POST['post_id'] ?? 0);
            $admin_id = intval($_POST['admin_id'] ?? 0);
            $user_name = trim($_POST['user_name'] ?? '');
            $comment = trim($_POST['comment'] ?? '');
            
            if($post_id > 0 && !empty($comment)) {
                if(model_comment_create($conn, $post_id, $admin_id, $user_id, $user_name, $comment)) {
                    $response['success'] = true;
                    $response['message'] = 'Comment added successfully!';
                    $response['comments_count'] = count(model_comment_get_by_post($conn, $post_id));
                } else {
                    $response['message'] = 'Failed to add comment!';
                }
            } else {
                $response['message'] = 'Invalid data!';
            }
        }
        break;
        
    case 'delete_comment':
        if(empty($user_id)) {
            $response['message'] = 'Please login first!';
        } else {
            $comment_id = intval($_POST['comment_id'] ?? 0);
            $comment = model_comment_get_by_id($conn, $comment_id);
            
            if($comment && $comment['user_id'] == $user_id) {
                if(model_comment_delete($conn, $comment_id)) {
                    $response['success'] = true;
                    $response['message'] = 'Comment deleted successfully!';
                } else {
                    $response['message'] = 'Failed to delete comment!';
                }
            } else {
                $response['message'] = 'Unauthorized!';
            }
        }
        break;
        
    case 'edit_comment':
        if(empty($user_id)) {
            $response['message'] = 'Please login first!';
        } else {
            $comment_id = intval($_POST['comment_id'] ?? 0);
            $new_comment = trim($_POST['comment'] ?? '');
            $comment = model_comment_get_by_id($conn, $comment_id);
            
            if($comment && $comment['user_id'] == $user_id && !empty($new_comment)) {
                if(model_comment_update($conn, $comment_id, $new_comment)) {
                    $response['success'] = true;
                    $response['message'] = 'Comment updated successfully!';
                } else {
                    $response['message'] = 'Failed to update comment!';
                }
            } else {
                $response['message'] = 'Invalid data or unauthorized!';
            }
        }
        break;
        
    case 'get_likes_count':
        $post_id = intval($_GET['post_id'] ?? 0);
        if($post_id > 0) {
            $response['success'] = true;
            $response['likes_count'] = model_like_get_count($conn, $post_id);
            $response['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post_id) : false;
        }
        break;
        
    case 'get_comments':
        $post_id = intval($_GET['post_id'] ?? 0);
        if($post_id > 0) {
            $comments = model_comment_get_by_post($conn, $post_id);
            $response['success'] = true;
            $response['comments'] = $comments;
            $response['comments_count'] = count($comments);
        }
        break;
        
    case 'search_posts':
        $search_term = trim($_POST['search'] ?? '');
        if(!empty($search_term)) {
            $posts = model_post_search($conn, $search_term);
            $result_posts = [];
            foreach($posts as $post) {
                $post['likes_count'] = model_like_get_count($conn, $post['id']);
                $post['comments_count'] = count(model_comment_get_by_post($conn, $post['id']));
                $post['has_liked'] = $user_id ? model_like_has_liked($conn, $user_id, $post['id']) : false;
                $result_posts[] = $post;
            }
            $response['success'] = true;
            $response['posts'] = $result_posts;
        }
        break;
        
    default:
        $response['message'] = 'Invalid action!';
        break;
}

echo json_encode($response);
?>
