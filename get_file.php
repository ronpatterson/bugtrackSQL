<?php
// get_file.php
// Ron Patterson, WildDog Design
// PDO version
	session_cache_limiter("private, must-revalidate");
	
	$id = $_GET["id"];
	
	require("dbdef.php");
	require("BugTrack.class.php");

	$bug = new BugTrack($dbpath);

	$r = $bug->getBugAttachment($id);
	if (!$r) die("ERROR: No attachment found ($id)");
	//print_r($r); exit;
	$r = (object)$r[0];
	$hash = $r->file_hash;
	$pdir = substr($hash,0,3);
	$data = file_get_contents($bug->getAdir().$pdir."/".$hash);
	//echo base64_encode($data); exit;

	$ext=substr(strrchr($r->file_name, "."), 1); 
	switch (strtolower($ext)) {
		case "txt":
			header("Content-type: text/plain");
			break;
		case "htm":
		case "html":
			header("Content-type: text/html");
			break;
		case "gif":
			header("Content-type: image/gif");
			header("Content-Disposition: inline; filename=".'"'.$r->file_name.'"');
			break;
		case "jpg":
		case "jpeg":
			header("Content-type: image/jpeg");
			header("Content-Disposition: inline; filename=".'"'.$r->file_name.'"');
			break;
		case "png":
			header("Content-type: image/png");
			header("Content-Disposition: inline; filename=".'"'.$r->file_name.'"');
			break;
		case "pdf":
			// remove any trailing stuff
			$arr = (split(" ",$r->file_size));
			$len = $arr[0];
			header("Content-type: application/pdf");
			#header("Content-Disposition: attachment; filename=".'"'.$r->file_name.'"');
			header("Content-Disposition: inline; filename=".'"'.$r->file_name.'"');
			header("Accept-Ranges: bytes");
			header("Content-Length: $len");
			header("Expires: 0");
			break;
		case "doc":
		case "dot":
			header("Content-type: application/msword");
			header("Content-Disposition: attachment; filename=\"$r->file_name\"");
			break;
		case "xls":
			header("Content-type: application/excel");
			header("Content-Disposition: attachment; filename=\"$r->file_name\"");
			break;
		default:
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$r->file_name\"");
	}
    //echo $r->attachment;
    echo $data;
?>
