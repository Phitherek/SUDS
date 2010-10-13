<?php
function slm_logout($redirect="index.php") {
	session_start();
	if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
	if($_SESSION['slm_loggedin'] == 1) {
	$_SESSION['slm_loggedin'] = 0;
	$_SESSION['slm_username'] = NULL;
	$_SESSION['slm_type'] = NULL;
	$_SESSION['slm_userfile_type'] = NULL;
?>
<p class="slm_success">Wylogowano z systemu SLM!</p><br /><br />
<a class="slm_link" href="<?php echo $redirect; ?>" alt="redirect">Kliknij tutaj, jeżeli nie zadziała automatyczne przekierowanie</a><br /><br />
<script type="text/javascript">
window.location.href = "<?php echo $redirect; ?>";
</script>
<?php
	} else {
	?>
<p class="slm_success">Nie jesteś zalogowany!</p><br /><br />
<a class="slm_link" href="<?php echo $redirect; ?>" alt="redirect">Kliknij tutaj, jeżeli nie zadziała automatyczne przekierowanie</a><br /><br />
<script type="text/javascript">
window.location.href = "<?php echo $redirect; ?>";
</script>
<?php	
}
}
?>
