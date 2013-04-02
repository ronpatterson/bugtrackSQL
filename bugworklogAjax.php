<?php
// bugworklogAjax.php
// Ron Patterson, WildDog Design
// PDO version
require("../session.php");
// connect to the database 
require("bugcommon.php");
require("BugTrack.class.php");
extract($_POST);

if (!isset($id) or $id == "") die("No ID provided!");
$id = intval($id);

$bug = new BugTrack();

// execute query 
$rows = $bug->getWorkLogEntries($id);
echo "<p align='center'>".count($rows)." Worklog entries found -- <a href='#' onclick='return add_worklog($id);'>Add</a><p>\n";
if (count($rows) > 0):
?>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<?
	foreach ($rows as $row) {
		$email = "";
		list($wid,$bid,$btusernm,$comments,$entry_dtm,$edtm)=$row;
		if ($btusernm != "") {
			$arr = get_user($btusernm);
			$ename = "$arr[2] $arr[1]";
			$email = $arr[3];
		} else $ename="";
?>
<tr><td><b>Date/Time: <? echo $edtm; ?>, By: <a href="mailto:<? echo $email ?>"><? echo $ename; ?></a></b></td></tr>
<tr><td cellspan="2"><? echo nl2br(addlinks($comments)); ?></td></tr>
<?
	}
endif;
?>
</table>
