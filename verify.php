<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';
require __DIR__ . "/send_verify_mail.php";

// Setup der Datenbank
$errorCode = setup_database();
if ($errorCode instanceof Throwable) {
    http_response_code(500);
    exit();
} elseif (is_int($errorCode)) {
    http_response_code($errorCode);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_GET['username'];
    $db = connect_to_database();
    if ($db instanceof Throwable) {
        http_response_code(500);
        exit();
    } else if (isset($_GET["resend_mail"])){
        $username = $_GET["username"] ?? "";
        $verification_code = random_int(100000, 999999);
        $hashed_verification_code = password_hash($verification_code, PASSWORD_DEFAULT);
        // Email-Adresse bekommen
        $sql = $db->prepare("SELECT email FROM user WHERE username=?");
        $sql->bind_param("s", $username);
        $sql->execute();
        $result = $sql->get_result();
        $row = $result->fetch_assoc();
        $to = $row['email'];
        $sql = $db->prepare("UPDATE user SET verification_code=? WHERE username=?");
        $sql->bind_param("ss", $hashed_verification_code, $username);
        $sql->execute();
        $sql->get_result();
        send_verify_mail($to, $verification_code, $username);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = connect_to_database();
        if ($db instanceof Throwable) {
            http_response_code(500);
            exit();
        } else {
            try {
                if (preg_match("/^\d$/", $_POST["0"]) && preg_match("/^\d$/", $_POST["1"]) && preg_match("/^\d$/", $_POST["2"]) && preg_match("/^\d$/", $_POST["3"]) && preg_match("/^\d$/", $_POST["4"]) && preg_match("/^\d$/", $_POST["5"])) {
                    $username = $_GET["username"] ?? "";
                    $verification_code = $_POST["0"] . $_POST["1"] . $_POST["2"] . $_POST["3"] . $_POST["4"] . $_POST["5"];
                    $hashed_verification_code = password_hash($verification_code, PASSWORD_DEFAULT);
                    $sql = $db->prepare("SELECT verification_code FROM user WHERE username=?");
                    $sql->bind_param("s", $username);
                    $sql->execute();
                    $result = $sql->get_result();
                    $row = $result->fetch_assoc();
                    // Korrekter Verifizierungs-Code
                    if (password_verify($verification_code, (string)$row["verification_code"])){
                        $sql = $db->prepare("UPDATE user SET verified=? WHERE username=?");
                        $wahr = 1;
                        $sql->bind_param("is", $wahr, $username);
                        $sql->execute();
                        header("Location: login.php");
                        exit;
                    }
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
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
                        <input id="0" name="0" class="code-input">
                        <input id="1" name="1" class="code-input">
                        <input id="2" name="2" class="code-input">
                        <input id="3" name="3" class="code-input">
                        <input id="4" name="4" class="code-input">
                        <input id="5" name="5" class="code-input">
                    </div>
                    <button class="option" type="submit"><p>Verifizieren</p></button>
                    <script>
                        // Automatisches Ausfüllen des Codes
                        const code = new URLSearchParams(window.location.search).get('code');
                        const inputs = document.querySelectorAll('.code-input');
                        if (code !== null && /^\d{6}$/.test(code)){ // Regex code zum Testen auf gültiges Code-Format
                            // Ziffern auf die Felder verteilen
                            code.split('').forEach((char, index) => {
                                if (inputs[index]) inputs[index].value = char;
                            });
                        }
                        inputs[0].addEventListener("focus", async() => { // Sobald man ins erste Feld klickt
                            try {
                                let code = await navigator.clipboard.readText();
                                code = code.replace(/\s/g, ''); // Zeilenumbrüche entfernen
                                if (/^\d{6}$/.test(code)) {
                                    // Ziffern auf die Felder verteilen
                                    code.split('').forEach((char, index) => {
                                        if (inputs[index]) inputs[index].value = char;
                                    });
                                }
                            } catch (err) {

                            }
                        }, { once: true }) // Der Listener funktioniert nur beim ersten Klick in ein Feld. Somit wird verhindert, dass man nichts eigenes mehr eingeben kann.
                        // Listened für ein Paste-Event
                        document.querySelector('.verify-code').addEventListener('paste', (e) => {
                            e.preventDefault(); // Verhindert, dass Einfügen
                            let code = e.clipboardData.getData('text');
                            code = code.replace(/\s/g, ''); // Zeilenumbrüche entfernen
                            if (/^\d{6}$/.test(code)) {
                                // Auf die Felder verteilen
                                code.split('').forEach((char, index) => {
                                    if (inputs[index]) inputs[index].value = char;
                                });
                            }
                        });
                        inputs.forEach((input, index) => {
                            input.addEventListener('input', (e) => { // Bei jedem Input
                                const value = e.target.value;
                                if (/^\d{1}$/.test(value) && index < inputs.length - 1) { // Bei einzelnen Ziffern, sofern nicht im letzten Feld
                                    inputs[index + 1].focus(); // Fokus aufs nächste Feld
                                } else if (/^\d{1}$/.test(value) && index == inputs.length) {
                                }
                            });
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                                    inputs[index - 1].focus(); //Felder zurück
                                }
                            });
                        });
                    </script>
                </form>
                    <a href=<?php echo "verify.php?resend_mail=true&username=" . urlencode($username); ?>>Klicken Sie hier um den Code erneut per E-Mail zu senden.</a>
            </div>
        </main>
        <footer>
            <a href="impressum.html">Impressum</a>
        </footer>
    </body>
</html>