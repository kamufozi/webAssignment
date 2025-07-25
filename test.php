<?php
echo "PHP is working!";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "assignment";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    echo "<br>Database connection works!";
} catch(PDOException $e) {
    echo "<br>Database error: " . $e->getMessage();
}
?>