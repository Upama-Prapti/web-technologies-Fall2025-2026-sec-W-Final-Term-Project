<?php
require_once __DIR__ . '/config/config.php';

$route = $_GET['route'] ?? 'login';
$action = $_GET['action'] ?? '';

switch($route) {
    case 'login':
    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        if($route == 'login') {
            auth_login();
        } else {
            auth_register();
        }
        break;
        
    case 'home':
        require_once __DIR__ . '/controllers/PostController.php';
        post_home();
        break;
        
    case 'posts':
        require_once __DIR__ . '/controllers/PostController.php';
        post_all_posts();
        break;
        
    case 'post':
        require_once __DIR__ . '/controllers/PostController.php';
        post_view_post();
        break;
        
    case 'category':
        require_once __DIR__ . '/controllers/PostController.php';
        post_category();
        break;
        
    case 'author':
        require_once __DIR__ . '/controllers/PostController.php';
        post_author_posts();
        break;
        
    case 'search':
        require_once __DIR__ . '/controllers/PostController.php';
        post_search();
        break;
        
    case 'update':
        require_once __DIR__ . '/controllers/UserController.php';
        user_update_profile();
        break;
        
    case 'likes':
        require_once __DIR__ . '/controllers/UserController.php';
        user_likes();
        break;
        
    case 'comments':
        require_once __DIR__ . '/controllers/UserController.php';
        user_comments();
        break;
        
    case 'create_post':
        require_once __DIR__ . '/controllers/UserController.php';
        user_create_post();
        break;
        
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        auth_logout();
        break;
        
    case 'authors':
        require_once __DIR__ . '/controllers/AuthorController.php';
        author_all_authors();
        break;
        
    case 'categories':
        require_once __DIR__ . '/controllers/CategoryController.php';
        category_all_categories();
        break;
        
    case 'admin':
        require_once __DIR__ . '/controllers/AdminController.php';
        $action = $_GET['action'] ?? 'login';
        
        switch($action) {
            case 'login':
                admin_login();
                break;
            case 'dashboard':
                admin_dashboard();
                break;
            case 'add_post':
                admin_add_post();
                break;
            case 'view_posts':
                admin_view_posts();
                break;
            case 'edit_post':
                admin_edit_post();
                break;
            case 'read_post':
                admin_read_post();
                break;
            case 'update_profile':
                admin_update_profile();
                break;
            case 'users_accounts':
                admin_users_accounts();
                break;
            case 'admin_accounts':
                admin_admin_accounts();
                break;
            case 'comments':
                admin_comments();
                break;
            case 'register_admin':
                admin_register_admin();
                break;
            case 'logout':
                admin_logout();
                break;
            default:
                admin_login();
                break;
        }
        break;
        
    default:
        require_once __DIR__ . '/controllers/AuthController.php';
        auth_login();
        break;
}
?>
