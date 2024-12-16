<?php

function isLoggedIn() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: /login.php');
        exit();
    }
}

function isNotLoggedIn() {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header('Location: /dashboard.php');
        exit();
    }
}

function isAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: /index.php');
        exit();
    }
}

function isUser() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        header('Location: /index.php');
        exit();
    }
}

