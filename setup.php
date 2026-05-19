<?php

// Error 500 ist wenn unser Server generell ein Problem hat. Wenn die Datenbank nicht erreichbar ist, ist es eher ein 503, da es ein temporäres Problem sein könnte (z.B. Wartungsarbeiten).

function connect_to_db_server() {
    $host = '127.0.0.1';
    $user = 'fraguns';
    $pass = 'TrQ@%O0q5Ib*G!';

    try {
        $connection = new mysqli($host, $user, $pass);
        return $connection;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

// Baut eine Verbindung zur Datenbank auf
function connect_to_database() {
    $host = '127.0.0.1';
    $user = 'fraguns';
    $name = 'fraguns_datenbankprojekt';
    $pass = 'TrQ@%O0q5Ib*G!';

    try {
        $connection = new mysqli($host, $user, $pass, $name);
        return $connection;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

// Prüft ob die Datenbank bereits existiert
function check_for_existing_database($connection){
    $dbname = 'fraguns_datenbankprojekt';
    $result = $connection->query("SHOW DATABASES LIKE '$dbname'");
    if ($result->num_rows > 0) {
        return 0;
    } else {
        return -1;
    }
}

// Erstellt die Datenbank
function create_database($connection){
    $dbname = 'fraguns_datenbankprojekt';
    if ($connection->query("CREATE DATABASE $dbname") === TRUE) {
        return 0;
    } else {
        return -1;
    }
}


function create_token_table($connection) {
    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'login_tokens';

    // SQL-Befehl zur Erstellung der Relation "login_tokens", falls sie noch nicht existiert
    $sql = "CREATE TABLE IF NOT EXISTS `fraguns_datenbankprojekt`.`login_tokens` (`username` VARCHAR(20) NOT NULL , `token` VARCHAR(32) NOT NULL, PRIMARY KEY (`token`)) ENGINE = InnoDB;";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}


function create_user_table($connection) {
    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'user';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "CREATE TABLE IF NOT EXISTS `fraguns_datenbankprojekt`.$tableName (
    `username` VARCHAR(20) NOT NULL,
    `displayname` VARCHAR(10) NOT NULL,
    `password` VARCHAR(256) NOT NULL,
    `email` VARCHAR(20) NOT NULL,
    `verified` BOOLEAN NOT NULL DEFAULT FALSE,
    `verification_code` VARCHAR(256) NULL DEFAULT NULL,
    `verification_expires` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`username`),
    UNIQUE (`email`)
    ) ENGINE = InnoDB;";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

// Falls die Datenbank nicht existiert, wird sie erstellt.
function setup_database() {
    $connection = connect_to_db_server();
    if ($connection instanceof Throwable) {
        error_log($connection);
        return 503;
    }
    if (check_for_existing_database($connection) === -1) {
        if (create_database($connection) === -1) {
            error_log('Failed to create database');
            $connection->close();
            return 500;
        } else {
            echo 'Database created successfully'; // Temporär
        }
    }

    // Nun können alle Relationen auf ihre Existenz geprüft und bei Bedarf erstellt werden
    $result = create_user_table($connection);
    if ($result instanceof Throwable) {
        error_log("Error while creating table: " . $result->getMessage());
        $connection->close();
        return 503;
    }
    $result = create_token_table($connection);
    if ($result instanceof Throwable) {
        error_log("Error while creating table: " . $result->getMessage());
        $connection->close();
        return 503;
    }
    $connection->close();
    return $connection;
}

setup_database();