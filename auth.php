<?php

include "bootstrap/init.php";

$action = $_GET['action'] ?? '';

// Skip auth check for AJAX resend requests.
if ($action !== 'resend' && isLoggedIn()) {
    redirect('index.php');
}

deleteExpiredTokens();

function sendVerificationToken(string $email): bool
{
    if (!empty($_SESSION['hash'])) {
        deleteToken($_SESSION['hash']);
        unset($_SESSION['hash']);
    }

    $tokenData = generateToken();

    if (sendTokenByMail($email, $tokenData['token'])) {
        $_SESSION['hash'] = $tokenData['hash'];
        return true;
    }

    deleteToken($tokenData['hash']);
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = $_POST;

    if ($action === 'register') {
        if (empty($params['name']) || empty($params['phone']) || empty($params['email'])) {
            setErrorAndRedirect('All fields are required.', 'auth.php?action=register');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Invalid email format.', 'auth.php?action=register');
        }
        if (!preg_match('/^[0-9]{10,11}$/', $params['phone'])) {
            setErrorAndRedirect('Phone number must be 10 or 11 digits.', 'auth.php?action=register');
        }
        if (isUserExists($params['email'], $params['phone'])) {
            setErrorAndRedirect('User with this email or phone already exists.', 'auth.php?action=register');
        }
        if (createUser($params)) {
            $_SESSION['email'] = $params['email'];
            $_SESSION['phone'] = $params['phone'];
            redirect('auth.php?action=verify');
        }

        setErrorAndRedirect('Registration failed. Please try again.', 'auth.php?action=register');
    }

    if ($action === 'login') {
        if (empty($params['email'])) {
            setErrorAndRedirect('Email is required.', 'auth.php?action=login');
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('Invalid email format.', 'auth.php?action=login');
        }

        if (!isUserExists($params['email'], '')) {
            setErrorAndRedirect('Invalid email. Please register or try again.', 'auth.php?action=login');
        }

        $_SESSION['email'] = $params['email'];

        if (sendVerificationToken($params['email'])) {
            setSuccessAndRedirect('Login successful! Check your email for the verification token.', 'auth.php?action=verify-email');
        }

        setErrorAndRedirect('Failed to send verification email. Please use Send Again.', 'auth.php?action=verify-email');
    }

    if ($action === 'resend') {
        header('Content-Type: application/json');

        if (empty($_SESSION['email'])) {
            echo json_encode(['success' => false, 'message' => 'No active session. Please login again.']);
            exit;
        }

        if (sendVerificationToken($_SESSION['email'])) {
            echo json_encode(['success' => true, 'message' => 'A new verification token has been sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
        }

        exit;
    }

    if ($action === 'verify') {
        if (empty($_SESSION['email']) || empty($_SESSION['hash'])) {
            setErrorAndRedirect('No active verification request. Please login again.', 'auth.php?action=login');
        }

        $hash = $_SESSION['hash'];
        $token = findToken($hash);
        $submittedToken = trim((string) ($params['token'] ?? ''));

        if ($token && (string) $token->token === $submittedToken && isAliveToken($hash)) {
            $session = bin2hex(random_bytes(32));
            changeLogginSession($session, $_SESSION['email']);
            setcookie('session', $session, time() + (86400 * 30), "/");
            deleteToken($hash);
            unset($_SESSION['hash'], $_SESSION['email'], $_SESSION['phone']);
            setSuccessAndRedirect('Token verified successfully! You are now logged in.', 'index.php');
        }

        setErrorAndRedirect('Invalid or expired token. Please try again.', 'auth.php?action=verify-email');
    }
}

if (in_array($action, ['verify', 'verify-email'], true) && !empty($_SESSION['email'])) {
    if (!isUserExists($_SESSION['email'], '')) {
        setErrorAndRedirect('User Not Exists with this data!', 'auth.php?action=login');
    }

    if (empty($_SESSION['hash']) || !isAliveToken($_SESSION['hash'])) {
        if (!sendVerificationToken($_SESSION['email'])) {
            $_SESSION['error'] = 'Failed to send verification email. Please try again.';
            unset($_SESSION['success']);
        } else {
            $_SESSION['success'] = $_SESSION['success'] ?? 'Verification token sent. Check your email.';
            unset($_SESSION['error']);
        }
    }

    include 'views/verify-view.php';
    exit;
}

switch ($action) {
    case 'register':
        include 'views/register-view.php';
        break;
    case 'login':
        include 'views/login-view.php';
        break;
    case 'verify-email':
        setErrorAndRedirect('No active verification request. Please login again.', 'auth.php?action=login');
        break;
    default:
        echo "Welcome to " . SITE_TITLE . "! Please <a href='?action=register'>register</a> or <a href='?action=login'>login</a>.";
        break;
}
