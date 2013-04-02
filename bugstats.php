<?php
require("../session.php");
// buglist.php
// Ron Patterson, WildDog Design
// PDO version
// connect to the database 
require("dbdefpdo.php");
$max=50;
$start=isset($_GET["start"]) ? $_GET["start"] : "";
if ($start == "") $start=0;
$otype = isset($_GET["otype"]) ? $_GET["otype"] : "";
$ttl="BugTrack Stats";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <title>BugTrack Stats</title>
  <meta name="author" content="Ron Patterson, ASD20">
  <link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
</head>
<body background="" bgcolor="white">
<center>
<table>
<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
</table><br>
Click on the Description/Status to see list.<p>
<a href="bugedit.php?action=add"><b>Add a new bug entry</b></a>
-- <a href="buglist.php?type=<? echo $otype; ?>"><b>Show <? echo $otype; ?> list</b></a>
-- <a href="buglist.php?type=unassigned"><b>Show unassigned</b></a>
<p>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<tr><th>Description</th><th>Records</th></th></tr>
<?
$ttl=0;
// execute query 
$sql = "select bug_type,t.descr,count(*) from bt_bugs b, bt_type t where bug_type=cd group by bug_type,t.descr";
$stmt = $dbh->query($sql);
if ($stmt) {
	// loop thru all rows
	$cnt2=0; $out="";
    while ($arr = $stmt->fetch(PDO::FETCH_NUM)) {
		list($type,$descr,$cnt) = $arr;
		$href="buglist.php?type=bytype&bug_type=$type|$descr";
		$out .= "
<tr valign='top'>
	<td><a href='$href'>$descr</a></td>
	<td align='right'>$cnt</td>
</tr>";
		$ttl+=$cnt;
	}
	echo $out;
}
?>
<td></td>
<td align='right'><b><? echo $ttl; ?></b></td>
</tr></table><br>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<tr><th>Status</th><th>Records</th></th></tr>
<?
$sql = "select status,count(*) from bt_bugs group by status";
$stmt = $dbh->query($sql);
if ($stmt) {
	// loop thru all rows
	$cnt2=0; $out=""; $nccnt=0;
    while ($arr = $stmt->fetch(PDO::FETCH_NUM)) {
		list($status,$cnt) = $arr;
		if ($status != "c") $nccnt+=$cnt;
		$href="buglist.php?type=bystatus&status={$status}";
		$out .= "
<tr valign='top'>
	<td><a href='$href'>{$sarr[$status]}</a></td>
	<td align='right'>$cnt</td>
</tr>";
		$cnt2++;
	}
	$out .= "
<tr valign='top'>
	<td>Non-closed</a></td>
	<td align='right'>$nccnt</td>
</tr>";
	echo $out;
}
$start+=$cnt2;
?>
</tr></table><br>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<tr><th>Priority</th><th>Records</th></th></tr>
<?
$sql = "select priority,count(*) from bt_bugs group by priority";
$stmt = $dbh->query($sql);
if ($stmt) {
	// loop thru all rows
	$cnt2=0; $out="";
    while ($arr = $stmt->fetch(PDO::FETCH_NUM)) {
		list($priority,$cnt) = $arr;
		$href="buglist.php?type=bypriority&priority={$priority}";
		$out .= "
<tr valign='top'>
	<td><a href='$href'>{$parr[$priority]}</a></td>
	<td align='right'>$cnt</td>
</tr>";
		$cnt2++;
	}
	echo $out;
}
$start+=$cnt2;
?>
</tr></table><br>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<tr><th>Description</th><th>Records</th></th></tr>
<?
$sql = "select count(*) from bt_worklog";
$sth = $dbh->prepare($sql);
$sth->execute();
$count = $sth->fetchColumn();
?>
<tr>
<td>Work Log entries</td>
<td align='right'><b><? echo $count; ?></b></td>
</tr>
<?
$sql = "select count(*) from bt_attachments";
$sth = $dbh->prepare($sql);
$sth->execute();
$count = $sth->fetchColumn();
?>
<tr>
<td><a href=buglist2.php>Attachment entries</a></td>
<td align='right'><b><? echo $count; ?></b></td>
</tr></table><br>
<?
#if ($start < $cnt) echo "<a href='buglist.php?start=$start'><b>Next -&gt;</b></a><p>\n";
?>
<a href="bugedit.php?action=add"><b>Add a new bug entry</b></a>
-- <a href="buglist.php?type=<? echo $otype; ?>"><b>Show <? echo $otype; ?> list</b></a>
-- <a href="buglist.php?type=unassigned"><b>Show unassigned</b></a>
<p>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
<? require("footer.php"); ?>
</center>
</body>
</html>
