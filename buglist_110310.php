<?php
require("../session.php");
# buglist.php
# Ron Patterson, WildDog Design
#print_r($_SESSION);
# return a standard <select> for a lookup table
function retselect2 ($name, $tab, $def) {
	$sql = "select * from $tab where active='y' order by descr";
	$result = mysql_query($sql);
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n<option value='0'>None\n";
	while ($arr = mysql_fetch_array($result)) {
		list($cd,$descr,$active) = $arr;
		if ($cd == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$cd|$descr'$chk>$descr\n";
	}
	mysql_free_result($result);
	$out .= "</select>\n";
	return $out;
}
// connect to the database 
require("dbdef.php");
require("bugcommon.php");
$max=100;
$start=isset($_GET["start"]) ? intval($_GET["start"]) : 0;
$type=isset($_GET["type"]) ? $_GET["type"] : "open";
$bugtype=isset($_GET["bug_type"]) ? $_GET["bug_type"] : "";
if ($start == "") $start=0;
$ttl="BugTrack Bugs List";
$otype="closed";
$nextlink="";
if ($type == "closed") {
	$ttl="BugTrack Closed List";
	$otype = "open";
}
if ($type == "bytype") {
	$arr=split("[|]",$bugtype);
	$cd=$arr[0];
	if ($cd == "0" or $cd == " ") {
		echo "<b>No bug type selected</b>";
		exit;
	}
	$stype=$arr[1];
	$ttl="BugTrack $type List";
	$otype = "open";
}
if ($type == "bystatus") {
	$status=$_GET["status"];
	if ($status == "0" or $status == " ") {
		echo "<b>No status type selected</b>";
		exit;
	}
	$stype=$sarr[$status];
	$ttl="BugTrack $type List";
	$otype = "open";
}
if ($type == "bypriority") {
	$priority=isset($_GET["priority"]) ? $_GET["priority"] : "";
	if ($status == "0" or $status == " ") {
		echo "<b>No priority type selected</b>";
		exit;
	}
	$stype=$parr[$priority];
	$ttl="BugTrack $type List";
	$otype = "open";
}
if ($type == "assignments") {
	$uname=$_SESSION['uname'];
	#$result = mysql_query("select id,lname,fname,nname,portal_role,emploc from metaman.d20_person where empnbr=$empnbr");
	#$arr = mysql_fetch_array($result);
	#mysql_free_result($result);
	$ttl="BugTrack My Assignments";
	#$type = "open";
	$otype = "open";
}
if ($type == "unassigned") {
	$ttl="BugTrack Unassigned";
	#$type = "open";
	$otype = "open";
}
$search=isset($_POST["search"]) ? $_POST["search"] : "";
$btypes=retselect2("bug_type","bt_type","");
$stat=retselectarray('status',$sarr,"");
mysql_close($link);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
	<title>BugTrack List</title>
	<meta name="author" content="Ron Patterson, ASD20">
	<link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
	<script type="text/javascript" src="../jquery.js"></script>
	<script type="text/javascript">
	function get_results (type,obj) {
		//alert('here');
		var err = '';
		$('#results').html('Working...');
		if (type == 'bytype' && $('#bug_type').val() == ' ')
			err += ' - No bug type selected\n';
		if (type == 'bystatus' && $('#status').val() == ' ')
			err += ' - No status type selected\n';
		if (type == 'bysearch' && $.trim($('#search').val()) == '')
			err += ' - No search info provided\n';
		if (err != '') {
			alert('Problems encountered:\n' + err + 'Please correct and resubmit\n');
			return false;
		}
		$('#results').load('buglistAjax.php', { type: type, bug_type: $('#bug_type').val(), status: $('#status').val(), search: $('#search').val(), max: $('#max').val(), start: $('#start').val() });
		return false;
	}
	</script>
</head>
<body background="" bgcolor="white" onload="return get_results('<? echo $type ?>',this);">
<center>
<table>
<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
</table><br>
<form method="get" name="form1">
<table>
<tr><td>
<input type="hidden" name="max" id="max" value="<? echo $max ?>">
<input type="hidden" name="start" id="start" value="<? echo $start ?>">
<? echo $btypes; ?>
<input type="submit" name="bytype" id="bytype" value="Type List" onclick="return get_results('bytype',this);">
</td><td>
<? echo $stat; ?>
<input type="submit" name="bystatus" id="bystatus" value="Status List" onclick="return get_results('bystatus',this);">
</td><td>
<input type="text" name="search" id="search" size="10">
<input type="submit" name="bysearch" id="bysearch" value="Search" onclick="return get_results('bysearch',this);">
</td></tr></table>
</form>
<p>Click on the ID to see details.
<a href="bugstats.php"><b>BugTrack Stats</b></a>
-- <a href="../logout.php"><b>Logout</b></a></p>
<p><a href="bugedit.php?action=add"><b>Add a new bug entry</b></a>
-- <a href="buglist.php?type=<? echo $otype; ?>"><b>Show <? echo $otype; ?> list</b></a>
-- <a href="buglist.php?type=assignments"><b>Show my assignments</b></a>
-- <a href="buglist.php?type=unassigned"><b>Show unassigned</b></a>
</p>

<div id="results"></div>

<?
//$start+=$cnt2;

//if ($start < $cnt) echo "<a href='buglist.php?start={$start}{$nextlink}'><b>Next -&gt;</b></a><p>\n";
?>
<a href="bugedit.php?action=add"><b>Add a new bug entry</b></a>
-- <a href="buglist.php?type=<? echo $otype; ?>"><b>Show <? echo $otype; ?> list</b></a>
-- <a href="buglist.php?type=assignments"><b>Show my assignments</b></a>
-- <a href="buglist.php?type=unassigned"><b>Show unassigned</b></a>
<p>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
<? require("footer.php"); ?>
</center>
</body>
</html>
