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
    $hash = bin2hex(random_bytes(random_bytes(16))); // Generates a 32-character hexadecimal token
    $token = rand(100000, 999999); // Generate a random 6-digit token
    $expired_at = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Set expiration time (e.g., 15 minutes from now)
    $sql = "INSERT INTO tokens (hash, token, expiration) VALUES (:hash, :token, :expired_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $hash, 'token' => $token, 'expired_at' => $expired_at]);
    return ['hash' => $hash, 'token' => $token, 'expired_at' => $expired_at];
}

function isAliveToken(string $hash): bool
{
    $token = findToken($hash);
    if ($token) {
        return strtotime($token->expiration) > time();
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

#verify token