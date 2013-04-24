<?php
# buglist.php
# Ron Patterson, WildDog Design
#print_r($_SESSION);
# return a standard <select> for a lookup table
function retselect2 ($dbh, $name, $tab, $def) {
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n<option value='0'>None\n";
	$sql = "select * from $tab where active='y' order by descr";
	$stmt = $dbh->query($sql);
    while ($row = $stmt->fetchArray(SQLITE3_NUM)) {
		list($cd,$descr,$active) = array($row[0],$row[1],$row[2]);
		if ($cd == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$cd|$descr'$chk>$descr\n";
	}
	$out .= "</select>\n";
	return $out;
}
require("bugcommon.php");
$dbh = $db->getHandle();
#print_r($dbh);
#print_r($_POST);
$max=100;
$start=isset($_POST["start"]) ? intval($_POST["start"]) : 0;
$type=isset($_POST["type"]) ? $_POST["type"] : "open";
$bugtype=isset($_POST["bug_type"]) ? $_POST["bug_type"] : "";
if ($start == "") $start=0;
$ttl="BugTrack Bugs List";
$otype="closed";
$nextlink="";
if ($type == "closed") {
	$ttl="BugTrack Closed List";
	$otype = "open";
}
// if ($type == "bytype") {
// 	$arr=split("[|]",$bugtype);
// 	$cd=$arr[0];
// 	if ($cd == "0" or $cd == " ") {
// 		echo "<b>No bug type selected</b>";
// 		exit;
// 	}
// 	$stype=$arr[1];
// 	$ttl="BugTrack $type List";
// 	$otype = "open";
// }
// if ($type == "bystatus") {
// 	$status=$_POST["status"];
// 	if ($status == "0" or $status == " ") {
// 		echo "<b>No status type selected</b>";
// 		exit;
// 	}
// 	$stype=$sarr[$status];
// 	$ttl="BugTrack $type List";
// 	$otype = "open";
// }
// if ($type == "bypriority") {
// 	$priority=isset($_POST["priority"]) ? $_POST["priority"] : "";
// 	if ($status == "0" or $status == " ") {
// 		echo "<b>No priority type selected</b>";
// 		exit;
// 	}
// 	$stype=$parr[$priority];
// 	$ttl="BugTrack $type List";
// 	$otype = "open";
// }
// if ($type == "assignments") {
// 	$uname=$_SESSION['uname'];
// 	#$sql = "select id,lname,fname,nname,portal_role,emploc from metaman.d20_person where empnbr=$empnbr";
// 	#$sth = $dbh->prepare($sql);
// 	#$sth->execute();
// 	#$arr = $sth->fetchColumn();
// 	$ttl="BugTrack My Assignments";
// 	#$type = "open";
// 	$otype = "open";
// }
// if ($type == "unassigned") {
// 	$ttl="BugTrack Unassigned";
// 	#$type = "open";
// 	$otype = "open";
// }
$search=isset($_POST["search"]) ? $_POST["search"] : "";
$btypes=retselect2($dbh,"bug_type","bt_type","");
$stat=retselectarray('status',$sarr,"");
?>
<form method="get" name="form1">
<table>
<tr><td>
<input type="hidden" name="max" id="max" value="<?php echo $max ?>">
<input type="hidden" name="start" id="start" value="<?php echo $start ?>">
<?php echo $btypes; ?>
<input type="submit" name="bytype" id="bytype" value="Type List" onclick="return bt.buglist(event,'bytype');">
</td><td>
<?php echo $stat; ?>
<input type="submit" name="bystatus" id="bystatus" value="Status List" onclick="return bt.buglist(event,'bystatus');">
</td>
<!-- <td>
<input type="text" name="search" id="search" size="10">
<input type="submit" name="bysearch" id="bysearch" value="Search" onclick="return get_results('bysearch',this);">
</td> -->
</tr></table>
</form>
<!--
<p><a href="#" onclick="return bt.stats();"><b>BugTrack Stats</b></a>
-- <a href="../../logout.php"><b>Logout</b></a></p>
<a href="#" onclick="return bt.add();"><b>Add a new bug entry</b></a>
-->
<p><a href="#" onclick="return bt.buglist(event,'<?php echo $otype; ?>');"><b>Show <?php echo $otype; ?> list</b></a>
-- <a href="#" onclick="return bt.buglist(event,'assignments');"><b>Show my assignments</b></a>
-- <a href="#" onclick="return bt.buglist(event,'unassigned');"><b>Show unassigned</b></a>
</p>

<div id="results"></div>

<?php
//$start+=$cnt2;

//if ($start < $cnt) echo "<a href='buglist.php?start={$start}{$nextlink}'><b>Next -&gt;</b></a><p>\n";
?>
<p><a href="#" onclick="return bt.buglist(event,'<?php echo $otype; ?>');"><b>Show <?php echo $otype; ?> list</b></a>
-- <a href="#" onclick="return bt.buglist(event,'assignments');"><b>Show my assignments</b></a>
-- <a href="#" onclick="return bt.buglist(event,'unassigned');"><b>Show unassigned</b></a>
</p>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a-->
</center>
