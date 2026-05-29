<?php

function isUserExists(string $email, string $phone): bool
{
    global $pdo;
    $sql = "SELECT * FROM users WHERE email = :email OR phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email ?? '', 'phone' => $phone ?? '']);
    $record = $stmt->fetch(PDO::FETCH_OBJ);
    return $record !== false;
}

function createUser(array $userData)
{
    global $pdo;
    $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone']

        ]
    );
    return $stmt->rowCount() ? true : false;
}

# token generate

function generateToken(): array
{
    global $pdo;
    $hash = bin2hex(random_bytes(16)); // Generates a 32-character hexadecimal token
    $token = rand(100000, 999999); // Generate a random 6-digit token
    $expired_at = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Set expiration time (5 minutes from now)
    $sql = "INSERT INTO tokens (hash, token, expired_at) VALUES (:hash, :token, :expired_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $hash, 'token' => $token, 'expired_at' => $expired_at]);
    return ['hash' => $hash, 'token' => $token, 'expired_at' => $expired_at];
}

function isAliveToken(string $hash): bool
{
    $token = findToken($hash);
    if ($token) {
        return strtotime($token->expired_at) > time();
    }
    return false;
}

function findToken(string $hash): ?object
{
    global $pdo;
    $sql = "SELECT * FROM tokens WHERE hash = :hash";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $hash]);
    return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
}


# send Token to 

function sendTokenByMail(string $email, string|int $token): bool
{
    global $mail;
    try {
        // Clear previous recipients
        $mail->clearAllRecipients();
        $mail->addAddress($email);
        $mail->Subject = '7Auth Verify Token';
        $mail->Body = "Your verification token is: <strong>$token</strong><br>It will expire in 5 minutes.";
        $mail->AltBody = "Your verification token is: $token. It will expire in 5 minutes.";
        return $mail->send();
    } catch (Exception $e) {
        error_log("Exception sending token email to $email: " . $e->getMessage());
        return false;
    }
}

function changeLogginSession(string $session, string $email): bool
{
    global $pdo;
    $sql = 'UPDATE `users` SET `session` = :session WHERE `email` = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':session' => $session, ':email' => $email]);
    return $stmt->rowCount() ? true : false;
}

function deleteToken(string $hash): bool
{
    global $pdo;
    $sql = "DELETE FROM tokens WHERE hash = :hash";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $hash]);
    return $stmt->rowCount() ? true : false;
}

function getAuthenticatedUserBySession(string $session): ?object
{
    global $pdo;
    $sql = "SELECT * FROM users WHERE session = :session";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':session' => $session]);
    return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
}

function isLoggedIn(): bool
{
    if (isset($_COOKIE['session'])) {
        $user = getAuthenticatedUserBySession($_COOKIE['session']);
        return $user !== null;
    }
    return false;
}
