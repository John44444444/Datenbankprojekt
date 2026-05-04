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

// Todo: Registrierungslogik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST["username"]) >= 3 && $_POST["display_name"] !== "" && $_POST["email"] !== "" && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["password"]) >= 8 && preg_match('/\d/', $_POST["password"]) && preg_match('/[A-Z]/', $_POST["password"]) && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $_POST["password"])) {
        $db = connect_to_database();
        if ($db instanceof Throwable) {
            http_response_code(500);
            exit();
        } else {
            $verification_code = random_int(100000, 999999);
            $sql = $db->prepare("INSERT INTO user (username, displayname, password, email, verification_code, verification_expires) VALUES (?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $hashed_verification_code = password_hash($verification_code, PASSWORD_DEFAULT);
            $sql->bind_param("ssssss", $_POST['username'], $_POST['display_name'], $hashed_password, $_POST['email'], $hashed_verification_code, date('Y-m-d H:i:s', time() + 15 * 60));
            $sql->execute();
            $sql->get_result();
            $verification_code = (string) $verification_code;
            $to = $_POST["email"];
            $subject = "Registrierung best&#228;tigen | FragUns";
            $message = '<!Doctype html>
<html lang=de>
    <head>
        <meta charset=UTF-8>
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <title>FragUns - Registrieren</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    </head>
    <body style="text-align: center;
    background-color: var(--bg-color);
    color: var(--text-color);
    font-family: "Varela Round", sans-serif;
    font-weight: 400;
    font-style: normal;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;">
        <header style="background-color: #ffffff36;
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px auto;"><h1>FragUns benötigt Ihre Bestätigung</h1></header>
        <main>
            <div class="card" style="width: 100%;
    background-color: #ffffff36;
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px auto;
    min-height: 50px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;">
                <h2>Schließen Sie jetzt Ihre Registrierung ab</h2>
                <table width="250px;">
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[0] . '</h1></td>
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[1] . '</h1></td>
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[2] . '</h1></td>
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[3] . '</h1></td>
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[4] . '</h1></td>
                    <td style="margin: 10px; border-radius: 10px; border: 2px solid black;"><h1>' . $verification_code[5] . '</h1></td>
                </table>
                <a href="http://fraguns.bplaced.net/datenbankprojekt/verify.php?username=' . $_POST['username'] . '&code=' . $verification_code . '"  style="color:black; text-decoration: none;">
   Registrierung bestätigen
</a>
                <h3>Das waren Sie nicht?</h3>
                <p>Sie können diese E-Mail löschen. Die Registrierung wird automatisch abgebrochen und ihre E-Mail-Adresse wird aus der Datenbank entfernt.</p>
            </div>
        </main>
        <footer>
            <a href="http://fraguns.bplaced.net/" style="color:black; text-decoration: none;">FragUns</a> |
            <a href="http://fraguns.bplaced.net/datenbankprojekt/impressum.html" style="color:black; text-decoration: none;">Impressum</a> |
            <a href="mailto:luckyart@gmx.de" style="color:black; text-decoration: none;">Kontakt</a>
        </footer>
    </body>
</html>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: absender@example.com";
            mail($to, $subject, $message, $headers);
            header("Location: verify.php?username=" . $_POST['username']);
            exit;
        }
    } else {
        echo "<script>alert('Bitte überprüfen Sie die eingegebenen Daten.')</script>";
    }
}

?>

<!Doctype html>
<html lang=de>
    <head>
        <meta charset=UTF-8>
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <title>FragUns - Registrieren</title>
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
                <form id="form" method="post">
                    <input id="username" name="username" placeholder="Benutzername">
                    <span id="username-status" style="color: red;"></span>
                    <input id="display_name" name="display_name" placeholder="Anzeigename">
                    <span id="display-name-status" style="color: red;"></span>
                    <input id="email"name="email" placeholder="E-Mail" type="email">
                    <span id="email-status" style="color: red;"></span>
                    <input id="password" name="password" placeholder="Passwort" type="password">
                    <span id="password-status" style="color: red;"></span>
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAADAsYxWSwYU4ejyd"></div>
                    <button class="option" type="submit"><p>Registrieren</p></button>
                </form>
            </div>
        </main>
        <nav>
            <a>Link1</a>
            <a>Link2</a>
            <a>Link3</a>
        </nav>
        <footer>
            <a href="impressum.html">Impressum</a>
        </footer>
        <script>
            // Testen von Verfügbarkeit des Benutzernamens und angemessener Sicherheit des Passworts
            const formInput = document.getElementById('form');
            const usernameInput = document.getElementById('username');
            const statusDisplay = document.getElementById('username-status');
            const displayNameInput = document.getElementById('display_name');
            const statusDisplayName = document.getElementById('display-name-status');
            const passwordInput = document.getElementById('password');
            const statusPassword = document.getElementById('password-status');
            const emailInput = document.getElementById('email');
            const statusEmail = document.getElementById('email-status');

            // Verfügbarkeit des Benutzernamens
            usernameInput.addEventListener('input', function() {
                const username = this.value;

                if (username.length === 0) {
                    statusDisplay.textContent = "";
                    return;
                } else if (username.length < 3) {
                    statusDisplay.textContent = "Mindestens 3 Zeichen erforderlich.";
                    return;
                }

                const formData = new FormData();
                formData.append('username', username);

                // Anfrage an check-username.php senden, um die Verfügbarkeit zu überprüfen
                fetch('check-username.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "taken") {
                        statusDisplay.textContent = "Bereits vergeben.";
                    } else {
                        statusDisplay.textContent = "";
                    }
                })
                .catch(error => console.error('Fehler:', error));
            });

            
            // Passwortsicherheit
            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.length === 0 || passwordInput.value.length >= 8) { // Mindestlänge von 8 Zeichen
                    statusPassword.textContent = "";
                }
            });

            // Gültige E-Mail-Adresse
            emailInput.addEventListener('input', function() {
                if (this.value.length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                    statusEmail.textContent = "";
                }
            });

            // Anzeigename
            displayNameInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    statusDisplayName.textContent = "";
                }
            });

            
            formInput.addEventListener('submit', function(event) {
                event.preventDefault();
                console.log(passwordInput)

                everythingValid = true;

                // Passwortsicherheit
                if (passwordInput.value.length === 0) { // Passwort fehlt
                    statusPassword.textContent = "Bitte geben Sie ein Passwort ein."
                    everythingValid = false
                } else if (passwordInput.value.length < 8) { // Mindestlänge von 8 Zeichen
                    statusPassword.textContent = "Mindestens 8 Zeichen erforderlich.";
                    everythingValid = false;
                } else if (/\d/.test(passwordInput.value) === false) { // Mindestens eine Zahl
                    statusPassword.textContent = "Mindestens eine Zahl erforderlich.";
                    everythingValid = false;
                } else if (/[A-Z]/.test(passwordInput.value) === false) { // Mindestens ein Großbuchstabe
                    statusPassword.textContent = "Mindestens ein Großbuchstabe erforderlich.";
                    everythingValid = false;
                } else if (/[!@#$%^&*(),.?":{}|<>]/.test(passwordInput.value) === false) { // Mindestens ein Sonderzeichen
                    statusPassword.textContent = "Mindestens ein Sonderzeichen erforderlich.";
                    everythingValid = false;
                }

                // Gültige E-Mail-Adresse
                if (emailInput.value.length === 0) {
                    statusEmail.textContent = "Bitte geben Sie eine E-Mail-Adresse ein."
                    everythingValid = false
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                    statusEmail.textContent = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
                    everythingValid = false;
                }

                // Anzeigename
                if (displayNameInput.value.length === 0) {
                    statusDisplayName.textContent = "Bitte geben sie einen Anzeigenamen ein.";
                    everythingValid = false;
                }

                if (everythingValid === true) {
                    formInput.submit();
                }
            });
        </script>
    </body>
</html>
