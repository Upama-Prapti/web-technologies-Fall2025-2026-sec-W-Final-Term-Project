<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AdminModel.php';

function auth_login() {
    global $conn;
    $message = [];
    
    if(isset($_POST['submit'])) {
        $login_type = $_POST['login_type'] ?? 'user';
        
        // Redirect all admin login attempts to the dedicated admin login panel
        if($login_type === 'admin') {
            header('location: index.php?route=admin&action=login');
            exit;
        }
        
        $pass = sha1($_POST['pass']);
        $name = trim($_POST['name']);
        
        // User login - only check users table (not admin table)
        // Users with admin privileges can still login as regular users
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if($row['password'] == $pass){
                // Clear admin session variables when logging in as user
                unset($_SESSION['admin_id']);
                unset($_SESSION['admin_name']);
                unset($_SESSION['is_from_admin_table']);
                
                // Set user session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['is_admin'] = $row['is_admin'] ?? 0;
                header('location: index.php?route=home');
                exit;
            }else{
                $message[] = 'incorrect password!';
            }
        }else{
            $message[] = 'incorrect username!';
        }
    }
    
    include __DIR__ . '/../views/user/login.php';
}

function auth_register() {
    global $conn;
    $message = [];
    
    if(isset($_POST['submit'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $pass = sha1($_POST['pass']);
        $cpass = sha1($_POST['cpass']);
        
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $message[] = 'username already exists!';
        }else{
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $email_result = $stmt->get_result();
            
            if($email_result->num_rows > 0){
                $message[] = 'email already exists!';
            }else{
                $stmt = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $admin_result = $stmt->get_result();
                
                if($admin_result->num_rows > 0){
                    $message[] = 'username already exists!';
                }else{
                if(strlen($_POST['pass']) < 8){
                    $message[] = 'password must be at least 8 characters!';
                }elseif($pass != $cpass){
                    $message[] = 'confirm password not matched!';
                }else{
                    $stmt = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
                    $stmt->bind_param("sss", $name, $email, $cpass);
                    if($stmt->execute()){
                            $stmt = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
                            $stmt->bind_param("s", $name);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if($result->num_rows > 0){
                                $row = $result->fetch_assoc();
                                $_SESSION['user_id'] = $row['id'];
                                $_SESSION['user_name'] = $row['name'];
                                header('location: index.php?route=home');
                                exit;
                            }
                        }else{
                            $message[] = 'registration failed! please try again.';
                        }
                    }
                }
            }
        }
    }
    
    include __DIR__ . '/../views/user/register.php';
}

function auth_logout() {
    session_unset();
    session_destroy();
    header('location: index.php?route=login');
    exit;
}
?>
