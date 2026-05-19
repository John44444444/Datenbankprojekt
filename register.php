<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';
require __DIR__ . '/send_verify_mail.php';

// Todo: Registrierungslogik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST["username"]) >= 3 && $_POST["display_name"] !== "" && $_POST["email"] !== "" && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["password"]) >= 8 && preg_match('/\d/', $_POST["password"]) && preg_match('/[A-Z]/', $_POST["password"]) && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $_POST["password"])) {
        $db = connect_to_database();
        if ($db instanceof Throwable) {
            http_response_code(500);
            exit();
        } else {
            // Prüfen, ob die E-Mail-Adresse bereits registriert ist
            $sql = $db->prepare("SELECT COUNT(*) AS count FROM user WHERE email=?");
            $sql->bind_param("s", $_POST['email']);
            $sql->execute();
            $result = $sql->get_result();
            $row = $result->fetch_assoc();
            // Wenn E-Mail-Adresse bereits registriert ist, wird die/der Benutzer*in darüber informiert
            if ($row['count'] > 0) {
                $to = $_POST["email"];
                send_already_registered_mail($to);
                header("Location: verify.php?username=" . $_POST['username']);
                exit;
            } else { // Ansonsten wird der/die Benutzer*in normal registriert
                $verification_code = 100000; // random_int(100000, 999999); Temprär auskommentiert zum einfachen lokalen testen
                $sql = $db->prepare("INSERT INTO user (username, displayname, password, email, verification_code, verification_expires) VALUES (?, ?, ?, ?, ?, ?)");
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $hashed_verification_code = password_hash($verification_code, PASSWORD_DEFAULT);
                $sql->bind_param("ssssss", $_POST['username'], $_POST['display_name'], $hashed_password, $_POST['email'], $hashed_verification_code, date('Y-m-d H:i:s', time() + 15 * 60));
                $sql->execute();
                $sql->get_result();
                $to = $_POST["email"];
                $username = $_POST["username"];
                send_verify_mail($to, $verification_code, $username);
                header("Location: verify.php?username=" . $_POST['username']);
                exit;
            }
        }
    } else {
        echo "<script>alert('Bitte überprüfen Sie die eingegebenen Daten.')</script>";
    }
}

?>

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
                    <div style="display:grid; grid-template-columns: 20px 1fr; align-items: center; gap: 10px;">
                        <input id="accept_terms" name="accept_terms" type="checkbox">
                        <label for="accept_terms">Ich habe die <a href="terms.html" style="text-decoration: underline;">Nutzungsbedingungen</a> und die <a href="terms.html" style="text-decoration: underline;">Datenschutzbestimmungen</a> gelesen und akzeptiere sie.</label>
                    </div>
                    <span id="accept-terms-status" style="color: red;"></span>
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAADAsYxWSwYU4ejyd"></div>
                    <button class="option" type="submit"><p>Registrieren</p></button>
                </form>
            </div>
        </main>
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
            const accept_termsInput = document.getElementById('accept_terms');
            const statusAcceptTerms = document.getElementById('accept-terms-status');

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

            // Terms of Service
            accept_termsInput.addEventListener('input', function() {
                if (this.checked) {
                    statusAcceptTerms.textContent = "";
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

                // Terms of Service
                if (!accept_termsInput.checked) {
                    statusAcceptTerms.textContent = "Bitte akzeptieren Sie die Nutzungsbedingungen und Datenschutzbestimmungen.";
                    everythingValid = false;
                }

                if (everythingValid === true) {
                    formInput.submit();
                }
            });
        </script>
    </body>
</html>
