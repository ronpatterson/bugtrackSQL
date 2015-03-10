<?php
ini_set("display_errors", "on");
require_once("btsession.php");
// bugtrack.php - BugTrack main
// Ron Patterson
// SQLite version
// if ($_SESSION['user_id']=="") {
// 	die("<html><b>Not logged in!!<p><a href=login.php>Login</a></b></html>");
// }
date_default_timezone_set('America/Denver');
$ttl = "BugTrack";
$uname = (isset($_SESSION["user_nm"])) ? $_SESSION["user_nm"] : "rlpatter";
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
<link rel="stylesheet" href="/lib/scripts/DataTables/DataTables-1.10.5/media/css/jquery.dataTables.css">
<script type="text/javascript" src="/lib/scripts/DataTables/DataTables-1.10.5/media/js/jquery.js"></script>
<script type="text/javascript" src="/lib/scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/scripts/DataTables/DataTables-1.10.5/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="bugtrack2.js"></script>
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

<div id="content_div" style="display: none;">
<span id="btc_types"></span>
<input type="button" name="bytype" value="Type List" onclick="return bt.buglist(event,'bytype');">
<span id="btc_status"></span>
<input type="submit" name="bystatus" value="Status List" onclick="return bt.buglist(event,'bystatus');">
</div>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
</div>

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

<div id="bt_user_heading" style="position: absolute; width: 30em; top: 15px; right: 1em; text-align: right; font-size: 9pt; display: none;">
Welcome <span id="bt_user_name_top"><?php echo $uname ?></span> <a href="#" onclick="return bt.logout_handler();">Logout</a>
</div>

<div id="bt_bugs_list" style="display: none;">
	<center>
	<p>Click on Edit to see details. Click column title to sort.</p>
	<div style="width: 650px;">
	<table id="bt_tbl" class="display" border="1" cellspacing="0" cellpadding="3" width="100%">
	<thead>
	<tr>
	<th>ID</th><th>Description</th><th>Date entered</th><th>Status</th><th>&nbsp;</th>
	</tr>
	</thead>
	</table>
	</div>
	</center>
</div>

<div id="bugshow_div" class="bugform" style="display: none;">
	<form name="bugshow_form1">
	<input type="hidden" name="update_list" id="update_list" value="0">
	<input type="hidden" name="update_log" id="update_log" value="0">
	<input type="hidden" name="bugshow_id" id="bugshow_id" value="">
	</form>
	<fieldset>
		<legend>BugTrack Record</legend>
		<label>ID:</label>
		<div class="fields2"><span id="bug_id_v"><span></div><br class="clear">
		<label>Description:</label>
		<div class="fields2"><span id="descr_v"></span></div><br class="clear">
		<label>Product or Application:</label>
		<div class="fields2"><span id="product_v"</span></div><br class="clear">
		<label>Bug Type:</label>
		<div class="fields2"><span id="bt_v"></span></div><br class="clear">
		<label>Status:</label>
		<div class="fields2"><span id="status_v"></span></div><br class="clear">
		<label>Priority:</label>
		<div class="fields2"><span id="priority_v"></span></div><br class="clear">
		<label>Comments:</label>
		<div class="fields2"><span id="comments_v"></span></div><br class="clear">
		<label>Solution:</label>
		<div class="fields2"><span id="solution_v"></span></div><br class="clear">
		<label>Attachments:</label>
		<div class="fields2"><div id="filesDiv"><span id="files_v"></span></div></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><a href="mailto:"><span id="ename_v"></span></a></div><br class="clear">
		<label>Assigned To:</label>
		<div class="fields2"><div id="assignedDiv"><a href="mailto:"><span id="aname_v"></span></a></div> </div><br class="clear">
		<label>Entry Date/Time:</label>
		<div class="fields2"><span id="edtm_v"></span></div><br class="clear">
		<label>Update Date/Time:</label>
		<div class="fields2"><span id="udtm_v"></span></div><br class="clear">
		<label>Closed Date/Time:</label>
		<div class="fields2"><span id="cdtm_v"></span></div><br class="clear">
	</fieldset>
	<p align="center">
	<button onclick="return bt.edit_bug(event);">Edit Bug</button>
	<button onclick="return bt.email_bug(event);">Email Bug</button>
	</p>
</div>

<div id="bugedit_div" style="text-align: left; width: 580px; display: none;">
	<form name="bt_form1" id="bugedit_form1"><br>
	<input type="hidden" name="action2" id="action2" value="" />
	<input type="hidden" name="bid" id="bid" value="" />
	<input type="hidden" name="bug_id" id="bug_id" value="" />
	<input type="hidden" name="user_nm" id="user_nm" value="">
	<input type="hidden" name="email" id="email" value="">
	<input type="hidden" name="ename" id="ename" value="">
	<input type="hidden" name="uname" id="uname" value="">
	<input type="hidden" name="oldstatus" id="oldstatus" value="">
	<input type="hidden" name="assigned_to" id="assigned_to" value="">
	<input type="hidden" name="update_list" id="update_list" value="0">
	<fieldset>
		<legend> BugTrack Record </legend>
		<label>ID:</label>
		<div class="fields2"><span id="bugedit_id"</span></div><br class="clear">
		<label for="group"><span class="required">*</span>Group:</label>
		<div class="fields2"><span id="bt_grp"></span></div><br class="clear">
		<label for="descr"><span class="required">*</span>Description:</label>
		<div class="fields2"><input type="text" name="descr" size="40" value=""></div><br class="clear">
		<label for="product"><span class="required">*</span>Product or Application:</label>
		<div class="fields2"><input type="text" name="product" size="40" value=""></div><br class="clear">
		<label for="bug_type"><span class="required">*</span>Bug Type:</label>
		<div class="fields2"><span id="btypes_s"></span></div><br class="clear">
		<label for="status">Status:</label>
		<div class="fields2"><span id="status_s"></span></div><br class="clear">
		<label for="priority"><span class="required">*</span>Priority:</label>
		<div class="fields2"><span id="priority_s"></span></div><br class="clear">
		<label for="comments"><span class="required">*</span>Comments:</label>
		<div class="fields2"><textarea name="comments" rows="4" cols="40" wrap="virtual"></textarea></div><br class="clear">
		<label for="solution">Solution:</label>
		<div class="fields2"><textarea name="solution" rows="4" cols="40" wrap="virtual"></textarea></div><br class="clear">
		<label>Attachments:</label>
		<div class="fields2"><div id="filesDiv"></div><span id="bfiles"></span></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><span id="euser"></span></div><br class="clear">
		<label>Assigned To:</label>
		<div class="fields2"><div id="assignedDiv"></div></div><br class="clear">
		<label>Entry Date/Time:</label>
		<div class="fields2"><span id="edtm" class="bt_date"></span></div><br class="clear">
		<label>Update Date/Time:</label>
		<div class="fields2"><span id="udtm" class="bt_date"></span></div><br class="clear">
		<label>Closed Date/Time:</label>
		<div class="fields2"><span id="cdtm" class="bt_date"></span></div><br class="clear">
		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="SAVE"> <input
 type="button" id="cancel1" value="Cancel"></div><br class="clear">
	</fieldset>
	<br>
	<div class="required" style="font-size: 9pt;" align="center">* Required fields</div>
	</form>
	<div id="bugedit_errors"></div>
</div>

<div id="bt_users_list" style="display: none;">
	<input type="button" id="bt_admin_users_add" value="Add User">
	<table id="bt_user_tbl" class="display" border="1" cellspacing="0" cellpadding="2">
	<thead>
	<tr>
	<th>UID</th>
	<th>Name</th>
	<th>Email</th>
	<th>Roles</th>
	<th>Act</th>
	<th>&nbsp;</th>
	</tr>
	</thead>
	</table>
	</div>
	</center>
</div>

<div id="bt_users_form" style="display: none;">
	<fieldset>
	<legend>User Add/Edit</legend>
		<form name="bt_user_form" id="bt_user_form_id">
		<table id="bt_user_tbl2" border="0" cellspacing="0" cellpadding="2">
		<tr><th align="right">UID</th><td><span id="uid1"></span></td></tr>
		<tr><th align="right">Last Name</th><td><input type="text" name="lname" value=""></td></tr>
		<tr><th align="right">First Name</th><td><input type="text" name="fname" value=""></td></tr>
		<tr><th align="right">Email</th><td><input type="text" name="email" size="40" value=""></td></tr>
		<tr><th align="right">Password</th><td><input type="password" name="pw" value=""><input type="hidden" name="pw2" value=""></td></tr>
		<tr><th align="right">Active</th><td><label class="yesno"><input type="radio" name="active" value="y">Yes</label> <label class="yesno"><input type="radio" name="active" value="n">No</label></td></tr>
		<tr><th align="right">Roles</th><td><label class="yesno"><input type="radio" name="roles"   value="admin">Admin</label> <label class="yesno"><input type="radio" name="roles"  value="ro">RO</label> <label class="yesno"><input type="radio" name="roles"  value="user">User</label></td></tr>
		<tr><th align="right">Group</th><td><div id="bt_groups"></div></td></tr>
		</table>
		<input type="submit" value="Save">
		<input type="hidden" name="uid" value="">
		</form>
	</fieldset>
	<div id="bt_admin_errors"></div>
</div>

<div id="bughelp_div" style="display: none;">
	<p>TBA - Some help info</p>
</div>

<br><br><center><?php require("footer.php"); ?><center>

</body>
</html>
