<?php
// bugedit4.php
// Ron Patterson, WildDog Design
// SQLite version
// connect to the database 
require_once("bugcommon.php");

extract($_POST);
$rec = $_POST;

$bid=intval($id);
if (!isset($action)) die("No entry form provided!");

$err="";
if ($comments == "")
	$err .= " - Comments must not be blank\n";
if ($err != "") die("<pre>$err</pre>");

if ($action2 == "add") {
	$id = $db->addWorkLog($rec);
	$comments = stripcslashes($comments);
	$msg = "Hello,

BugTrack entry $bug_id worklog entry was added by $ename.

Description: $descr
Comments: $comments
";
	$to = $ebemail;
	if ($to == "") $to = $email;
	$headers = "From: BugTrack <info@wilddogdesign.com\r\nCC: ron@wilddogdesign.com,janie@wilddogdesign.com,$email,$aemail";
	//mail($to,"BugTrack $bug_id worklog entry",stripcslashes($msg),$headers);
} else {
	$db->updateWorkLog($id,$rec);
	#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
}
//header("Location: bugshow1.php?id=$bid");
?>
