<?php
require("../session.php");
# bugassign1.php - Directory staff search results
# Ron Patterson
require("bugcommon.php");
extract($_POST);
#print_r($_POST); exit;

if ($fname == " " && $lname == "") {
	echo "<b>No search data entered!!</b>";
	exit;
}
// connect to the database 
require("dbdef.php");
#require("d20head1.php");
$bid=$id;
$lname = slashem($lname);
$fname = slashem($fname);

$crit="";
if ($lname != "") $crit .= " and user_lnm like '%$lname%'";
if ($fname != "") $crit .= " and user_fnm like '%$fname%'";

$sql="select count(*) from mega_user where 1=1 $crit limit 100";
$result=mysql_query($sql);
$found = mysql_result($result,0);
#echo mysql_error();

if ($found > 0) {
	$sql="select *,concat(user_fnm,' ',user_lnm) full_name from mega_user where 1=1 $crit order by user_lnm,user_fnm limit 400";
	$result=mysql_query($sql);
}
if ($found > 400) $found = 400;
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
<?
while ($r = mysql_fetch_object($result)):
	$email=split("@",$r->user_email);
	$ename=$email[0];
	$uname=urlencode($r->full_name);
	$id = $r->user_id;
?>
  <tr>
    <td valign="TOP" height="16"><a href="#" onclick="return opener.do_assign(<? echo $bid; ?>,<? echo $id; ?>);"><? echo "$r->user_lnm, $r->user_fnm"; ?></a></td> 
    <td valign="TOP" height="16"><? echo $r->user_nm; ?></td> 
    <td valign="TOP" height="16"><? echo $r->user_email; ?></td> 
  </tr>
<?
endwhile;
?>
</table>
<div id="results2"></div>
<?

endif;

mysql_free_result($result);
mysql_close($link);
?>
