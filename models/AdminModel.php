<?php
require_once __DIR__ . '/../config/config.php';

function model_admin_get_by_name($conn, $name) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_admin_get_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function model_admin_change_password($conn, $id, $newPassword) {
    $hashedPassword = sha1($newPassword);
    $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);
    return $stmt->execute();
}

function model_admin_get_all($conn) {
    $result = $conn->query("SELECT * FROM admin ORDER BY id DESC");
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
    return $admins;
}

function model_admin_verify_password($password, $hashedPassword) {
    return sha1($password) === $hashedPassword;
}
?>
