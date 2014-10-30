<?php
class Connection {
    public static function getPDOconn() {
        $dsn = "mysql:host=localhost;dbname=DATABASENAAM;charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $pdo = new PDO($dsn,'GEBRUIKERSNAAM','WACHTWOORD', $opt);

        return $pdo;
    }
}
