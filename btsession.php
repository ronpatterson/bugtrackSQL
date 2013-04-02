<?php
session_start();
if (!isset($_SESSION["user_id"]))
{
	$_SESSION["user_id"] = "rlpatter";
	$_SESSION["user_nm"] = "Ron Patterson";
}
?>
