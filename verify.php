<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';

// Setup der Datenbank
$errorCode = setup_database();
if ($errorCode instanceof Throwable) {
    http_response_code(500);
    exit();
} elseif (is_int($errorCode)) {
    http_response_code($errorCode);
    exit();
}

?>

<!Doctype html>
<html lang=de>
    <head>
        <meta charset=UTF-8>
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <title>FragUns - Verifizieren</title>
        <link rel=stylesheet href=style.css>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    </head>
    <body>
        <div class="aurora-wrapper">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
            <div class="blob blob-4"></div>
            <div class="blob blob-5"></div>
            <div class="blob blob-6"></div>
        </div>
        <header><h1>FragUns</h1></header>
        <main>
            <div class="card">
                <h1>Registrieren</h1>
                <br>
                <p>Bitte geben Sie den Code ein, den Sie per E-Mail erhalten haben.</p>
                <form id="form" method="post">
                    <div class="verify-code">
                        <input id="0" class="code-input">
                        <input id="1" class="code-input">
                        <input id="2" class="code-input">
                        <input id="3" class="code-input">
                        <input id="4" class="code-input">
                        <input id="5" class="code-input">
                    </div>
                    <script>
                        // Automatisches Ausfüllen des Codes
                        const id = new URLSearchParams(window.location.search).get('code');
                        if (id !== null && /^\d{6}$/.test(id)){
                            document.getElementById("0").value = id[0] ?? "";
                            document.getElementById("1").value = id[1] ?? "";
                            document.getElementById("2").value = id[2] ?? "";
                            document.getElementById("3").value = id[3] ?? "";
                            document.getElementById("4").value = id[4] ?? "";
                            document.getElementById("5").value = id[5] ?? "";
                        }
                    </script>
                    <button class="option" type="submit"><p>Verifizieren</p></button>
                </form>
                <p><a>Klicken Sie hier um den Code erneut per E-Mail zu senden.</a></p>
            </div>
        </main>
        <footer>
            <a href="impressum.html">Impressum</a>
        </footer>
    </body>
</html>