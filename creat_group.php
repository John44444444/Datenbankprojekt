<?php

?>

<!Doctype html>
<html lang=de>
    <head>
        <meta charset=UTF-8>
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <title>FragUns - Gruppe erstellen</title>
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
            
        </div>
        <header><h1>FragUns</h1></header>
        <main>
            <div class="card">
                <h1>Gruppe erstellen</h1>
                <input id="username" name="username" placeholder="Gruppenmitglieder">
                <span id="display-name-status" style="color: red;"></span>
                <button class="option" style="dsplay:inline;" onclick="test()">Hinzufügen</button>
                <list>Test</list>
                <form id="create_group_form" method="post">
                    <input id="username" name="username" placeholder="Gruppenname">
                    <span id="username-status" style="color: red;"></span>
                    <button class="option" type="submit"><p>Registrieren</p></button>
                </form>
            </div>
        </main>
        <footer>
            <a href="impressum.html">Impressum</a>
        </footer>
        <script>
            function test(){
                console.log("test")
                username_input = document.getElementById("username");
                statusDisplay = document.getElementById("display-name-status");
                if (username_input.value.length === 0){
                    console.log("HI")
                    statusDisplay.textContent = "Bitte geben Sie einen Benutzernamen ein.";
                } else {
                    console.log("HALLo")
                    const formData = new FormData();
                    formData.append('username', username)
                }
            }
            
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
        </script>
    </body>
</html>
