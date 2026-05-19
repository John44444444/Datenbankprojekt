
<?php
// require ist hier wichtig, weil die App ohne den Zugriff auf Datenbanken nicht funktioniert und setup.php sicherstellt, dass diese korrekt existieren.
require_once __DIR__ . '/setup.php';


    


function creat_group_table($connection) {

    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_admin';

    // SQL-Befehl zur Erstellung der Relation "user", falls sie noch nicht existiert
    $sql = "CREATE TABLE IF NOT EXISTS `fraguns_datenbankprojekt`. $tableName (`group_id` INT(20) NOT NULL AUTO_INCREMENT, `admin_username` VARCHAR(20) NOT NULL , `group_name` VARCHAR(20) NOT NULL , PRIMARY KEY (`group_id`)) ENGINE = InnoDB;";
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
    $sql = "CREATE TABLE IF NOT EXISTS `fraguns_datenbankprojekt`. $tableName (`username` VARCHAR(20) NOT NULL , `group_id` INT(20) NOT NULL ) ENGINE = InnoDB;";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

$username = "test";
$groupid = "3";
$groupname="testergang";

function creat_group($connection, $username, $groupname){
    $sql = "INSERT INTO `group_admin` (`admin_username`, `group_name`) VALUES ('$username', '$groupname');";
    try {
        $connection->query($sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e;
    }
}

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
$personalusername="test";
$admin = false;
$check = 2;
function check_rights($connection, $personalusername, $admin, $groupid){
    $dbname = 'fraguns_datenbankprojekt';
    $tableName = 'group_admin';

    try {
        $sql = $connection->prepare("SELECT admin_username FROM group_admin WHERE group_id = ?");
        $sql->bind_param("s", $groupid);
        $sql->execute();
        $result = $sql->get_result();
        $row = $result->fetch_assoc();
        $ergebnis = $row['admin_username'];
        echo($ergebnis);
        
        echo($personalusername);
        if($ergebnis === $personalusername){
            return $ergebnis;
        } else {
            return 0;
        }
        
        
    } catch (mysqli_sql_exception $e) {
        return $e;
    }


}


echo(creat_groupmember_tabel(connect_to_database()));

echo(creat_group_table(connect_to_database()));
echo(creat_group(connect_to_database(),$username, $groupname));
echo(check_rights(connect_to_database(),$personalusername, $admin, $groupid));
//echo(add_groupmember(connect_to_database(),$username, $groupid));
