<?php
function slm_register($redirect=1,$redirectpage="login.php") {
?>
<h1>Rejestracja w systemie SLM</h1><br />
<?php
if($_POST['action'] == "register") {
	$action = NULL;
	if($_POST['regpass'] == $_POST['regpasscheck']) {
	$file = "slm_users/".$_POST['username'].".php";
	$f=fopen($file, 'w');
	flock($f, LOCK_EX);
	fputs($f, '<?php'."\n");
	fputs($f, '$pass="'.$_POST['regpass'].'";'."\n");	
	fputs($f, '$type="user";'."\n");
	fputs($f, '?>');
	flock($f, LOCK_UN);
	fclose($f);
	?>
	<p class="slm_success">Rejestracja zakończona!</p><br />
	<a class="slm_link" href="<?php echo $redirectpage; ?>" alt="redirect">Kliknij tutaj, jeżeli nie zadziała automatyczne przekierowanie</a><br /><br />
	<script type="text/javascript">
	window.location.href = "<?php echo $redirectpage; ?>";
	</script>
	<?php
	} else {
	?>
	<p class="slm_error">Niezgodne hasła!</p><br />
	<?php
	$action = "second";	
	}
} if($_POST['action'] == "checkusername" OR $action == "second") {
	$action = NULL;
	if(file_exists("slm_users/".$_POST['username'].".php")) {
		?>
		<p class="slm_error">Błąd SLM: Użytkownik już istnieje!</p><br /><br />
		<?php
		$action = "first";
	} else {
	?>
	<p class="slm_text">Teraz wpisz swoje hasło i kliknij &quot;Rejestruj!&quot;</p><br /><br />
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Nazwa użytkownika: <?php echo $_POST['username']; ?><br />
Hasło: <input type="password" name="regpass" /><br />
Potwierdź hasło: <input type="password" name="regpasscheck" /><br />
<input type="hidden" name="username" value="<?php echo $_POST['username']; ?>" />
<input type="hidden" name="action" value="register" />
<input type="submit" value="Rejestruj!" />
</form>
	<?php	
	}
} if($action == "first" OR $_POST['action'] == NULL)  {
?>
<p class="slm_text">Wpisz preferowaną nazwę użytkownika i kliknij &quot;Sprawdź dostępność!&quot;</p><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Nazwa użytkownika: <input type="text" name="username" /><br />
<input type="hidden" name="action" value="checkusername" />
<input type="submit" value="Sprawdź dostępność!" />
</form>
<?php
}
}
?>
