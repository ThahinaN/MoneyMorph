<?php
$connection = new mysqli('localhost', 'root', '', 'finplan');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
