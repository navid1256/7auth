<?php

include "bootstrap/init.php";
if (isLoggedIn()) {
    redirect('auth.php');
}

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
        if (!preg_match('/^[0-9]{10}|[0-9]{11}$/', $params['phone'])) {
            setErrorAndRedirect('Phone number must be 10 or 11 digits.', 'auth.php?action=register');
        }
        if (isUserExists($params['email'], $params['phone'])) {
            setErrorAndRedirect('User with this email or phone already exists.', 'auth.php?action=register');
        }
        if (createUser($params)) {
            $_SESSION['email'] = $params['email'];
            $_SESSION['phone'] = $params['phone'];
            redirect('auth.php?action=verify');
        } else {
            setErrorAndRedirect('Registration failed. Please try again.', 'auth.php?action=register');
        }
    } elseif ($action == 'login') {
        if (empty($params['email'])) {
            setErrorAndRedirect('Email is required.', 'auth.php?action=login');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Invalid email format.', 'auth.php?action=login');
        }
        // Check the user's credentials against the database
        if (isUserExists($params['email'], '')) {
            $_SESSION['email'] = $params['email'];
            $tokenData = generateToken();
            $_SESSION['token'] = $tokenData;
            // Send the token to the user's email
            if (sendTokenByMail($params['email'], $tokenData['token'])) {
                setSuccessAndRedirect('Login successful! Check your email for the verification token.', 'auth.php?action=verify-email');
            } else {
                setSuccessAndRedirect('Login successful but email sending failed. Please try again later.', 'auth.php?action=login');
            }
        } else {
            setErrorAndRedirect('Invalid email. Please register or try again.', 'auth.php?action=login');
        }
    } elseif ($action == 'resend') {
        // Handle resend token request
        header('Content-Type: application/json');
        if (empty($_SESSION['email'])) {
            echo json_encode(['success' => false, 'message' => 'No active session. Please login again.']);
            exit;
        }
        $tokenResult = generateToken();
        if (sendTokenByMail($_SESSION['email'], $tokenResult['token'])) {
            $_SESSION['hash'] = $tokenResult['hash'];
            echo json_encode(['success' => true, 'message' => 'A new verification token has been sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
        }
        exit;
    } elseif ($action == 'verify') {
        // Handle token verification from form submission
        $token = findToken($_SESSION['token']['hash']);
        if ($token && $token->token === $params['token'] && isAliveToken($_SESSION['token']['hash'])) {
            $session = bin2hex(random_bytes(32)); // Generate a random session token
            changeLogginSession($session, $_SESSION['email']);
            setcookie('session', $session, time() + (86400 * 30), "/"); // Set cookie for 30 days
            deleteToken($_SESSION['token']['hash']);
            redirect('dashboard.php');
            unset($_SESSION['token'], $_SESSION['hash'], $_SESSION['email'], $_SESSION['phone']);
            setSuccessAndRedirect('Token verified successfully! You are now logged in.', 'auth.php');
        } else {
            setErrorAndRedirect('Invalid or expired token. Please try again.', 'auth.php?action=verify-email');
        }
    }
}

// Handle GET requests - display pages
if (isset($_GET['action']) && $_GET['action'] === 'verify' && !empty($_SESSION['email'])) {
    if (!isUserExists($_SESSION['email'], '')) {
        setErrorAndRedirect('User Not Exists with this data!', 'auth.php?action=login');
    }

    if (isset($_SESSION['hash']) && isAliveToken($_SESSION['hash'])) {
        sendTokenByMail($_SESSION['email'], findToken($_SESSION['hash'])->token);
    } else {
        $tokenResult = generateToken();
        sendTokenByMail($_SESSION['email'], $tokenResult['token']);
        $_SESSION['hash'] = $tokenResult['hash'];
    }

    include 'tpl/verify-tpl.php';
    exit;
}

switch ($_GET['action'] ?? '') {
    case 'register':
        include 'views/register-view.php';
        break;
    case 'login':
        include 'views/login-view.php';
        break;
    case 'verify-email':
        include 'views/verify-view.php';
        break;
    default:
        echo "Welcome to " . SITE_TITLE . "! Please <a href='?action=register'>register</a> or <a href='?action=login'>login</a>.";
        break;
}
