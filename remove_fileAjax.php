<?php
// Ajax module to remove an attachment file

// Ron Patterson, WildDog Design
// PDO version

require("../session.php");

extract($_POST);
#echo "Date: $mtg_dt, type: $mtg_type";

$filelink = 1;
$fileedit = 1;

require("bugcommon.php");
require("BugTrack.class.php");

$bug = new BugTrack();

# attachments are now in the db
$bug->deleteAttachment($id);
?>
SUCCESS