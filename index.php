<?php

require 'bootstrap/init.php';

if (!isLoggedIn()) {
    redirect('auth.php?action=login');
}

$userData = getAuthenticatedUserBySession($_COOKIE['session']);

if (!$userData) {
    setcookie('session', '', time() - 3600, '/');
    redirect('auth.php?action=login');
}

include 'views/index-view.php';
