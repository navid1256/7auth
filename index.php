<?php

require 'bootstrap/init.php';

$userData = null;

if (isLoggedIn()) {
    $userData = getAuthenticatedUserBySession($_COOKIE['session']);
} elseif (isset($_COOKIE['session'])) {
    setcookie('session', '', time() - 3600, '/');
}

include 'views/index-view.php';
