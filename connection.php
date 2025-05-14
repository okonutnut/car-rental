<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "car_rental";

$con = mysqli_connect($host, $username, $password, $database) or die(mysqli_connect_error());


if (!$con) {
    header("./login/index.php");
}
