<?php
$dsn = 'mysql:dbname=bugtrack;host=wilddo1.fatcowmysql.com';
try {
	$dbh = new PDO($dsn,"buguser","apw4bug");
} catch (PDOException $e) {
	//echo 'Connection failed: ' . $e->getMessage();
	{ header("Location: /dberror.html"); exit; }
}
#print_r($dbh);
//if (!$link) { header("Location: dberror.html"); exit; }
define("AUSERS","ron,janie");
$sarr=array("o"=>"Open", "h"=>"Hold", "w"=>"Working", "t"=>"Testing", "c"=>"Closed");
$parr=array("1"=>"High","2"=>"Normal","3"=>"Low");
$grparr=array("WDD"=>"WildDog Design");
?>
