<?php

function assets(string $path): string
{
    return site_url('assets/' . $path);
}
function site_url(string $path): string
{
    return BASE_URL . $path;
}

function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function setErrorAndRedirect(string $message, string $url): void
{
    $_SESSION['error'] = $message;
    unset($_SESSION['success']);
    redirect(site_url($url));
}

function setSuccessAndRedirect(string $message, string $url): void
{
    $_SESSION['success'] = $message;
    unset($_SESSION['error']);
    redirect(site_url($url));
}
