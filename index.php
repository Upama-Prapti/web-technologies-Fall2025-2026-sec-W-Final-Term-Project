<?php
// Main entry point - Router
require_once __DIR__ . '/config/config.php';

// Get route from URL
$route = $_GET['route'] ?? 'login';
$action = $_GET['action'] ?? '';

// Route to appropriate controller
switch($route) {
    case 'login':
    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if($route == 'login') {
            $controller->login();
        } else {
            $controller->register();
        }
        break;
        
    case 'home':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->home();
        break;
        
    case 'posts':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->allPosts();
        break;
        
    case 'post':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->viewPost();
        break;
        
    case 'category':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->category();
        break;
        
    case 'author':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->authorPosts();
        break;
        
    case 'search':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController();
        $controller->search();
        break;
        
    case 'update':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->updateProfile();
        break;
        
    case 'likes':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->userLikes();
        break;
        
    case 'comments':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->userComments();
        break;
        
    case 'create_post':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->createPost();
        break;
        
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'authors':
        require_once __DIR__ . '/controllers/AuthorController.php';
        $controller = new AuthorController();
        $controller->allAuthors();
        break;
        
    case 'categories':
        require_once __DIR__ . '/controllers/CategoryController.php';
        $controller = new CategoryController();
        $controller->allCategories();
        break;
        
    case 'admin':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $action = $_GET['action'] ?? 'login';
        
        switch($action) {
            case 'login':
                $controller->login();
                break;
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'add_post':
                $controller->addPost();
                break;
            case 'view_posts':
                $controller->viewPosts();
                break;
            case 'edit_post':
                $controller->editPost();
                break;
            case 'read_post':
                $controller->readPost();
                break;
            case 'update_profile':
                $controller->updateProfile();
                break;
            case 'users_accounts':
                $controller->usersAccounts();
                break;
            case 'admin_accounts':
                $controller->adminAccounts();
                break;
            case 'comments':
                $controller->comments();
                break;
            case 'register_admin':
                $controller->registerAdmin();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                $controller->login();
                break;
        }
        break;
        
    default:
        // Default to login
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
}
?>
