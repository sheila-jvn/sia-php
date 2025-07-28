<?php
require_once __DIR__ . '/config.php';

/**
 * Establishes a database connection using PDO.
 *
 * Uses a static variable to ensure the connection is only made once per request.
 * @return PDO The PDO database connection object.
 */
function getDbConnection() {
    global $config;
    static $pdo;

    if (!$pdo) {
        try {       
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}
