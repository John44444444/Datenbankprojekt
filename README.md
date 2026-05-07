# Datenbankprojekt
Dieses Projekt ist ein Schulprojekt im Rahmen des Informatikunterrichts.
## Allgemeine Struktur
Folgendes UML Anwendungsfalldiagramm zeigt die grundlegenden Funktionen von FragUns:
<img width="650" height="665" alt="image" src="https://github.com/user-attachments/assets/0801015e-4e8c-43c3-a0e6-dc9c9635a926" />
Folgendes ER-Modell zeigt die Grundlegende Projektstruktur der Entitäten und Beziehungen:  
<img width="1302" height="891" alt="image" src="https://github.com/user-attachments/assets/957ec917-273f-4607-87eb-e0c1ba016f61" />
Daraus resultieren folgende Relationen in der Datenbank (leider werden hier die Unterstrichenen Wörter nicht richtig angezeigt):  
groups(<u>group_id</u>, ↑admin_username, group_name)
user(<u>username</u>, display_name, password)
simple_questions(↑<u>group_id</u>, <u>question_text</u>, ↑<u>creator_username</u>, ↑<u>expiration_date</u>)
multiple_choice_questions(↑<u>group_id</u>, <u>question_text</u>, ↑<u>creator_username</u>, ↑<u>expiration_date</u>)
multiple_choice_options(<u>option_text</u>)
text_questions(↑<u>group_id</u>, ↑<u>question_text</u>, ↑<u>creator_username</u>, ↑<u>expiration_date</u>)
ranking_questions(↑<u>group_id</u>, ↑<u>question_text</u>, ↑<u>creator_username</u>, ↑<u>expiration_date</u>)
ranking_options(<u>option_text</u>)
group_users(↑<u>username</u>, ↑<u>group_id</u>)
user_answers_simple_question(↑<u>username</u>, ↑<u>group_id</u>, answer)
user_answers_text_question(↑<u>username</u>, ↑<u>group_id</u>, answer)
user_answers_multiple_choice(↑<u>username</u>, ↑<u>group_id</u>, option_name)
user_answers_ranking(↑<u>username</u>, ↑<u>group_id</u>, option_name)
Hier noch ein Bild mit der richtigen Anzeige:
<img width="841" height="389" alt="image" src="https://github.com/user-attachments/assets/2a09f276-cf52-42ba-bbc5-d7526692b32c" />
## Funktionen und Inhalte
### Setup
Die Datei setup.php prüft bei Ihrem Aufruf automatisch, ob bereits eine Datenbank existiert und erstellt diese falls nicht. Zudem erstellt die Datei alle Relationen in der Datenbank, falls noch nicht vorhanden. Außerdem enthält die Datei die Funktion, zur Verbindung mit der Datenbank, welche von anderen Dateien genutzt werden kann.
### Registrieren
Die Registrierung läuft über die Datei register.php ab. Dabei gibt es im Formular für die Registrierung die Felder "Benutzername", "Anzeigename", "E-Mail" und "Passwort".
<img width="518" height="566" alt="image" src="https://github.com/user-attachments/assets/8b8d718a-dc54-463b-b82c-067ee35f9927" />
Sowohl Benutzername als auch E-Mail müssen einzigartig sein. Zudem ist die Länge des Benutzernamens auf 3-20 Zeichen festgelegt. Der Anzeigename ist auf 10 Zeichen beschränkt. Die E-Mail muss ebenfalls zwischen 3 und 20 Zeichen lang sein und dem Format string@string.string entsprechen. Das Passwort muss zwischen 8 und 30 Zeichen lang sein und mindestens einen Großbuchstaben, eine Zahl und ein Sonderzeichen enthalten. Diese Einschränkungen (außer die Einzigartigkeit der E-Mail) werden bereits Client-seitig per JavaScript geprüft. Die Einzigartigkeit der E-Mail wird aus Datenschutzgründen nicht im Client geprüft. Für die Verfügbarkeit des Benutzernamens wird zudem per API (check-username.php) auf die Relation der Benutzer zurückgegriffen.  
Um zu gewährleisten, dass keine ungültigen Einträge in die Datenbank gelangen, werden alle Einträge Server-seitig überprüft und die Datenbank lässt für die Felder username und email nur einzigartige Werte zu. Zusätzlich zu den gültigen Angaben müssen die AGB und Datenschutzerklärung akzeptiert und für gelesen erklärt werden. Außerdem prüft cloudflare per API-Einbindung ob der/die Nutzer*in menschlich ist. Nur wenn das der Fall ist, kann die Registrierung abgeschlossen werden. Anschließend wird eine E-Mail an die angegebene E-Mail-Adresse versendet, die entweder einen Verifizierungscode enthält, falls die E-Mail-Adresse noch nicht registriert/verifiziert ist und eine Information darüber, dass jemand versucht hat mit der E-Mail-Adresse ein Konto zu erstellen, wenn die E-Mail-Adresse bereits verifiziert ist. Anschließend wird der Nutzer auf die Verifizierungsseite weitergeleitet (verify.php).
### Verifizieren
<img width="532" height="462" alt="image" src="https://github.com/user-attachments/assets/84881b5f-47f5-4b27-8d4b-2a50cf0a8277" />
Die Verifizierungsseite (verify.php) erhält über die URL per GET den Benutzernamen der Person, welche sich verifizieren möchte. Wenn die Verifizierungsseite über den Link in der E-Mail aufgerufen wurde enthält die URL automatisch den Verifizierungscode, welcher per Javascript automatisch in die entsprechenden Felder gefüllt wird. Falls die Verifizierungsseite über die Registrierungsseite aufgerufen wurde prüft Javascript beim Klick in das erste Ziffernfeld die Zischenablage auf einen sechsstelligen Zahlencode. Falls ein solcher in der Zwischenablage liegt, wird dieser eingefügt. Ansonsten prüft Javascript alle per PASTE eingegeben Daten auf das Format des sechstelligen Zahlencodes und fügt die Daten, falls passend, ein. Gibt der/die Nutzer*in die Ziffern manuell ein, so wird der Fokus nach jeder Ziffer automatisch in das nächste Ziffernfeld gerückt um die Benutzerfreundlichkeit zu maximieren. Beim Klick auf den "Verifizieren"-Button wird Server-seitig geprüft, ob der Verifizierungscode noch gültig ist und falls ja wird der/die Nutzer*in als verifiziert vermerkt. Beim Klick auf den Link "Klicken Sie hier um den Code erneut per E-Mail zu senden." wird, wie zu erwarten, erneut ein Verifizierungscode an die, bei der Registrierung angegebene E-Mail-Adresse gesendet.

