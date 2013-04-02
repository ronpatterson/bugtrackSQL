<?php
require("../session.php");
// bugassign1.php - Directory staff search results
// Ron Patterson, WildDog Design
// PDO version
require("bugcommon.php");
extract($_POST);
#print_r($_POST); exit;

if ($fname == " " && $lname == "") die("No search data entered!!");

$bid=$id;
$lname = slashem($lname);
$fname = slashem($fname);

reset($UsersArr);
$list = ""; $found = 0;
foreach ($UsersArr as $k=>$v) {
	list($uid,$lnm,$fnm,$email) = preg_split("/,/",$v);
	if ((trim($lname) != "" and preg_match("/".$lname."/i",$lnm)) or (trim($fname) != "" and preg_match("/".$fname."/i",$fnm))) {
		$list .= <<<END
  <tr>
    <td valign="TOP" height="16"><a href="#" onclick="return opener.do_assign($bid,'$k');">$lnm, $fnm</a></td> 
    <td valign="TOP" height="16">$k</td> 
    <td valign="TOP" height="16">$email</td> 
  </tr>
END;
	++$found;
	}
}
?>
<h5>Your search found <? echo $found; ?> listing(s). Click
on the Last Name link to assign to BugTrack record.</h5>
<?
if ($found > 0):
?>
<table width="520" border="1" cellspacing="0" cellpadding="3" class="worklog">
  <tr>
    <td width="40%" valign="TOP"><b>Name</b></td> 
    <td width="20%" valign="TOP"><b>Username</b></td> 
    <td width="40%" valign="TOP"><b>Email</b></td> 
  </tr>
  <? echo $list ?>
</table>
<div id="results2"></div>
<?

endif;
?>
