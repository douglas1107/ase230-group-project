<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ase230';

$db = new mysqli($host, $username, $password, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
