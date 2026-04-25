<?php
session_start();

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireUser() {
    if (!isUserLoggedIn()) {
        header("Location: /user/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header("Location: /admin/login.php");
        exit();
    }
}
?>
