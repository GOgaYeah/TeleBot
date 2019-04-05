<?php
include "setting.php";
$stmt = $mysqli->query('SELECT * FROM `send`');
$st = $stmt->fetch_assoc();
$qiwi_to = $st['qiwi'];

$mysqli->query("UPDATE send SET qiwi = 1231231 WHERE id = 1");
	
	
	
	
	
	
	
	
	
	
	
	
	
	