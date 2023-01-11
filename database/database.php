<?php

$host = "localhost";
$dbname = "gerenciamento_riscos";
$username = "root";
$password = "";

try {
	$conn = new 
		PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// echo "Conectado ao banco: $dbname.";
} catch (PDOException $pe) {
	die
	("Could not connect to the database $dbname :" . $pe->getMessage());
}