<?php
// PDO version
require("../session.php");
# buglistAjax.php
# Ron Patterson, WildDog Design
#print_r($_SESSION);
# return a standard <select> for a lookup table
// connect to the database 
require("bugcommon.php");
require("BugTrack.class.php");

extract($_POST);

// get passed arguments
//$max=isset($_GET["max"]) ? intval($_GET["max"]) : 50;
//$start=isset($_GET["start"]) ? intval($_GET["start"]) : 0;
//$type=isset($_GET["type"]) ? $_GET["type"] : "";
//$bugtype=isset($_GET["bug_type"]) ? $_GET["bug_type"] : "";
//$priority=isset($_GET["priority"]) ? $_GET["priority"] : "";
//$search=isset($_GET["search"]) ? $_GET["search"] : "";

$bug = new BugTrack();

if ($start == "") $start=0;
$ttl="BugTrack Open List";
$crit="lower(status) <> 'c'";
$otype="closed";
$nextlink="";
$orderby = "entry_dtm desc";
$out = "";
if (!isset($stype)) $stype = "open";

if ($type == "closed") {
	$ttl="BugTrack Closed List";
	$crit="lower(status) = 'c'";
	$otype = "open";
	$stype = "open";
	$nextlink="&type=closed";
	$orderby = "closed_dtm desc";
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
	$crit="lower(bug_type) = ".quotem($cd);
	$otype = "open";
	$nextlink="&type=bytype&bug_type=".$bugtype;
}
if ($type == "bystatus") {
	$status=$_GET["status"];
	if ($status == "0" or $status == " ") {
		echo "<b>No status type selected</b>";
		exit;
	}
	$stype=$sarr[$status];
	$ttl="BugTrack $type List";
	$crit="lower(status) = ".quotem($status);
	$otype = "open";
	$nextlink="&type=bystatus&status=$status";
}
if ($type == "bypriority") {
	if ($status == "0" or $status == " ") {
		echo "<b>No priority type selected</b>";
		exit;
	}
	$stype=$parr[$priority];
	$ttl="BugTrack $type List";
	$crit="lower(priority) = ".quotem($priority);
	$otype = "open";
	$nextlink="&type=bypriority&priority=$priority";
}
if ($type == "assignments") {
	$uname=$_SESSION['uname'];
	#$sql = "select id,lname,fname,nname,portal_role,emploc from metaman.d20_person where empnbr=$empnbr";
	#$sth = $dbh->prepare($sql);
	#$sth->execute();
	#$arr = $sth->fetchColumn();
	$ttl="BugTrack My Assignments";
	$crit.=" and assigned_to=".quotem($uname);
	$stype = "open";
	$otype = "open";
	$nextlink="&type=assignments";
}
if ($type == "unassigned") {
	$ttl="BugTrack Unassigned";
	$crit.=" and assigned_to is NULL";
	$stype = "open";
	$otype = "open";
	$nextlink="&type=unassigned";
}
if ($type == "bysearch") {
	$bids = array();
	$search = slashem($search);
	$dbh = $bug->getHandle();
	$sql = "select bug_id from bt_worklog where comments like '%$search%'";
	$stmt = $dbh->query($sql);
	// loop thru all rows
    while ($arr = $stmt->fetch(PDO::FETCH_NUM)) {
		$bids[] = $arr[0];
	}
	$ids = "";
	if (count($bids) > 0) $ids = " or id in (".join(",",$bids).")";
	$crit .= " and (bug_id like '%$search%' or product like '%$search%' or descr like '%$search%' or comments like '%$search%' or solution like '%$search%'$ids)";
}

if ($type == "closed") {
	$out .= <<<END
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
	<tr>
		<th>ID</th><th>Description</th><th>Entered by<th>Date closed</th><th>Status</th>
	</tr>\n
END;
}
else {
	$out .= <<<END
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
	<tr>
		<th>ID</th><th>Description</th><th>Entered by<th>Date entered</th><th>Status</th>
	</tr>\n
END;
}
// execute query
$rows = $bug->getBugs($crit,$orderby);
if (count($rows) > 0) {
	$count2=0;
	foreach ($rows as $row) {
		$r = (object) $row;
		$descr = stripslashes($r->descr);
		$status = $r->status;
		$dt = $r->edt;
		if ($type == "closed") $dt = $r->cdt;
		$out .= <<<END
	<tr valign='top' style='font-size: 10pt;' align="left">
		<td><a href="bugshow1.php?id={$r->id}{$nextlink}"><b>{$r->bug_id}</b></a></td>
		<td>{$descr}</td>
		<td>{$r->user_nm}</td>
		<td>{$dt}</td>
		<td>{$sarr[$status]}</td>
	</tr>\n
END;
		$count2++;
	}
}
$start+=$count2;
$out .= <<<END
	</tr>
</table><br>
<p align="center"><b>$count $stype Bugs found. ($count2 shown)</b></p>
END;

echo $out;
?>