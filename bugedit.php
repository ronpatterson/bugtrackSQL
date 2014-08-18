<?php
// bugedit.php
// Ron Patterson, WildDog Design
// SQLite version
#require("btsession.php");
#print_r($_SESSION);

# return a standard <select> for a lookup table
function retselect2 ($dbh, $name, $tab, $def) {
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n<option value='0'>None</option>\n";
	$sql = "select * from $tab where active='y' order by descr";
	$stmt = $dbh->query($sql);
    while ($row = $stmt->fetchArray(SQLITE3_NUM)) {
		list($cd,$descr,$active) = array($row[0],$row[1],$row[2]);
		if ($cd == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$cd'$chk>$descr</option>\n";
	}
	$out .= "</select>\n";
	return $out;
}

// connect to the database 
require("bugcommon.php");
$dbh = $db->getHandle();

$usernm = $_SESSION["user_id"];
// $arr = get_user($usernm);
$uname = $_SESSION["user_nm"];
// $entry_id = $arr[0];
$email = $_SESSION["email"];
// $usernm = "admin";
// $uname = "BugTrack Administrator";
// $email = "ronlpatterson@me.com";
#require("myhead1.php");
$action = "change";
extract($_POST);
$id = isset($id) ? intval($id) : "";
if ($id == "") {
	$action="add";
}
if ($action == "add") {
	$but1="Add new Bug entry";
	$ttl="Add Record";
	$bug_id="TBD";
	$group=$_SESSION["group"];
	$descr="";
	$product = "";
	$bug_type="";
	$stat="Open<input type='hidden' name='status' id='status' value='o'>";
	$status = "o";
	$priority="2";
	$comments="";
	$solution="";
	$ename=$uname;
	$assigned_to="rlpatter";
	$aname="TBD";
	$alink="";
	$files = "Upload attachments after add";
	//$files = "Upload attachments after add<br /><a href='#' onclick='return add_file();'>Upload file</a>";
	$edtm=date("m-d-Y H:i");
	$update_dt="NULL";
	$udtm = "";
	$cdtm = "";
} else {
	$but1="Change Bug entry";
	$ttl="Edit Record";
	$action = "change";

	// execute query 
	$arr = $db->getBug($id);
	//list($id,$descr,$product,$btusernm,$bug_type,$status,$priority,$comments,$solution,$assigned_to,$bug_id,$entry_dtm,$update_dtm,$closed_dtm,$edtm,$udtm,$cdtm) = $arr;
	if (empty($arr)) die("ERROR: Record was not found ($id)!");
	//print_r($arr);
	extract($arr);
	// extract the group code
	$group = ereg_replace("[^A-Z]","",$bug_id);
	$descr = stripslashes($descr);
	$product = stripslashes($product);
	$comments = stripslashes($comments);
	$solution = stripslashes($solution);
	$edtm = $entry_dtm != "" ? date("m/d/Y g:i a",strtotime($entry_dtm)) : "";
	$udtm = $update_dtm != "" ? date("m/d/Y g:i a",strtotime($update_dtm)) : "";
	$cdtm = $closed_dtm != "" ? date("m/d/Y g:i a",strtotime($closed_dtm)) : "";
	/*
	$descr = htmlentities($descr);
	$product = htmlentities($product);
	*/
	$stat=retselectarray('status',$sarr,$status);
// 	if ($btusernm != "") {
// 		$arr = get_user($dbh, $btusernm);
// 		$ename = "$arr[2] $arr[1]";
// 		$email = $arr[3];
// 	} else {
		$ename=""; $email="";
//	}
// 	if ($assigned_to != "") {
// 		$arr = get_user($dbh, $assigned_to);
// 		$aname = "$arr[2] $arr[1]";
// 		$aemail = $arr[3];
// 	} else {
		$aname=""; $aemail="";
//	}
	$alink="";
// 	if (ereg($_SESSION["uname"],AUSERS))
// 		$alink="<a href='#' onclick='return assign_locate($id)'>Assign</a>";
	$files=""; $sep="";
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
}
$grp=retselect2($dbh,"group","bt_groups",$group);
$btypes=retselect2($dbh,"bug_type","bt_type",$bug_type);
$pri=retselectarray('priority',$parr,$priority);
//$grp=retselectarray('group',$grparr,$group);
?>
<div style="text-align: left; width: 580px;">
<form name="bt_form1" id="bt_form1" method="post" enctype="multipart/form-data"><br>
	<input type="hidden" name="action2" id="action2" value="<?php echo $action; ?>" />
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="bug_id" id="bug_id" value="<?php echo $bug_id; ?>" />
	<input type="hidden" name="user_nm" id="user_nm" value="<?php echo $usernm; ?>">
	<input type="hidden" name="email" id="email" value="<?php echo $email; ?>">
	<input type="hidden" name="ename" id="ename" value="<?php echo $ename; ?>">
	<input type="hidden" name="uname" id="uname" value="<?php echo $uname; ?>">
	<input type="hidden" name="oldstatus" id="oldstatus" value="<?php echo $status; ?>">
	<input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo $assigned_to; ?>">
	<input type="hidden" name="update_list" id="update_list" value="0">
	<fieldset>
		<legend> BugTrack Record </legend>
		<label>ID:</label>
		<div class="fields2"><?php echo $bug_id; ?></div><br class="clear">
		<label for="group"><span class="required">*</span>Group:</label>
		<div class="fields2"><?php echo $grp; ?></div><br class="clear">
		<label for="descr"><span class="required">*</span>Description:</label>
		<div class="fields2"><input type="text" name="descr" id="descr" size="40" value="<?php echo htmlentities($descr); ?>"></div><br class="clear">
		<label for="product"><span class="required">*</span>Product or Application:</label>
		<div class="fields2"><input type="text" name="product" id="product" size="40" value="<?php echo htmlentities($product); ?>"></div><br class="clear">
		<label for="bug_type"><span class="required">*</span>Bug Type:</label>
		<div class="fields2"><?php echo $btypes; ?></div><br class="clear">
		<label for="status">Status:</label>
		<div class="fields2"><?php echo $stat; ?></div><br class="clear">
		<label for="priority"><span class="required">*</span>Priority:</label>
		<div class="fields2"><?php echo $pri; ?></div><br class="clear">
		<label for="comments"><span class="required">*</span>Comments:</label>
		<div class="fields2"><textarea name="comments" id="comments" rows="4" cols="40" wrap="virtual"><?php echo htmlentities($comments); ?></textarea></div><br class="clear">
		<label for="solution">Solution:</label>
		<div class="fields2"><textarea name="solution" id="solution" rows="4" cols="40" wrap="virtual"><?php echo htmlentities($solution); ?></textarea></div><br class="clear">
		<label>Attachments:</label>
		<div class="fields2"><div id="filesDiv"><?php echo $files; ?></div></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><?php echo $ename; ?></div><br class="clear">
		<label>Assigned To:</label>
		<div class="fields2"><div id="assignedDiv"><?php echo $aname; ?></div> <?php echo $alink; ?></div><br class="clear">
		<label>Entry Date/Time:</label>
		<div class="fields2"><?php echo $edtm; ?></div><br class="clear">
		<label>Update Date/Time:</label>
		<div class="fields2"><?php echo $udtm; ?></div><br class="clear">
		<label>Closed Date/Time:</label>
		<div class="fields2"><?php echo $cdtm; ?></div><br class="clear">
		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="<?php echo $but1; ?>"> <input
 type="button" id="cancel1" value="Cancel"></div><br class="clear">
	</fieldset>
	<br>
	<div class="required" style="font-size: 9pt;" align="center">* Required fields</div>
</form>
</div>
<?php if ($action == "change"): ?>
<script type="text/javascript">get_files(<?php echo $id ?>);</script>
<?php endif; ?>
