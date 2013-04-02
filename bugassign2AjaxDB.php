<?php
// bugassign2Ajax.php
// handle bug assignment
require("../session.php");
extract($_POST);
// update bug record
require("dbdef.php");
$btusernm = $_SESSION["uname"];
$pid = intval($pid);
$bid = intval($bid);
$result = mysql_query("select user_nm,user_fnm,user_lnm,user_email from mega_user where user_id=$pid");
$arr = mysql_fetch_array($result);
list($unm,$user_fnm,$user_lnm,$user_email) = $arr;
mysql_free_result($result);
$sql = "update bt_bugs set assigned_to='$unm' where id=$bid";
#echo $sql;
$result = mysql_query($sql);
echo mysql_error();
if (!$result) die("ERROR: Update failed, $sql, ".mysql_error());
$result = mysql_query("select * from bt_bugs where id=$bid");
$r = mysql_fetch_object($result);
mysql_close($link);
$msg = "Hello $user_fnm $user_lnm,

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
echo "<a href='mailto:$user_email'>".$user_fnm." ".$user_lnm."</a>";
?>
