<?php
// bugedit1.php
// Ron Patterson, WildDog Design
// SQLite version
#require("btsession.php");
#print_r($_POST); exit;
// connect to the database 
require("bugcommon.php");

extract($_POST);
$rec = $_POST;
$bid = $id;
if (!isset($action2)) die("No entry form provided!");

$err="";
if ($descr == "")
	$err .= " - Description must not be blank\n";
if ($bug_type == " " || $bug_type == "0")
	$err .= " - Bug type must be selected\n";
if ($status == " " || $status == "0")
	$err .= " - Bug type must be selected\n";
if ($err != "") {
	echo "<pre>$err</pre>\n";
	exit;
}

// $descr = slashem($descr);
// $product = slashem($product);
// $comments = slashem($comments);
// $solution = slashem($solution);
if ($action2 == "add") {
	$bid = $db->addBug($rec);
// 	$descr = stripcslashes($descr);
// 	$comments = stripcslashes($comments);
	$headers = "From: BugTrack <info@wilddogdesign.com>\r\nCC: ron@wilddogdesign.com,janie@wilddogdesign.com";
	$msg = "Hello,

A new BugTrack entry was added by $ename.

ID: $bid
Description: $descr
Product: $product
Type: $bug_type
Priority: $parr[$priority]
Comments: $comments
";
	//mail($email,"New BugTrack entry $bid",stripcslashes($msg),$headers);
} else {
	#$comments = ereg_replace("'","''",$comments);
	if ($assigned_to == "") $assigned_to="NULL";
	if ($status != $oldstatus and $status == "c")
		$closed = true; else $closed = false;
	$db->updateBug($bid,$rec,$closed);
	if ($status != $oldstatus) {
// 		$descr = stripcslashes($descr);
// 		$comments = stripcslashes($comments);
// 		$solution = stripcslashes($solution);
		$headers = "From: BugTrack <info@wilddogdesign.com>\r\nCC: ron@wilddogdesign.com,janie@wilddogdesign.com,$ebemail,$aemail";
		$msg = "Hello,

BugTrack entry $bid status was changed by $uname.

ID: $bid
Description: $descr
Status: $sarr[$status]
Comments: $comments
Solution: $solution
";
		//mail($email,"BugTrack entry $bid changed",stripcslashes($msg),$headers);
	}
	#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
}
echo "SUCCESS ".$bid;
?>
