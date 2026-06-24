<?php

$userData = $userData ?? null;
$displayName = $userData ? htmlspecialchars(($userData->name ?? '') ?: ($userData->email ?? 'User')) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>7Auth - Home</title>
    <link rel="stylesheet" type="text/css" href="<?= assets('css/styles.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.css" rel="stylesheet" />
</head>

<body>
    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 align-items-center mb-5 justify-content-center">
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                        7Learn Auth <br />
                        <span style="color: hsl(218, 81%, 75%)">Home Page</span>
                    </h1>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5 text-center">
                            <?php if (!empty($_SESSION['success'])): ?>
                                <div class="alert alert-success" role="alert">
                                    <?= htmlspecialchars($_SESSION['success']) ?>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>

                            <?php if ($userData): ?>
                                <h2 class="fw-bold mb-3">Welcome, <?= $displayName ?>!</h2>
                                <p class="text-muted mb-4">You are successfully logged in.</p>
                                <a href="<?= site_url('logout.php') ?>" class="btn btn-outline-danger btn-block">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </a>
                            <?php else: ?>
                                <h2 class="fw-bold mb-3">Welcome to 7Auth!</h2>
                                <p class="text-muted mb-4">Please login or register to continue.</p>
                                <a href="<?= site_url('auth.php?action=login') ?>" class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-sign-in-alt me-1"></i> Login
                                </a>
                                <a href="<?= site_url('auth.php?action=register') ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.js"></script>
</body>

</html>
