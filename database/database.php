<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sirmesir";

$dbs = mysqli_connect($servername, $username, $password, $dbname);
if(!$dbs) {
    die("connection failed: ". mysqli_connect_error());
}


function getData($connection, $query): ?array
{
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $data = $stmt->get_result();
    return $data->fetch_all(MYSQLI_ASSOC);
}