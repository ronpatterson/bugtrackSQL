<?php
// bugsend1.php
// Ron Patterson, WildDog Design
// PDO version
require("../session.php");
// connect to the database 
require("bugcommon.php");
require("BugTrack.class.php");

$bug = new BugTrack();

$ttl="Send Record";
extract($_POST);
if (!isset($id)) $id = isset($_GET['id']) ? $_GET['id'] : "";
if ($id == "") die("ERROR: No ID provided!");

if (!isset($action) or $action == "") $action="form";
$id = intval($id);

// execute query 
$arr = $bug->getBug($id,PDO::FETCH_NUM);
if (count($arr) == 0) die("ERROR: Bug not found ($id)");
list($id,$descr,$product,$usernm,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$bug_id,$entry_dtm,$update_dtm,$closed_dtm,$edtm,$udtm,$cdtm) = $arr;
$bt = $bug->getBugTypeDescr($bug_type);
if ($usernm != "") {
	$arr = get_user($usernm);
	$ename = "$arr[2] $arr[1]";
} else $ename="";
$email = $arr[3];
if ($assigned_to != "") {
	$arr = get_user($assigned_to);
	$aname = "$arr[2] $arr[1]";
} else $aname="";
$aemail = $arr[3];
$alink="";
if (preg_match("/".$_SESSION["uname"]."/",AUSERS))
	$alink="<a href='bugassign.php?id=$id'>Assign</a>";
#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
if ($action == "send") {
	$descr = stripcslashes($descr);
	$comments = stripcslashes($comments);
	$solution = stripcslashes($solution);
	$msg = "$msg2
	
Details of Bug ID $bug_id.

Description: $descr
Product or Application: $product
Bug Type: $bt
Status: $sarr[$status]
Priority: $parr[$priority]
Comments: $comments
Solution: $solution
Entry By: $ename
Assigned To: $aname
Entry Date/Time: $edtm
Update Date/Time: $udtm
Closed Date/Time: $cdtm

";
	$rows = $bug->getWorkLogEntries($id);
	$msg .= count($rows)." Worklog entries found

";
	if (count($rows) > 0) {
		foreach ($rows as $row) {
			list($wid,$bid,$usernm,$comments,$entry_dtm,$edtm)=$row;
			if ($usernm != "") {
				$arr = get_user($usernm);
				$ename = "$arr[2] $arr[1]";
			} else $ename="";
			$comments = stripcslashes($comments);
			$msg .= "Date/Time: $edtm, By: $ename
Comments: $comments
";
		}
	}
	if (!preg_match("/@/",$sendto)) $sendto.="@wilddogdesign.com";
	if ($cc != "" and !preg_match("/@/",$cc)) $cc.="@wilddogdesign.com";
	if ($cc != "") $ccx="CC: $cc"; else $ccx="";
	mail($sendto,$subject,stripcslashes($msg),$ccx);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <title>BugTrack Send</title>
  <meta name="author" content="Ron Patterson, ASD20">
  <link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
</head>
<body background="" bgcolor="white">
<div class="bugform">
<table align="center">
<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
</table><br>
<?
if ($action == "form"):
?>
<form name="form1" method="post"><br>
  <input type="hidden" name="action" value="send">
  <input type="hidden" name="id" value="<? echo $id; ?>">
  <input type="hidden" name="bug_id" value="<? echo $bug_id; ?>">
  <input type="hidden" name="uname" value="<? echo $uname; ?>">
	<fieldset>
		<legend>BugTrack Record</legend>
		<label>ID:</label>
		<div class="fields2"><? echo $bug_id; ?></div><br class="clear">
		<label>Description:</label>
		<div class="fields2"><? echo $descr; ?></div><br class="clear">
		<label for="sendto">Send to:</label>
		<div class="fields2"><input type="text" name="sendto" id="sendto" size="40"></div><br class="clear">
		<label for="cc">Send copy to (CC):</label>
		<div class="fields2"><input type="text" name="cc" id="cc" size="40"></div><br class="clear">
		<label for="subject">Subject:</label>
		<div class="fields2"><input type="text" name="subject" id="subject" size="40" value="<? echo "$bug_id - $descr"; ?>"></div><br class="clear">
		<label for="msg2">Message to add:</label>
		<div class="fields2"><textarea name="msg2" id="msg2" rows="3" cols="40" wrap="virtual"></textarea></div><br class="clear">
 		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="Send Bug Message"> <input
 type="reset"></div><br class="clear">
	</fieldset>
  <br>
</form>
<?
else:
?>
<script type="text/javascript">
	alert("Bug message sent.");
	window.opener.w.close();
</script>
<?
	exit;
endif;
?>
<p><a href="#" onclick="window.opener.w.close();">Close window</a></p>
<? require("footer.php"); ?>
</body>
</html>
