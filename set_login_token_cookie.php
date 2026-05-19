<?php

if (isset($_POST["login_token"])) {
    // Cookie Daten
    $cookie_name = "login_token";
    $cookie_value = $_POST["login_token"];
    $expire_time = time() + 7800000; // ca 3 Monate in Sekunden

    $cookie_options = [
        'expires'  => $expire_time, // Haltbarkeitszeit des Keks (sehr lange)
        'path'     => '/', // Alle Unterseiten dürfen den Keks probieren
        'domain'   => '', // Unsere Seite liegt hier
        'secure'   => true, // klingt gut
        'httponly' => true, // Damit js den Keks nicht klaut
        'samesite' => 'Lax' // Damit keine anderen Websites den Keks klauen
    ];

    // 3. Das Cookie setzen
    setcookie($cookie_name, $cookie_value, $cookie_options);
}
?>