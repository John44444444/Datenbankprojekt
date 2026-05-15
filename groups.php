
<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';


    



function create_group_table($connection) {

    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_admin';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "CREATE TABLE `fraguns_datenbankprojekt`. $tableName (`group_id` INT(20) NOT NULL , `admin_username` VARCHAR(20) NOT NULL , `group_name` VARCHAR(20) NOT NULL , PRIMARY KEY (`group_id`)) ENGINE = InnoDB;";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}


function creat_groupmember_tabel($connection) {

    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_users';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "CREATE TABLE `fraguns_datenbankprojekt`. $tableName (`username` VARCHAR(20) NOT NULL , `group_id` INT(20) NOT NULL ) ENGINE = InnoDB;";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

$username = "test";
$groupid = "241234";

function add_groupmember($connection, $username, $groupid){
    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_users';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "INSERT INTO `group_users` (`username`, `group_id`) VALUES ('$username', '$groupid');";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }

}

function remove_groupmember($connection, $username, $groupid){

    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_users';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "DELETE FROM group_users WHERE username='$username' and group_id='$groupid'";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}


echo(creat_groupmember_tabel(connect_to_database()));

echo(create_group_table(connect_to_database()));

echo(add_groupmember(connect_to_database(),$username, $groupid));

echo(remove_groupmember(connect_to_database(),$username, $groupid));