<?php
require_once __DIR__ . '/setup.php';

if (isset($_POST['username'])) {
    $db = connect_to_database();
    $user = $_POST['username'];

    $stmt = $db->prepare("SELECT username FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    // Wenn mehr als 0 Zeilen gefunden wurden, ist der Name vergeben
    if ($stmt->num_rows > 0) {
        echo "taken";
    } else {
        echo "available";
    }
    $stmt->close();
    $db->close();
}
?>