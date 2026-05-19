<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';
require __DIR__ . '/login_tokens.php';


if (isset($_COOKIE['login_token'])) {
    $login_token = $_COOKIE['login_token'];
    $username = check_login_token($login_token);
    
} else {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST["username"]) >= 3) {
        $db = connect_to_database();
        if ($db instanceof Throwable) {
            http_response_code(500);
            exit();
        } else {
            $sql = $db->prepare("SELECT password FROM user WHERE username=?");
            $sql->bind_param("s", $_POST['username']);
            $sql->execute();
            $result = $sql->get_result();
            $row = $result->fetch_assoc();
            if (password_verify($_POST['password'], (string)$row["password"])){
                $token = generate_login_token($_POST['username']);
                if ($token) {
                    echo ("<script>
                        const formData = new FormData();
                        formData.append('login_token', '" . $token . "');

                        fetch('set_login_token_cookie.php', {
                            method: 'POST',
                            body: formData
                        })
                        .catch(error => console.error('Fehler:', error));
                    </script>");
                } else {
                    echo "<script>alert('Fehler beim Generieren des Login-Tokens.')</script>";
                }
            } else {
                echo "<script>alert('Benutzername oder Passwort ist falsch.')</script>";
            }
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
        <title>FragUns - Profil</title>
        <link rel=stylesheet href=style.css>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
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
                <h1>Profil</h1>
                <img src="https://api.dicebear.com/9.x/notionists/svg?seed=<?= $username ?>" alt="Profilbild">
                <p>Benutzername: <?php echo $username;?></p>
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
            const passwordInput = document.getElementById('password');
            const statusPassword = document.getElementById('password-status');
            
            formInput.addEventListener('submit', function(event) {
                event.preventDefault();
                console.log(passwordInput)

                everythingValid = true;

                // Prüflogik Client
                if (usernameInput.value.length === 0) { // Benutzername fehlt
                    statusPassword.textContent = "Bitte geben Sie einen Benutzernamen ein."
                    everythingValid = false
                } else {
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
                            statusDisplay.textContent = "";
                        } else {
                            statusDisplay.textContent = "Der Benutzername ist falsch.";
                            everythingValid = false
                        }
                    })
                    .catch(error => console.error('Fehler:', error));
                }

                if (everythingValid === true) {
                    formInput.submit();
                }
            });
        </script>
    </body>
</html>
