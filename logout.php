<?php

require 'bootstrap/init.php';

if (!empty($_COOKIE['session'])) {
    $userData = getAuthenticatedUserBySession($_COOKIE['session']);

    if ($userData) {
        logout($userData->email);
    }
}

$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

if (isset($_COOKIE['session'])) {
    setcookie('session', '', time() - 3600, '/');
}

session_destroy();

redirect('auth.php?action=login');
