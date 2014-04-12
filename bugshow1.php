<?php
// bugshow1.php
// Ron Patterson, WildDog Design
// SQLite version
ini_set("display_errors", "1");
require("bugcommon.php");

$id = isset($_POST['id']) ? intval($_POST['id']) : "";
if ($id == "") {
	echo "<b>No ID provided</b>\n";
	exit;
}

$ttl="Show Record";
// execute query 
$arr = $db->getBug($id,SQLITE3_NUM);
if (count($arr) == 0) die("ERROR: Bug not found ($id)");
		list($id,$descr,$product,$btusernm,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$bug_id,$entry_dtm,$update_dtm,$closed_dtm) = $arr;
$descr = stripslashes($descr);
$product = stripslashes($product);
$comments = stripslashes($comments);
$solution = stripslashes($solution);
$edtm = $entry_dtm != "" ? date("m/d/Y g:i a",strtotime($entry_dtm)) : "";
$udtm = $update_dtm != "" ? date("m/d/Y g:i a",strtotime($update_dtm)) : "";
$cdtm = $closed_dtm != "" ? date("m/d/Y g:i a",strtotime($closed_dtm)) : "";
$bt = $db->getBugTypeDescr($bug_type);
# attachments are now in the db
$files="";
$rows = $db->getBugAttachments($id);
if (count($rows) > 0) {
	foreach ($rows as $row) {
		list($aid,$fname,$size)=$row;
		$files.="<a href='get_file.php?id=$aid' target='_blank'>$fname</a> ($size)<br>";
	}
}
$dbh = $db->getHandle();
if ($btusernm != "") {
	$arr = get_user($dbh,$btusernm);
	$ename = "$arr[1] $arr[0]";
	$email = $arr[2];
} else $ename="";
if ($assigned_to != "") {
	$arr = get_user($dbh,$assigned_to);
	$aname = "$arr[1] $arr[0]";
	$aemail = $arr[2];
} else $aname="";
$alink = ""; $elink = "";
//if (ereg($_SESSION["uname"],AUSERS)) {
//	$alink = "<a href='#' onclick='return bt.assign_locate(\"bugassign.php?id=$id\")'>Assign</a>";
	$alink = "<a href='#' onclick='return assign_locate($id)'>Assign</a>";
	$elink = <<<END
<a href="#" onclick="return bt.bugedit(event,$id);">Edit record</a>
-- <a href="#" onclick="return delete_entry($id);">Delete</a> --
END;
//}
/*
$flist=glob("attachments/$bug_id"."___*");
if ($flist) {
	foreach ($flist as $filename) {
		$fn=ereg_replace($bug_id."___","",basename($filename));
		$files.="$sep<a href='$filename'>$fn</a>";
		$sep=", ";
	}
}
*/
$nextlink="x";
$type=isset($_GET["type"]) ? $_GET["type"] : "";
if ($type == "closed") {
	$nextlink="type=closed";
}
if ($type == "bytype") {
	$nextlink="type=bytype&bug_type=".$_GET["bug_type"];
}
if ($type == "bystatus") {
	$status=$_GET["status"];
	$nextlink="type=bystatus&status=$status";
}
if ($type == "assignments") {
	$uname=$_SESSION['uname'];
	$nextlink="type=assignments";
}
if ($type == "unassigned") {
	$nextlink="type=unassigned";
}
#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
?>
<div class="bugform">
<form name="form1" method="post" action="#">
<input type="hidden" name="update_list" id="update_list" value="0">
<input type="hidden" name="update_log" id="update_log" value="0">
<input type="hidden" name="id" id="id" value="<?php echo $id ?>">
</form>
<fieldset>
	<legend>BugTrack Record</legend>
	<label>ID:</label>
	<div class="fields2"><?php echo $bug_id; ?></div><br class="clear">
	<label>Description:</label>
	<div class="fields2"><?php echo $descr; ?></div><br class="clear">
	<label>Product or Application:</label>
	<div class="fields2"><?php echo $product; ?></div><br class="clear">
	<label>Bug Type:</label>
	<div class="fields2"><?php echo $bt; ?></div><br class="clear">
	<label>Status:</label>
	<div class="fields2"><?php echo $sarr[$status]; ?></div><br class="clear">
	<label>Priority:</label>
	<div class="fields2"><?php echo $parr[$priority]; ?></div><br class="clear">
	<label>Comments:</label>
	<div class="fields2"><?php echo nl2br(addlinks($comments)); ?></div><br class="clear">
	<label>Solution:</label>
	<div class="fields2"><?php echo nl2br(addlinks($solution)); ?></div><br class="clear">
	<label>Attachments:</label>
	<div class="fields2"><div id="filesDiv"><?php echo $files; ?></div></div><br class="clear">
	<label>Entry By:</label>
	<div class="fields2"><a href="mailto:<?php echo $email; ?>"><?php echo $ename; ?></a></div><br class="clear">
	<label>Assigned To:</label>
	<div class="fields2"><div id="assignedDiv"><a href="mailto:<?php echo $aemail; ?>"><?php echo $aname; ?></a></div> <?php echo $alink; ?></div><br class="clear">
	<label>Entry Date/Time:</label>
	<div class="fields2"><?php echo $edtm; ?></div><br class="clear">
	<label>Update Date/Time:</label>
	<div class="fields2"><?php echo $udtm; ?></div><br class="clear">
	<label>Closed Date/Time:</label>
	<div class="fields2"><?php echo $cdtm; ?></div><br class="clear">
</fieldset>
<p align="center">
<?php echo $elink ?>
<a href="bt.buglist.php?<?php echo $nextlink; ?>">Show list</a>
-- <a href="#" onclick="return bt.email_bug(<?php echo $id; ?>);">Email Bug</a>
</p>
<div id="worklogDiv">
<?php
$rows = $db->getWorkLogEntries($id);
$count = count($rows);
echo "<p align='center'>$count Worklog entries found -- <a href='#' onclick='return bt.add_worklog(event,$id);'>Add</a><p>\n";
if ($count > 0):
?>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<?php
	foreach ($rows as $row) {
		$email = "";
		list($wid,$bid,$btusernm,$comments,$entry_dtm)=$row;
		if ($btusernm != "") {
			$arr = get_user($dbh,$btusernm);
			$ename = "$arr[1] $arr[0]";
			$email = $arr[2];
		} else $ename="";
?>
<tr><td><b>Date/Time: <?php echo date("m/d/Y g:i a",strtotime($entry_dtm)); ?>, By: <a href="mailto:<?php echo $email ?>"><?php echo $ename; ?></a></b></td></tr>
<tr><td cellspan="2"><?php echo nl2br(addlinks($comments)); ?></td></tr>
<?php
	}
endif;
?>
</table>
</div>
<script type="text/javascript">get_files(<?php echo $id ?>);</script>
