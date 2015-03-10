<?php
ini_set("display_errors", "on");
require_once("btsession.php");
// bugtrack.php - BugTrack main
// Ron Patterson
// SQLite version
// if ($_SESSION['user_id']=="") {
// 	die("<html><b>Not logged in!!<p><a href=login.php>Login</a></b></html>");
// }
// connect to the database
require_once("dbdef.php");
require("BugTrack.class.php");
$db = new BugTrack($dbpath);
$ttl = "BugTrack";
$uname = (isset($_SESSION["user_nm"])) ? $_SESSION["user_nm"] : "";
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
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
</div>
<?php require("footer.php"); ?>
<div id="dialog-modal" title="Basic modal dialog" style="display: none;">
	<div id="dialog-content">
		<p>Adding the modal overlay screen makes the dialog look more prominent because it dims out the page content.</p>
	</div>
	<div id="errors"></div>
</div>
<div id="dialog-login" title="Basic modal dialog" style="text-align: center; display: none;">
	<div id="login-content">
		<form name="bt_login_form" id="bt_login_form">
		<p>You are not logged in.</p>
		User name<br><input type="text" name="uid"><br>
		Password<br><input type="password" name="pw"><br>
		<input type="submit" value="Login">
		</form>
	</div>
	<div id="login_errors"></div>
</div>
<div id="bt_user_heading" style="position: absolute; width: 30em; top: 15px; right: 1em; text-align: right; font-size: 9pt; display: none;">Welcome <span id="bt_user_name_top"><?php echo $uname ?></span> <a href="#" onclick="return bt.logout_handler();">Logout</a></div>
</body>
</html>
