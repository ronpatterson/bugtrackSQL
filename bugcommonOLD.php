<?php
# bugcommon.php - common library functions

$allowed = array("html","htm","doc","docx","rtf","pdf","txt","xls","xlsx","gif","jpg","jpeg","png","eps","mov","ppt","pps","sql","zip","tgz");
$maxsize = 500*1024*1024;
$maxnamesize = 60;

$UsersArr = array(
"ron"=>"1,Patterson,Ron,ron@wilddogdesign.com",
"janie"=>"2,McCarthy,Janie,janie@wilddogdesign.com",
"guest"=>"3,User,Guest,webmaster@wilddogdesign.com"
);


function repl($matches) {
	$x=$matches[2];
        // must have at least one period
        if (!ereg("\.",$x)) return $x;
	// check for possibly just slash separators
	if (ereg("^[_A-Za-z0-9]+([/][_A-Za-z0-9]+)*$",$x)) return $x;
        // check for possibly just slash separators
        if (ereg("^[_A-Za-z0-9]+([/][_A-Za-z0-9]+)*$",$x)) return $x;
        // check for possibly just a decimal number
        if (ereg("^[0-9]+(\.[0-9]*-?)*$",$x)) return $x;
        // check for just a letter . and letter/digit
        if (ereg("^[A-Za-z]\.[A-Za-z0-9]$",$x)) return $x;
        // check for just a letter . and letter...
        if (ereg("^[A-Za-z](\.[A-Za-z])+$",$x)) return $x;
	// might be an email address
	if (ereg("@",$x))
		return "<a href='mailto:$x'>$x</a>";
	elseif (ereg("^https",$matches[0]))
		return "<a href='https://$x'>$x</a>";
	else
		return "<a href='http://$x'>$x</a>";
}

# return a string with possible added links
function addlinks ($str) {
	$x=htmlspecialchars($str);
	#$x=eregi_replace("(http://)*(([-a-z0-9]+\.[-a-z0-9]+)+)","<a href='http://\\2'>\\2</a>",$x);
	#$x=eregi_replace("([-a-z0-9.]+@[-a-z0-9]\.+[-a-z0-9]+)","<a href='mailto:\\1'>\\1</a>",$x);
	$x=preg_replace_callback("~(https*://|)([_A-Za-z0-9]+[@./][_A-Za-z0-9]+([@./][_A-Za-z0-9]+)*[/]*)+~","repl",$x);
	return $x;
}
# return t/f for possble option
function checkopt ($id, $opt) {
	if ($id == "" or $opt == "") return false;
	$result = mysql_query("select concat(opts,',',opts2) from metaman.d20_person where empnbr=$id");
	$opts = mysql_result($result,0);
	mysql_free_result($result);
	if (substr_count($opts,$opt) == 1) return true;
	return false;
}
# return a standard <select> for a lookup table
function retselect ($name, $tab, $def) {
	$sql = "select * from $tab where type_id>0 and type_id<999 and active='y' order by type_desc";
	$result = mysql_query($sql);
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n";
	#if ($def == 0)
	#	$out .= "<option value='0' selected>None\n";
	#else
	#	$out .= "<option value='0'>None\n";
	while ($arr = mysql_fetch_array($result)) {
		list($id,$desc,$active) = $arr;
		if ($id == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$id'$chk>$desc\n";
	}
	mysql_free_result($result);
	if ($def == 999)
		$out .= "<option value=999 selected>Other\n";
	else
		$out .= "<option value=999>Other\n";
	$out .= "</select>\n";
	return $out;
}

# return a standard <select> for a lookup table (full name)
function retselectfull ($name, $tab, $def) {
	$sql = "select * from metaman.$tab where type_id>0 and type_id<999 and active='y' order by full_name";
	$result = mysql_query($sql);
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n";
	#if ($def == 0)
	#	$out .= "<option value='0' selected>None\n";
	#else
	#	$out .= "<option value='0'>None\n";
	while ($arr = mysql_fetch_array($result)) {
		list($id,$desc,$fname,$active) = $arr;
		if ($id == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$id'$chk>$fname\n";
	}
	mysql_free_result($result);
	if ($def == 999)
		$out .= "<option value=999 selected>Other\n";
	else
		$out .= "<option value=999>Other\n";
	$out .= "</select>\n";
	return $out;
}

# return a standard <select> for a lookup array
function retselectarray ($name, $arr, $def) {
	reset($arr);
	$out = "<select name='$name' id='$name'><option value=' '>--Select one--\n";
	#if ($def == 0)
	#	$out .= "<option value='0' selected>None\n";
	#else
	#	$out .= "<option value='0'>None\n";
	while (list($key,$val) = each($arr)) {
		if ($key == $def) $chk=" selected"; else $chk="";
		$out .= "<option value='$key'$chk>$val\n";
	}
	$out .= "</select>\n";
	return $out;
}

# return standard <select>'s for date entry (def date is yyyy-mm-dd)
function selectdate ($name, $def, $yrmin=1990) {
	$dyr = ''; $dmon = ''; $dday = '';
	if ($def != "") {
		$dyr = substr($def,0,4);
		$dmon = substr($def,5,2);
		$dday = substr($def,8,2);
	}
	$out = "<select name='".$name."_mon' id='".$name."_mon'>\n<option value=''>--Month--\n";
	$mons = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	for ($i=1; $i<=12; $i++) {
		$mm = sprintf("%02d",$i);
		if ($i == $dmon)
			$out .= "<option value=$mm selected>$mons[$i]\n";
		else
			$out .= "<option value=$mm>$mons[$i]\n";
	}
	$out .= "</select><select name=".$name."_day id='".$name."_day'>\n<option value=''>--Day--\n";
	for ($i=1; $i<=31; $i++) {
		$dd = sprintf("%02d",$i);
		if ($i == $dday)
			$out .= "<option value=$dd selected>$i\n";
		else
			$out .= "<option value=$dd>$i\n";
	}
	$out .= "</select><select name=".$name."_yr id='".$name."_yr'>\n<option value=''>--Year--\n";
	for ($i=$yrmin; $i<=2020; $i++) {
		if ($i == $dyr)
			$out .= "<option selected>$i\n";
		else
			$out .= "<option>$i\n";
	}
	$out .= "</select>\n";
	if ($def == "") $chk=" checked"; else $chk="";
	$out .= "<input type='checkbox' name='".$name."_null' id='".$name."_null' value='y'$chk><font size='-1'>Unknown</font>\n";
	return $out;
}

# return standard yes/no entry boxes
function yesno ($name, $def) {
	$ychk = ""; $nchk = "";
	if (strtolower($def) == "y")
		$ychk = " checked";
	if (strtolower($def) == "n")
		$nchk = " checked";
	return "<table border=1 cellspacing=0 cellpadding=3><tr><td>
<input type=radio name='$name' id='{$name}_y' value='y'$ychk><font size='-1'>Yes</font></td><td>
<input type=radio name='$name' id='{$name}_n' value='n'$nchk><font size='-1'>No</font></td></tr></table>\n";
}

# return Yes/No values
function retyesno ($val) {
	if (strtolower($val) == "y")
		return "Yes";
	elseif (strtolower($val) == "n")
		return "No";
	else
		return "Unknown";
}

# handle general slash quoting
function slashem ($val) {
	if (!ini_get('magic_quotes_gpc'))
		return addslashes($val);
	return $val;
}
function quotem ($val) {
	if (!ini_get('magic_quotes_gpc'))
		return "'".addslashes($val)."'";
	return "'$val'";
}

# determine the entry location
function get_loc ($id) {
	$result = mysql_query("select type_desc from metaman.d20_loc where type_id=".intval($id));
	$loc = mysql_result($result,0);
	mysql_free_result($result);
	return $loc;
}

// determine user info
function get_user ($uname) {
	global $UsersArr;
// 	$result = mysql_query("select user_id,user_lnm,user_fnm,user_email from mega_user where user_nm='$uname'");
// 	$arr = mysql_fetch_array($result);
// 	mysql_free_result($result);
	return explode(",", $UsersArr[$uname]);
}

# return string with initial caps based on exclusion rules
# Thanks to Justin@gha.bravepages.com
function limited_ucfirst($text, $min_word_len = 3, 
						 $always_cap_first = true,
						 $exclude = 
	Array("of","a","the ","and","an",
	"or","nor","but","is",
	"if","then","else","when","up",
	"at","from","by","on",
	"off","for","in","out","over",
	"to","into", "with")) {

	// Allows for the specification of the minimum length 
	//Ê of characters each word must be in order to be capitalized

	// Make sure words following punctuation are capitalized
	$text = str_replace(
		Array("(", "-", ".", "?", ",",":","[",";","!"), 
		Array("( ", "- ", ". ", "? ", ", ",": ","[ ","; ","! "), $text);

	$words = explode (" ", strtolower($text));
	$count = count($words); 
	$num = 0; 

	while ($num < $count) { 
		if (strlen($words[$num]) >= $min_word_len
			&& array_search($words[$num], $exclude) === false)
			$words[$num] = ucfirst($words[$num]); 
		$num++; 
	}

	$text = implode(" ", $words); 
	$text = str_replace(
		Array("( ", "- ", ". ", "? ", ", ",": ","[ ","; ","! "),
		Array("(", "-", ".", "?", ",",":","[",";","!"), $text);

	// Always capitalize first char if cap_first is true
	if ($always_cap_first) {
		if (ctype_alpha($text[0]) && ord($text[0]) <= ord("z") 
			&& ord($text[0]) > ord("Z"))
			$text[0] = chr(ord($text[0]) - 32);
	}

	return $text;
}
?>
