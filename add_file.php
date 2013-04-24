<?php
// add_file.php - add an upload file to the space
// Ron Patterson, WildDog Design
// PDO version

ini_set("display_errors","0");

require("btsession.php");
#print_r($_SESSION);

$method = strtolower($_SERVER["REQUEST_METHOD"]);

if ($method == "post") {
	extract($_POST);
}
else {
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : "";
}

require_once("dbdef.php");
require("bugcommon.php");
require("BugTrack.class.php");
$ttl = "BugTrack Attachment";
$usernm = "rlpatter";

$bug = new BugTrack($dbpath);

#if ($id == "") die("No ID provided");

/*
if (!@file_exists($udir1)) {
	@mkdir($udir1);
}
if (!file_exists($dir)) {
	@mkdir($dir);
	$msg .= "Your Appload area is initialized.<br>\n";
}
*/

// get meeting dates

?>
<html>
<head>
	<title><?php echo $ttl ?></title>
	<link href="bugtrack.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/lib/scripts/DataTables/media/js/jquery.js"></script>
	<script type="text/javascript" src="add_file.js"></script>
	<style type="text/css">
	<!--
	a:link,a:visited,a:active { color: blue; text-decoration: none }
	a:hover { color: red }
	#notice2, #errors { color: red; font-size: 10; }
	#msg3 { font-size: 11; }
	-->
	</style>
</head>
<body style="font-family: Sans-Serif,Helvetica,Arial">
<?php
$fileedit = 1;
$filelink = 1;
$fileupload = 1;
extract($_POST);
#print_r($_POST); exit;
$msg = "";

// check for subdirectories
$uplink = "";
$dir = isset($rootdir) ? $rootdir : "";
$sub = isset($_GET['sub']) ? trim($_GET['sub']) : "";
if ($sub != "") {
	// setup a parent directory link
	$sub2 = "";
	if (strstr($sub,"/")) $sub2 = substr($sub,0,strrpos($sub,"/"));
	$uplink = "<tr><td colspan='5'><small>$sub&nbsp;&nbsp;<a href='filer.php?sub=".urlencode($sub2)."'>../</a></small></td></tr>\n";
	$dir .= $sub."/";
}

# check for a possible uploaded file
if ($msg == "" and isset($_FILES['upfile']) and is_uploaded_file($_FILES['upfile']['tmp_name'])) {
	$ext = substr(strrchr($_FILES['upfile']['name'], "."), 1);
	#echo "here ".$_FILES['upfile']['name'];
	#echo "here ".$dir;
	echo " ";
	// check for allowed file type
	if (!in_array(strtolower($ext),$allowed)) {
		$msg .= "Unallowed document file type specified, must be of type<br>".join($allowed,",")."\n";
	}
	// check for file size
	//elseif ($_FILES['upfile']['size'] + $dirsize > $maxsize) {
	//	$msg .= "Sorry, directory overflow (max. is $maxsize)<br>\n";
	//}
	// check for allowed file name characters
	elseif (!ereg("^[-A-Za-z0-9._ ]+$",$_FILES['upfile']['name'])) {
		$msg .= "Unallowed characters in file name specified, must be only letters, numbers, ., -, _<br>\n";
	}
	// check for max. file name size
	elseif (strlen($_FILES['upfile']['name']) > $maxnamesize) {
		$msg .= "File name exceeds maximum size ($maxnamesize character max.)<br>\n";
	}
	elseif ($_FILES['upfile']['name'] != "upload.php") {
		#$newname = ereg_replace("[^A-Za-z0-9._]","_",$_FILES['upfile']['name']);
		#print_r($_FILES); exit;
		$newname = ereg_replace("[^A-Za-z0-9._]","_",$_FILES['upfile']['name']);
		//if (@move_uploaded_file($_FILES['upfile']['tmp_name'], $dir . $newname)) {
			// update file list when finished
		$new_filename = $_FILES['upfile']['tmp_name']."_".$id;
		move_uploaded_file($_FILES['upfile']['tmp_name'],$new_filename);		
		// read the file into memory
		$size = $_FILES['upfile']['size'];
		//$raw_file = addslashes(fread(fopen($new_filename, "r"), $size));
		$raw_file = file_get_contents($new_filename);
		//$filename = addslashes($_FILES['upfile']['name']);
		$filename = $_FILES['upfile']['name'];
		$id = $bug->addAttachment($id, $filename, $size, $raw_file);
		$msg .= "File is valid, and was successfully uploaded as {$_FILES['upfile']['name']}.\n";
		echo "<script type='text/javascript'>fini_upload();</script>";
		// clean up the tmp files
		unlink ($new_filename);
		//}
		/*
		$newname = "BOE".$doc_type.$dtarr[1].".".$ext;
		if (@move_uploaded_file($_FILES['upfile']['tmp_name'], $dir . $newname)) {
			$msg .= "File is valid, and was successfully uploaded as $newname.\n";
			#print "Here's some more debugging info:\n";
		} else {
			print "Possible file upload attack!\nHere's some more debugging info:\n";
			print_r($_FILES);
		}
		*/
	}
}
?>
<div id="errors"><?php echo $msg ?></div>
<fieldset>
	<legend>Attachment Upload</legend>
	<form name="form1" enctype="multipart/form-data" method="post" onsubmit="return upload_file();">
		<input type="hidden" name="id" id="id" value="<?php echo $id ?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="8000000">
		<input type="hidden" name="update_list" id="update_list" value="0" onchange="alert('changed='+this.value);">
		<table border="0" cellspacing="1" cellpadding="3">
			<tr><td align="right"><label for="upfile">File to upload:</label></td><td><input type='file' name='upfile' id='upfile'></td></tr>
			<tr><td></td><td><input type='submit' value='Upload File'><br><br><div id="errors"></div></td></tr>
		</table>
	</form>
	<div id="msg3">Note: Make sure that the names of your upload document files are clear
and only contain letters, numbers, underscores (_), dashes (-), periods (.), or spaces.
Also only the following file types are allowed: <?php echo join(", ",$allowed)."."; ?>
	</div>
</fieldset>
<br>
<p><a href="#" onclick="close_win(window.opener.w);">Close window</a></p>
<?php require("footer.php"); ?>
</body></html>
