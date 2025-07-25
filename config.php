<?php
$servername = "localhost";
$username = "root";
$password = "";  // XAMPP default MySQL password is empty
$dbname = "assignment";  // Your database name

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful! Wa mbwa weeeeeee"; // You can remove this line later
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>