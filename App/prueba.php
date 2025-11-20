<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=portal_empleo;charset=utf8mb4", "user", "1234");
    echo "OK";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>