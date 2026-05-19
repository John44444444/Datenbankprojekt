<?php
function send_verify_mail($to, $verification_code, $username) {
    $verification_code = (string) $verification_code;
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
                        <a href="http://fraguns.bplaced.net/datenbankprojekt/verify.php?username=' . $username . '&code=' . $verification_code . '"  style="color:black; text-decoration: none;">
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
}

function send_already_registered_mail($to) {
    $subject = "Sicherheitshinweis | FragUns";
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
            margin: 20px auto;"><h1>Jemand hat versucht, sich mit Ihrer E-Mail-Adresse bei FragUns zu registrieren.</h1></header>
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
                        <h2>Das waren Sie? Nutzen Sie die Login Seite um sich bei Ihrem Konto einzuloggen.</h2>
                        <a href="http://fraguns.bplaced.net/datenbankprojekt/login.php"  style="color:black; text-decoration: none;">
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
    $headers .= "From: noreply@fraguns.com";
    mail($to, $subject, $message, $headers);
}
?>