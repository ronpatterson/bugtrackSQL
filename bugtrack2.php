<?php
ini_set("display_errors", "on");
require_once("btsession.php");
// bugtrack.php - BugTrack main
// Ron Patterson, WildDog Design
// SQLite version
// if ($_SESSION['user_id']=="") {
// 	die("<html><b>Not logged in!!<p><a href=login.php>Login</a></b></html>");
// }
date_default_timezone_set('America/Denver');
$ttl = "BugTrack";
$uname = (isset($_SESSION["user_nm"])) ? $_SESSION["user_nm"] : "rlpatter";
$roles = (isset($_SESSION["roles"])) ? $_SESSION["roles"] : "";
//print_r($_SESSION);
?>
<html>
<head>
	<meta charset="iso-8859-1" />
	<title>BugTrack</title>
	<meta name="author" content="Ron Patterson, ASD20">
	<link type="text/css" rel="stylesheet" href="bugtrack.css" title="bt styles">
<link rel="stylesheet" href="/lib/scripts/jquery/ui-1.11/jquery-ui.min.css">
<!--
<style type="text/css" title="currentStyle">
	@import "/lib/scripts/DataTables/media/css/demo_page.css";
	@import "/lib/scripts/DataTables/media/css/demo_table.css";
    @import "/lib/scripts/DataTables/media/css/jquery.dataTables.css";
</style>
-->
<link rel="stylesheet" href="/lib/scripts/DataTables/DataTables-1.10.5/media/css/jquery.dataTables.css">
<script type="text/javascript" src="/lib/scripts/jquery/jquery-1.11.2.js"></script>
<script type="text/javascript" src="/lib/scripts/jquery/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/lib/scripts/jquery/ui-1.11/jquery-ui.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/scripts/DataTables/DataTables-1.10.5/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="bugtrack2.js"></script>
<style type="text/css">
	button:hover { font-weight: bold; }
</style>
</head>
<body background="" bgcolor="#e0e0e0">
<center>
<table>
	<tr><td><img src="BugTrack.gif" alt="BugTrack">Powered by <a href="http://www.mongodb.org/"><img src="sqlite370_banner.gif" alt="MongoDB" width="110" height="50"></a></td><td width="30">&nbsp;</td>
	<td valign="middle"><font size="+1"><b><? echo $ttl; ?></b></font></td></tr>
</table><br>
<input type="hidden" name="usernm" value="<?php echo $uname ?>" id="usernm" />
<input type="hidden" name="bid" id="bid" value="">
<input type="hidden" name="bug_id" id="bug_id" value="">
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
<p><!-- <a href=viewphp1.php><b>View PHP code modules</b></a--></p>

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
	<p>Click on Show to see details. Click column title to sort.</p>
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
	<fieldset>
		<legend>BugTrack Record</legend>
		<label>ID:</label>
		<div class="fields2"><span id="bug_id2_v"><span></div><br class="clear">
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
		<div class="fields2"><div id="filesDiv"></div> <input type="button" id="bt_assign_btn1" value="Attach File" onclick="return bt.attach_file();"></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><span id="ename_v"></span></div><br class="clear">
		<label>Assigned To:</label>
		<div class="fields2"><div id="assignedDiv1"><a href="mailto:"><span id="edit_v"></span></a></div> <input type="button" id="bt_assign_btn1" value="Assign" onclick="return bt.assign_search();"></div><br class="clear">
		<label>Entry Date/Time:</label>
		<div class="fields2"><span id="edtm_v"></span></div><br class="clear">
		<label>Update Date/Time:</label>
		<div class="fields2"><span id="udtm_v"></span></div><br class="clear">
		<label>Closed Date/Time:</label>
		<div class="fields2"><span id="cdtm_v"></span></div><br class="clear">
	</fieldset>
	<br>
	<div align="center" id="bt_show_buttons">
		<span onclick="return bt.edit_bug(event);">Edit Bug</span>
		<span onclick="return bt.delete_bug(event);">Delete Bug</span>
		<span onclick="return bt.show_email(event);">Email Bug</span>
		<span onclick="return bt.add_worklog(event);">Add Worklog</span>
	</div>
	<br>
	<fieldset>
	<legend>Bug Worklog</legend>
	<div id="bt_worklog_div"></div>
	</fieldset>
	</p>
</div>

<div id="bugedit_div" style="text-align: left; width: 580px; display: none;">
	<form name="bt_form1" id="bugedit_form1"><br>
	<input type="hidden" name="oldstatus" id="oldstatus" value="">
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
		<label>Assigned To:</label>
		<div class="fields2"><div id="assignedDiv2"></div><!--<input type="button" id="bt_assign_btn2" value="Assign" onclick="return bt.assign_search();">--></div><br class="clear">
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
	</form>
	<br>
	<div class="required" style="font-size: 9pt;" align="center">* Required fields</div>
	<div id="bugedit_errors"></div>
</div>

<div id="bt_email_div" class="bugform" style="display: none;">
	<form name="bug_email_form" id="bug_email_form"><br>
	<fieldset>
		<legend>BugTrack Record</legend>
		<label>ID:</label>
		<div class="fields2"><span id="bug_id_email"></span></div><br class="clear">
		<label>Description:</label>
		<div class="fields2"><span id="descr_email"></span></div><br class="clear">
		<label for="sendto">Send to:</label>
		<div class="fields2"><input type="text" name="sendto" size="40"></div><br class="clear">
		<label for="cc">Send copy to (CC):</label>
		<div class="fields2"><input type="text" name="cc" size="40"></div><br class="clear">
		<label for="subject">Subject:</label>
		<div class="fields2"><input type="text" name="subject" size="40" value="bug_id - descr"></div><br class="clear">
		<label for="msg2">Message to add:</label>
		<div class="fields2"><textarea name="msg2" rows="3" cols="40" wrap="virtual"></textarea></div><br class="clear">
 		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="Send Bug Message"> <input type="reset"></div><br class="clear">
	</fieldset>
	</form>
	<div id="email_errors"></div>
</div>

<div id="bt_worklog_form" class="bugform" style="display: none;">
	<form id="bt_form2" name="bt_form2" method="post"><br>
	<fieldset>
		<legend>BugTrack Worklog Record</legend>
		<label>ID:</label>
		<div class="fields2"><span id="bt_wl_bug_id"></span></div><br class="clear">
		<label>Description:</label>
		<div class="fields2"><span id="bt_wl_descr"></span></div><br class="clear">
		<label>Bug Comments:</label>
		<div class="fields2"><span id="bt_bug_comments"></span></div><br class="clear">
		<label>Public:</label>
		<div class="fields2"><label class="yesno"><input type="radio" name="wl_public" value="y">Yes</label> <label class="yesno"><input type="radio" name="wl_public" value="n">No</label></div><br class="clear">
		<label for="comments"><span class="required">*</span>Worklog Comments:</label>
		<div class="fields2"><textarea name="wl_comments" rows="10" cols="40"
 wrap="virtual"></textarea></div><br class="clear">
		<label>Entry By:</label>
		<div class="fields2"><span id="bt_wl_ename"></span></div><br class="clear">
		<label>Bug Entry Date/Time:</label>
		<div class="fields2"><span id="bt_wl_entry_dtm"></span></div><br class="clear">
		<label>&nbsp;</label>
        <div class="fields2"><input type="submit" value="SAVE"> <input
 type="button" id="cancel2" value="Cancel"></div><br class="clear">
	</fieldset>
	</form>
	<br>
	<div class="required" style="font-size: 9pt;" align="center">* Required fields</div>
	<div id="wl_errors"></div>
</div>

<div id="bt_users_search" style="display: none;">
	<form name="bt_form9" id="bt_form9">
		<h5>You can search on any of the fields listed below. The more
		information you fill in, the narrower the search becomes.
		<input type="hidden" name="bid" value=""></h5>
		<fieldset>
			<legend>BugTrack Assignment Search</legend>
			<label for="lname">Last Name:</label>
			<div class="fields2"><input type="text" name="lname" id="lname" size="22"></div><br class="clear">
			<label for="fname">First Name:</label>
			<div class="fields2"><input type="text" name="fname" id="fname" size="22"></div><br class="clear">
			<label>&nbsp;</label>
			<div class="fields2"><input type="submit" name="find" value="Start Search">
			<input type="reset"></div><br class="clear">
			<table id="bt_user_assign_tbl" class="display" border="1" cellspacing="0" cellpadding="2" width="100%">
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
		</fieldset>
	</form>
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

<div id="bt_users_form" style="display: none;">
	<fieldset>
		<legend>User Add/Edit</legend>
		<form name="bt_user_form" id="bt_user_form_id">
		<table id="bt_user_tbl2" border="0" cellspacing="0" cellpadding="2">
		<tr><th align="right">UID</th><td><input type="text" name="uid1" size="20"></td></tr>
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
