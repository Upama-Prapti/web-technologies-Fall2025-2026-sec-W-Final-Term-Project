<?php
require_once __DIR__ . '/../config/config.php';

function model_user_get_by_email($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_user_get_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_user_create($conn, $name, $email, $password) {
    $hashedPassword = sha1($password);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    return $stmt->execute();
}

function model_user_update($conn, $id, $name, $email) {
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    return $stmt->execute();
}

function model_user_change_password($conn, $id, $newPassword) {
    $hashedPassword = sha1($newPassword);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);
    return $stmt->execute();
}

function model_user_set_admin($conn, $userId, $isAdmin) {
    $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
    $stmt->bind_param("ii", $isAdmin, $userId);
    return $stmt->execute();
}

function model_user_get_all($conn) {
    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

function model_user_verify_password($password, $hashedPassword) {
    return sha1($password) === $hashedPassword;
}
?>
