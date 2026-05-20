<?php


include "bootstrap/init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_GET['action'];
    $params = $_POST;
    if ($action == 'register') {
        if (empty($params['name']) || empty($params['phone']) || empty($params['email'])) {
            setErrorAndRedirect('All fields are required.', 'auth.php?action=register');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Invalid email format.', 'auth.php?action=register');
        }
        if (!preg_match('/^[0-9]{10}$/', $params['phone'])) {
            setErrorAndRedirect('Phone number must be 10 digits.', 'auth.php?action=register');
        }
        if (isUserExists($params['email'], $params['phone'])) {
            setErrorAndRedirect('User with this email or phone already exists.', 'auth.php?action=register');
        }
        if(createUser($params)){
            setSuccessAndRedirect('Registration successful! Please login.', 'auth.php?action=login');
        } else {
            setErrorAndRedirect('Registration failed. Please try again.', 'auth.php?action=register');
        }
    } elseif ($action == 'login') {
        if (empty($params['email'])) {
            setErrorAndRedirect('Email is required.', 'auth.php?action=login');
        }
        // Here you would typically check the user's credentials against the database
        setSuccessAndRedirect('Login successful! Welcome back.', 'auth.php?action=login');
    } else {
        setErrorAndRedirect('Invalid action.', 'auth.php');
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'register') {
    include 'views/register-view.php';
} elseif (isset($_GET['action']) && $_GET['action'] == 'login') {
    include 'views/login-view.php';
} else {
    echo "Welcome to " . SITE_TITLE . "! Please <a href='?action=register'>register</a> or <a href='?action=login'>login</a>.";
}
