<?php
// bugassign2Ajax.php
// handle bug assignment
// Ron Patterson, WildDog Design
// PDO version
require("../session.php");
extract($_POST);
// update bug record
require("dbdefpdo.php");
$btusernm = $_SESSION["uname"];
$pid = intval($pid);
$unm = addslashes($uname);
$sql = "update bt_bugs set assigned_to='$unm' where id=$bid";
#echo $sql;
$count = $dbh->exec($sql);
if (!$count) die("ERROR: Update failed ($sql)");
$sql = "select * from bt_bugs where id=$bid";
$stmt = $dbh->query($sql);
$r = $stmt->fetchObject();
if ($r === FALSE) die("Bug not found ($id)");

list($uid,$lnm,$fnm,$email) = preg_split("/,/",$UsersArr[$unm]);

$msg = "Hello $fnm $lnm,

You have been assigned a BugTrack entry by $btusernm.

ID: $r->bug_id
Description: $r->descr
Product: {$parr[$r->priority]}
Type: $r->bug_type
Comments: $r->comments
";
if (!mail($user_email,"New BugTrack assignment {$r->bug_id}",$msg,"CC: ron@wilddogdesign.com,janie@wilddogdesign.com"))
	die("ERROR: Send mail failed!");
#echo nl2br($msg); exit;
#header("Location: bugshow1.php?id=$bid");
echo "<a href='mailto:$email'>".$fnm." ".$lnm."</a>";
?>
