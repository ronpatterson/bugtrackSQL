<?php
// Ron Patterson, WildDog Design
// PDO version
require("../session.php");
// handle bug delete
extract($_POST);
$ttl="Bug Delete";
// delete contact record
require("BugTrack.class.php");
$bug = new BugTrack();
$bug->deleteBug($id);
?>
SUCCESS
