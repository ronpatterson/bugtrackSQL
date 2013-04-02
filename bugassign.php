<?php
// bugassign.php
// Ron Patterson, WildDog Design
// PDO version
require("../session.php");
# bugassign.php
# Ron Patterson
#print_r($_SESSION);
// connect to the database 
require("dbdefpdo.php");
$ttl="BugTrack Assignment Search";
require("bugcommon.php");
$id=intval($_GET["id"]);
# show the selection page
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
	<title>BugTrack Assignment Search</title>
	<meta name="author" content="Ron Patterson, ASD20">
	<link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
	<script type="text/javascript" src="../jquery.js"></script>
	<script type="text/javascript" src="fieldedits.js"></script>
</head>
<body background="" bgcolor="white" onload="document.form1.lname.focus();">
<div class="bugform">
	<table align="center">
	<tr><td><img src="BugTrack.gif" alt="BugTrack"></td><td width="30">&nbsp;</td>
	<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
	</table><br>

<form name="form9" id="form9" enctype="x-www-form-encoded">
<h5>You can search on any of the fields listed below. The more
information you fill in, the narrower the search becomes.
<input type="hidden" name="id" value="<? echo $id; ?>"></h5>

<fieldset>
	<legend>BugTrack Assignment Search</legend>
	<label for="lname">Last Name:</label>
	<div class="fields2"><input type="text" name="lname" id="lname" size="22"></div><br class="clear">
	<label for="fname">First Name:</label>
	<div class="fields2"><input type="text" name="fname" id="fname" size="22"></div><br class="clear">
	<label>&nbsp;</label>
	<div class="fields2"><input type="submit" name="find" value="Start Search"> <input
 type="reset"></div><br class="clear">
	<div id="results"></div>
	</fieldset>
</form>
<p><a href="#" onclick="close_win(window.opener.w);">Close window</a></p>
<? require("footer.php"); ?>
</body>
</html>
