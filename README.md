# Datenbankprojekt
Dieses Projekt ist ein Schulprojekt im Rahmen des Informatikunterrichts.
## Allgemeine Struktur
Folgendes UML Anwendungsfalldiagramm zeigt die grundlegenden Funktionen von FragUns:
<img width="650" height="665" alt="image" src="https://github.com/user-attachments/assets/0801015e-4e8c-43c3-a0e6-dc9c9635a926" />
Folgendes ER-Modell zeigt die Grundlegende Projektstruktur der Entitäten und Beziehungen:
TODO
Daraus resultieren folgende Relationen in der Datenbank:
TODO
## Funktionen und Inhalte
### Registrieren
Die Registrierung läuft über die Datei register.php ab. Dabei gibt es im Formular für die Registrierung die Felder "Benutzername", "Anzeigename", "E-Mail" und "Passwort".
<img width="518" height="566" alt="image" src="https://github.com/user-attachments/assets/8b8d718a-dc54-463b-b82c-067ee35f9927" />
Sowohl Benutzername als auch E-Mail müssen einzigartig sein. Zudem ist die Länge des Benutzernamens auf 3-20 Zeichen festgelegt. Der Anzeigename ist auf 10 Zeichen beschränkt. Die E-Mail muss ebenfalls zwischen 3 und 20 Zeichen lang sein und dem Format string@string.string entsprechen. Das Passwort muss zwischen 8 und 30 Zeichen lang sein und mindestens einen Großbuchstaben, eine Zahl und ein Sonderzeichen enthalten. Diese Einschränkungen (außer die Einzigartigkeit der E-Mail) werden bereits Client-seitig per JavaScript geprüft. Die Einzigartigkeit der E-Mail wird aus Datenschutzgründen nicht im Client geprüft. Für die Verfügbarkeit des Benutzernamens wird zudem per API (check-username.php) auf die Relation der Benutzer zurückgegriffen.
Um zu gewährleisten, dass keine ungültigen Einträge in die Datenbank gelangen, werden alle Einträge Server-seitig überprüft und die Datenbank lässt für die Felder username und email nur einzigartige Werte zu. Zusätzlich zu den gültigen Angaben müssen die AGB und Datenschutzerklärung akzeptiert und für gelesen erklärt werden. Außerdem prüft cloudflare per API-Einbindung ob der/die Nutzer/in menschlich ist. Nur wenn das der Fall ist, kann die Registrierung abgeschlossen werden. Anschließend wird eine E-Mail an die angegebene E-Mail-Adresse versendet, die entweder einen Verifizierungscode enthält, falls die E-Mail-Adresse noch nicht registriert/verifiziert ist und eine Information darüber, dass jemand versucht hat mit der E-Mail-Adresse ein Konto zu erstellen, wenn die E-Mail-Adresse bereits verifiziert ist. Anschließend wird der Nutzer auf die Verifizierungsseite weitergeleitet (verify.php).
### Verifizieren

