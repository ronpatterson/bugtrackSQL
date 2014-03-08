<?php
// bugadmin2.php
// Ron Patterson, WildDog Design
// SQLite version

$active_y = $active_n = "";
$active_y = $rec["active"] == "y" ? " checked" : "";
$active_n = $rec["active"] != "y" ? " checked" : "";
?>
<fieldset>
<legend>User Add/Edit</legend>
<form name="bt_user_form" id="bt_user_form_id">
<table id="bt_user_tbl2" border="0" cellspacing="0" cellpadding="2">
<tr><th align="right">UID</th><td><?php echo $rec["uid"] ?></td></tr>
<tr><th align="right">Last Name</th><td><input type="text" name="lname" value="<?php echo $rec["lname"] ?>"></td></tr>
<tr><th align="right">First Name</th><td><input type="text" name="fname" value="<?php echo $rec["fname"] ?>"></td></tr>
<tr><th align="right">Email</th><td><input type="text" name="email" size="40" value="<?php echo $rec["email"] ?>"></td></tr>
<tr><th align="right">Active</th><td><input type="radio" name="active" value="y"<?php echo $active_y ?>>Yes<input type="radio" name="active" value="n"<?php echo $active_n ?>>No</td></tr>
<tr><th align="right">Roles</th><td><input name="roles" value="<?php echo $rec["roles"] ?>"></td></tr>
</table>
<input type="submit" value="Save">
</form>
</fieldset>
