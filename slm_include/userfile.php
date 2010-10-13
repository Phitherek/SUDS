<?php
function slm_userfile_read($username) {
	if(file_exists("slm_users/".$username.".php")) {
		include("slm_users/".$username.".php");
		$_SESSION['slm_userfile_type'] = $type;
		return 0;
	} else {
	return 1;	
	}
}

function slm_userfile_winit($username) {
session_start();
if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
global $f;
if(file_exists("slm_users/".$username.".php")) {
		include("slm_users/".$username.".php");
		unlink("slm_users/".$username.".php");
		$f=fopen("slm_users/".$username.".php", 'w');
		flock($f, LOCK_EX);
		fputs($f, '<?php'."\n");
		fputs($f, '$pass="'.$pass.'";'."\n");
		fputs($f, '$type="'.$type.'";'."\n");
		$_SESSION['slm_userfile_winit'] = 1;
		return 0;
} else {
return 1;	
}
}

function slm_userfile_puts($name, $value) {
	session_start();
if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
global $f;
	if($_SESSION['slm_userfile_winit'] == 1) {
	fputs($f, 'global $'.$name.';'."\n".'$'.$name.'="'.$value.'";'."\n");
	return 0;	
	} else {
	return 1;	
	}
}

function slm_userfile_wclose() {
	session_start();
if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
global $f;
	if($_SESSION['slm_userfile_winit'] == 1) {
	fputs($f, '?>'."\n");
	flock($f, LOCK_UN);
	fclose($f);
	$_SESSION['slm_userfile_winit'] = 0;
	return 0;	
	} else {
	return 1;	
	}
}
?>
