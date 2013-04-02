<?php
ini_set("display_errors", "on");
require_once("btsession.php");
# bugtrack.php - BugTrack main
# Ron Patterson
if ($_SESSION['user_id']=="") {
	die("<html><b>Not logged in!!<p><a href=login.php>Login</a></b></html>");
}
// connect to the database 
require_once("dbdef.php");
require("BugTrack.class.php");
$db = new BugTrack($dbpath);
$ttl = "BugTrack";
?>
<html>
<head>
	<meta charset="iso-8859-1" />
	<title>BugTrack</title>
	<meta name="author" content="Ron Patterson, ASD20">
	<link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
<link rel="stylesheet" href="/lib/scripts/css/custom-theme/jquery-ui-1.10.1.custom.css">
<!--
<style type="text/css" title="currentStyle">
	@import "/lib/scripts/DataTables/media/css/demo_page.css";
	@import "/lib/scripts/DataTables/media/css/demo_table.css";
    @import "/lib/scripts/DataTables/media/css/jquery.dataTables.css";
</style>
-->
<link rel="stylesheet" href="/lib/scripts/DataTables/media/css/jquery.dataTables.css">
<script type="text/javascript" src="/lib/scripts/DataTables/media/js/jquery.js"></script>
<script type="text/javascript" src="/lib/scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/scripts/DataTables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="bugtrack.js"></script>
<script type="text/javascript" src="fieldedits.js"></script>
</head>
<body background="" bgcolor="#e0e0e0">
<center>
<table>
<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
</table><br>
<?php
$db = null;
?>
<div id="bt_button_div">
	<span id="bt_refresh_btn">Refresh List</span>
	<span id="bt_add_btn">Add New</span>
	<span id="bt_admin_btn">Admin</span>
	<span id="bt_help_btn">Help</span>
</div>
<div id="content_div"></div>
<div id="errors"></div>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
<?php require("footer.php"); ?>
</html>
