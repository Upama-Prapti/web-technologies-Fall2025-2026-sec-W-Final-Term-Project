<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AdminModel.php';

class AuthController {
    private $userModel;
    private $adminModel;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userModel = new UserModel($conn);
        $this->adminModel = new AdminModel($conn);
    }
    
    public function login() {
        global $conn;
        $message = [];
        
        if (isset($_POST['submit'])) {
            $loginType = $_POST['login_type'] ?? 'user';
            
            if ($loginType === 'admin') {
                // Admin login - check both admin table and users table with is_admin=1
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
                // User login - using username (name field) instead of email
                $username = trim($_POST['email']); // Using email field name for backward compatibility
                $pass = sha1($_POST['pass']);

                $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ? AND password = ?");
                $stmt->bind_param("ss", $username, $pass);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if($result->num_rows > 0){
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['is_admin'] = $row['is_admin'] ?? 0;
                    
                    // Always redirect to home page for user login (even if they are admin)
                    // They can access admin panel separately through admin login
                    header('location: index.php?route=home');
                    exit;
                }else{
                    $message[] = 'incorrect username or password!';
                }
            }
        }
        
        include __DIR__ . '/../views/user/login.php';
    }
    
    public function register() {
        global $conn;
        $message = [];
        
        if (isset($_POST['submit'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $pass = sha1($_POST['pass']);
            $cpass = sha1($_POST['cpass']);
            
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
                $message[] = 'email already exists!';
            }else{
                if($pass != $cpass){
                    $message[] = 'confirm password not matched!';
                }else{
                    $stmt = $conn->prepare("INSERT INTO `users`(name, email, password, is_admin) VALUES(?,?,?,0)");
                    $stmt->bind_param("sss", $name, $email, $cpass);
                    if($stmt->execute()){
                        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
                        $stmt->bind_param("ss", $email, $pass);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        if($result->num_rows > 0){
                            $_SESSION['user_id'] = $row['id'];
                            $_SESSION['user_name'] = $row['name'];
                            header('location: index.php?route=home');
                            exit;
                        }
                    }
                }
            }
        }
        
        include __DIR__ . '/../views/user/register.php';
    }
    
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('location: index.php?route=login');
        exit;
    }
}
?>

