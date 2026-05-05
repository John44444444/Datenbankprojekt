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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
                <p><a>Klicken Sie hier um den Code erneut per E-Mail zu senden.</a></p>
            </div>
        </main>
        <footer>
            <a href="impressum.html">Impressum</a>
        </footer>
    </body>
</html>
