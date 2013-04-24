<?php
// bugassign2Ajax.php
// handle bug assignment
// Ron Patterson, WildDog Design
// SQLite version
require("btsession.php");
//extract($_POST);
// update bug record
// connect to the database 
require_once("dbdef.php");
require("BugTrack.class.php");
$db = new BugTrack($dbpath);
$dbh = $db->getHandle();
$uname = $_POST["uname"];
$bid = intval($_POST["bid"]);
$btusernm = $_SESSION["user_nm"];
$pid = intval($pid);
$unm = addslashes($uname);
$sql = "update bt_bugs set assigned_to='$unm' where id=$bid";
#echo $sql;
$count = $dbh->exec($sql);
if (!$count) die("ERROR: Update failed ($sql)");
$sql = "select * from bt_bugs where id=$bid";
$stmt = $dbh->query($sql);
$r2 = $stmt->fetchArray(SQLITE3_ASSOC);
if ($r2 === FALSE) die("Bug not found ($bid)");
$stmt->finalize();
$r = (object)$r2;

$sql = "select * from bt_users where uid='$uname'";
$stmt = $dbh->query($sql);
$user = $stmt->fetchArray(SQLITE3_NUM);
if ($user === FALSE) die("User not found ($uname)");
$stmt->finalize();

list($uid,$lnm,$fnm,$email,$act) = $user;

$msg = "Hello $fnm $lnm,

You have been assigned a BugTrack entry by $btusernm.

ID: $r->bug_id
Description: $r->descr
Product: {$parr[$r->priority]}
Type: $r->bug_type
Comments: $r->comments
";
//$headers = "CC: ron@wilddogdesign.com,janie@wilddogdesign.com";
$headers = "CC: ronlpatterson@me.com";
if (!mail($user_email,"New BugTrack assignment {$r->bug_id}",$msg,$headers))
	die("ERROR: Send mail failed!");
//echo nl2br($msg); exit;
#header("Location: bugshow1.php?id=$bid");
echo "<a href='mailto:$email'>".$fnm." ".$lnm."</a>";
?>
