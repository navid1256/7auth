<?php

function isUserExists(string $email, string $phone): bool
{
    global $pdo;
    $sql = "SELECT * FROM users WHERE email = :email OR phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email, 'phone' => $phone]);
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
