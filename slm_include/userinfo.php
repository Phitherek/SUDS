<?php
function slm_userinfo($showlink=1,$showunlogged=1,$loginpage="login.php",$logoutpage="logout.php") {
	global $prefixexists;
	if(!$prefixexists) {
	include("slm_include/prefixinclude.php");
prefixinclude("../suds_prefix.php");
	}
session_start();
if (!isset($_SESSION[$prefix.'started'])) {
	session_regenerate_id();
	$_SESSION[$prefix.'started'] = true;
}
if($_SESSION[$prefix.'slm_loggedin'] == 1) {
?>
<p class="slm_text">Jesteś zalogowany/a za pomocą systemu SLM jako: <?php echo $_SESSION[$prefix.'slm_username']; ?>!<br />
<?php
if($showlink == 1) {
?>
<a class="slm_link" href="<?php echo $logoutpage; ?>" alt="Logout">Kliknij tutaj, aby się wylogować</a></p><br /><br />
<?php
} else {
?>
<br />
<?php
}
} else if($showunlogged == 1) {
?>
<p class="slm_text">Nie jesteś zalogowany/a za pomocą systemu SLM<br />
<?php
if($showlink == 1) {
?>
<a class="slm_link" href="<?php echo $loginpage; ?>" alt="Logout">Kliknij tutaj, aby się zalogować</a></p><br /><br />
<?php
} else {
?>
<br />
<?php
}
}
}
?>
