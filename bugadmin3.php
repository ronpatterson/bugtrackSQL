<?php
// bugadmin3.php
// Ron Patterson, WildDog Design
// SQLite version
// connect to the database 
require_once("bugcommon.php");

extract($_POST);
$rec = $_POST;

if (!isset($action)) die("No entry form provided!");

$err="";
// if ($comments == "")
// 	$err .= " - Comments must not be blank\n";
if (trim($uid) == "" and trim($uid2) == "")
	$err .= " - UID must not be blank\n";
if (trim($lname) == "")
	$err .= " - Last Name must not be blank\n";
if (trim($email) == "")
	$err .= " - Email must not be blank\n";
if (trim($bt_group) == "")
	$err .= " - Group must be selected\n";

if ($err != "") die("<pre>$err</pre>");

if ($uid == "") {
	$uid = $db->addUser($rec);
	$msg = "Hello,

BugTrack user $uid2 was added by $ename.

Name: $lname, $fname
";
	$to = $email;
	//if ($to == "") $to = $email;
	$admin_emails = $db->get_admin_emails();
	$headers = "From: $from_email\r\nCC: $admin_emails,$email";
	//mail($to,"BugTrack $uid2 user entry",stripcslashes($msg),$headers);
} else {
	$db->updateUser($uid,$rec);
	#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
}
//header("Location: bugshow1.php?id=$bid");
?>
SUCCESS <?php echo $uid ?>
