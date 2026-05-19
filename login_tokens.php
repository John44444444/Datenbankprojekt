<?php
require_once __DIR__ . '/setup.php';

function generate_login_token($username) {
    $connection = connect_to_database();
    if ($connection instanceof Throwable) {
        http_response_code(500);
        exit();
    } else {
        $token = bin2hex(random_bytes(16)); // Zufälliges Token
        $sql = $connection->prepare("INSERT INTO login_tokens (username, token) VALUES (?, ?)");
        $sql->bind_param("ss", $username, $token);
        $sql->execute();
        if ($sql->affected_rows > 0) {
            return $token; // Rückgabe des generierten Tokens
        } else {
            return null; // Fehler bei der Token-Erstellung
        }
    }
}

function check_login_token($token) {
    $connection = connect_to_database();
    if ($connection instanceof Throwable) {
        http_response_code(500);
        exit();
    } else {
        $sql = $connection->prepare("SELECT username FROM login_tokens WHERE token = ?");
        $sql->bind_param("s", $token);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['username']; // Benutzername wird zurückgegeben, wenn das Token gülzig ist
        } else {
            return null; // Ungültiges Token
        }
    }
}
?>