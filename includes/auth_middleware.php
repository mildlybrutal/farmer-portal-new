<?php
function check_auth() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: /auth/login.php");
        exit();
    }
}

function check_farmer_auth() {
    check_auth();
    if ($_SESSION['user_type'] !== 'farmer') {
        header("Location: /index.php");
        exit();
    }
}

function check_retailer_auth() {
    check_auth();
    if ($_SESSION['user_type'] !== 'retailer') {
        header("Location: /index.php");
        exit();
    }
}
