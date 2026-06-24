<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Page</title>
    <link rel="stylesheet" type="text/css" href="<?= assets('css/styles.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Section: Design Block -->
    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 align-items-center mb-5 justify-content-center">
                <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                    <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                        7Learn Auth <br />
                        <span style="color: hsl(218, 81%, 75%)">Verify Page</span>
                    </h1>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                    <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                    <div class="card bg-glass">
                        <div class="card-body px-4 py-5 px-md-5">
                            <?php if (!empty($_SESSION['error'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($_SESSION['error']) ?>
                                </div>
                                <?php unset($_SESSION['error'], $_SESSION['success']); ?>
                            <?php elseif (!empty($_SESSION['success'])): ?>
                                <div class="alert alert-success" role="alert">
                                    <?= htmlspecialchars($_SESSION['success']) ?>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <form action="<?= site_url('auth.php?action=verify') ?>" method="post">
                                <!-- Token input -->
                                <div class="form-outline mb-4">
                                    <input type="text" name="token" id="token" class="form-control" required />
                                    <label class="form-label" for="token">Enter Verification Token</label>
                                </div>
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Verify Token
                                </button>
                                <hr>
                                <p>Check your email for the verification token. It will expire in 5 minutes.</p>
                                <button type="button" id="sendAgainBtn" class="btn btn-outline-primary btn-block">
                                    <span id="btnText">Send Again</span>
                                    <span id="btnLoader" style="display:none;">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Sending...
                                    </span>
                                </button>
                                <div id="resendMessage" class="mt-2" style="display:none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.js"></script>
    <script>
        document.getElementById('sendAgainBtn').addEventListener('click', function() {
            var btnText = document.getElementById('btnText');
            var btnLoader = document.getElementById('btnLoader');
            var messageDiv = document.getElementById('resendMessage');
            var btn = this;

            // Disable button and show loader
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
            messageDiv.style.display = 'none';

            fetch('auth.php?action=resend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    messageDiv.style.display = 'block';
                    if (data.success) {
                        messageDiv.className = 'mt-2 alert alert-success';
                        messageDiv.textContent = data.message;
                    } else {
                        messageDiv.className = 'mt-2 alert alert-danger';
                        messageDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    messageDiv.style.display = 'block';
                    messageDiv.className = 'mt-2 alert alert-danger';
                    messageDiv.textContent = 'An error occurred. Please try again.';
                })
                .finally(() => {
                    // Re-enable button
                    btn.disabled = false;
                    btnText.style.display = 'inline-block';
                    btnLoader.style.display = 'none';
                });
        });
    </script>
</body>

</html>
