<?php
// bugedit3.php
// Ron Patterson, WildDog Design
// PDO version
//require("../session.php");
#print_r($_SESSION);
$entry_id=12444; $ename="Ron Patterson";

extract($_POST);
$id = isset($id) ? intval($id) : "";
if ($id == "") {
	echo "<b>ID not provided</b>\n";
	exit;
}
// connect to the database 
require("bugcommon.php");
$dbh = $db->getHandle();

$usernmx = $_SESSION["user_id"];
$arr = get_user($dbh,$usernmx);
$ename = "$arr[1] $arr[0]";
//$entry_id = $arr[0];
$email = $arr[2];

// execute query 
$arr = $db->getBug($id);
if (count($arr) == 0) die("ERROR: Bug not found ($id)");
	//list($id,$descr,$product,$btusernm,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$bug_id,$entry_dtm,$update_dtm,$closed_dtm,$edtm,$udtm,$cdtm) = $arr;
$xxx = $id;
extract($arr);
$id = $xxx;
if ($user_nm != "") {
	$arr = get_user($dbh,$user_nm);
	$ebname = "$arr[0] $arr[1]";
	$ebemail = $arr[2];
} else {
	$ebname=""; $ebemail="";
}
if ($assigned_to != "") {
	$arr = get_user($dbh,$assigned_to);
	$aname = "$arr[0] $arr[1]";
	$aemail = $arr[2];
} else {
	$aname=""; $aemail="";
}
#require("myhead1.php");
$action="add";
$but1="Add new Worklog entry";
$ttl="Add Worklog Record";
$bid="TBD";
$wcomments="";
#$dvd_title = ereg_replace("\"","\\&quot;",$dvd_title);
?>
<div class="bugform">
<form id="bt_form2" name="bt_form2" method="post"><br>
	<input type="hidden" name="action2" value="<?php echo $action; ?>">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="bug_id" value="<?php echo $bug_id; ?>">
	<input type="hidden" name="usernm" value="<?php echo $usernmx; ?>">
	<input type="hidden" name="ename" value="<?php echo $ename; ?>">
	<input type="hidden" name="ebname" value="<?php echo $ebname; ?>">
	<input type="hidden" name="aname" value="<?php echo $aname; ?>">
	<input type="hidden" name="email" value="<?php echo $email; ?>">
	<input type="hidden" name="ebemail" value="<?php echo $ebemail; ?>">
	<input type="hidden" name="aemail" value="<?php echo $aemail; ?>">
	<input type="hidden" name="descr" value="<?php echo $descr; ?>">
	<fieldset>
		<legend>BugTrack Worklog Record</legend>
		<label>ID:</label>
		<div class="fields2"><?php echo $bug_id; ?></div><br class="clear">
		<label>Description:</label>
		<div class="fields2"><?php echo $descr; ?></div><br class="clear">
		<label>Bug Comments:</label>
		<div class="fields2"><?php echo nl2br(addlinks($comments)); ?></div><br class="clear">
		<label for="comments"><span class="required">*</span>Worklog Comments:</label>
		<div class="fields2"><textarea name="comments" id="comments" rows="10" cols="40"
 wrap="virtual"></textarea></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><?php echo $ename; ?></div><br class="clear">
		<label>Bug Entry Date/Time:</label>
		<div class="fields2"><?php echo date("m/d/Y g:i a",strtotime($entry_dtm)); ?></div><br class="clear">
		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="<?php echo $but1; ?>"> <input
 type="button" id="cancel2" value="Cancel"></div><br class="clear">
		<div id="message"></div>
	</fieldset>
	<br>
	<div class="required" style="font-size: 9pt;" align="center">* Required fields</div>
</form>
</div>
