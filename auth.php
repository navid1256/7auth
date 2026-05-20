<?php


include "bootstrap/init.php";

if (isset($_GET['action']) && $_GET['action'] == 'register') {
    include 'views/register-view.php';
} elseif (isset($_GET['action']) && $_GET['action'] == 'login') {
    include 'views/login-view.php';
} else {
    echo "Welcome to " . SITE_TITLE . "! Please <a href='?action=register'>register</a> or <a href='?action=login'>login</a>.";
}
