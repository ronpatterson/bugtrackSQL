<?php
// Ajax module to build a file selection list

// Ron Patterson, WildDog Design
// PDO version

ini_set("display_errors", "on");

extract($_POST);
#echo "Date: $mtg_dt, type: $mtg_type";

$filelink = 1;
$fileedit = 1;

require("bugcommon.php");
require("dbdef.php");
require("BugTrack.class.php");

$bug = new BugTrack($dbpath);

# attachments are now in the db
$files="";
$rows = $bug->getBugAttachments($id);
if (count($rows) > 0)
{
	foreach ($rows as $row) {
		list($aid, $bug_id, $fname, $size, $attachment, $entry_dtm)=$row;
		$files.="<a href='get_file.php?id=$aid' target='_blank'>$fname</a> (<a href='#' onclick='return remove_file($aid);'>Remove</a>) ($size)<br />";
	}
}
if ($files == "") $files = "None";
$files .= " <a href='#' onclick='return add_file();'>Upload file</a>";
echo $files;
?>
