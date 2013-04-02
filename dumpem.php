<?php
require("BugTrackMy.class.php");
$db = new BugTrack();
echo $db->buildTablesList();
?>