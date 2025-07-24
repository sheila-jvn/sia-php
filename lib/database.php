<?php
$config = [
    'host' => '127.0.0.1',
    'dbname' => 'my_app_db',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];


/**
 * Establishes a database connection using PDO.
 *
 * Uses a static variable to ensure the connection is only made once per request.
 * @return PDO The PDO database connection object.
 */
function getDbConnection() {
    static $pdo;

    if (!$pdo) {
        try {       
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

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
