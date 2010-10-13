<?php
function slm_loginpage_main($banmode=1,$register=1,$registerpage="register.php",$redirect="index.php")
{
session_start();
if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
if($_SESSION['slm_loggedin'] == 0) {
if($_POST['login'] == 1) {
	$error=4;
if(file_exists("slm_bans/".$_POST['username'].".php")) {
	if($banmode == 1) {
		include("slm_bans/".$_POST['username'].".php");
		?>
		<p class="slm_baninfo">Twoje konto SLM zostało zbanowane!<br />
		<?php
		if($reason == "no") {
		?>
		Administrator nie podał powodu.</p>
		<?php
		} else {
		?>
		Ban z powodu: <?php echo $reason; ?></p>
		<?php
		}
		$error=3;
	}	
	} else if($banmode == 0 OR $error == 4) {
	if(file_exists("slm_users/".$_POST['username'].".php")) {
		include("slm_users/".$_POST['username'].".php");
		if($pass == $_POST['password']) {
		$error = 0;
		session_regenerate_id;
		$_SESSION['slm_loggedin'] = 1;
		$_SESSION['slm_username'] = $_POST['username'];
		$_SESSION['slm_type'] = $type;
		} else {
		$error = 2;	
		}
	} else {
	$error = 1;	
	}
	}
} else {
$error = 3;	
}
if($error == 0) {
?>
<p class="slm_success">Poprawnie zalogowano jako: <?php echo $_POST['username']; ?>!</p><br />
<script type="text/javascript">
window.location.href = "<?php echo $redirect; ?>";
</script>
<a class="slm_link" href="<?php echo $redirect; ?>" alt="Index">Kliknij tutaj, jeżeli nie zadziała automatyczne przekierowanie</a>
<?php
} else {
	if($error == 1) {
	?>
	<p class="slm_error">Błąd SLM: Podany użytkownik nie istnieje!</p><br />
	<?php	
	} else if($error == 2) {
	?>
	<p class="slm_error">Błąd SLM: Złe hasło!</p><br />
	<?php	
	}
?>
<h1 class="slm_header">Logowanie do systemu SLM</h1><br /><br />
<p class="slm_text">Po zalogowaniu uzyskasz dostęp do dodatkowych funkcji strony. <?php if($register == 1) { ?>Jeżeli nie masz jeszcze konta, <a href="<?php echo $registerpage; ?>" alt="Register">zarejestruj się</a>.<?php } ?></p><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Nazwa użytkownika: <input type="text" name="username" /><br />
Hasło: <input type="password" name="password" /><br />
<input type="hidden" name="login" value="1" />
<input type="submit" value="Zaloguj" /><br /><br />
</form>
<?php
}
} else {
?>
<p class="slm_success">Jesteś już zalogowany jako: <?php echo $_POST['username']; ?>!</p><br />
<script type="text/javascript">
window.location.href = "<?php echo $redirect; ?>";
</script>
<a class="slm_link" href="<?php echo $redirect; ?>" alt="Index">Kliknij tutaj, jeżeli nie zadziała automatyczne przekierowanie</a>
<?php	
}
}
function slm_loginpage_sub($banmode=1,$register=1,$registerpage="register.php")
{
session_start();
if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
if($_SESSION['slm_loggedin'] == 0) {
	if($_POST['login'] == 1) {
	$error=4;
if(file_exists("slm_bans/".$_POST['username'].".php")) {
	if($banmode == 1) {
		include("slm_bans/".$_POST['username'].".php");
		?>
		<p class="slm_baninfo">Twoje konto SLM zostało zbanowane!<br />
		<?php
		if($reason == "no") {
		?>
		Administrator nie podał powodu.</p>
		<?php
		} else {
		?>
		Ban z powodu: <?php echo $reason; ?></p>
		<?php
		}
		$error=3;
	}
		} else if($banmode == 0 OR $error == 4)  {
	if(file_exists("slm_users/".$_POST['username'].".php")) {
		include("slm_users/".$_POST['username'].".php");
		if($pass == $_POST['password']) {
		$error = 0;
		session_regenerate_id();
		$_SESSION['slm_loggedin'] = 1;
		$_SESSION['slm_username'] = $_POST['username'];
		$_SESSION['slm_type'] = $type;
		} else {
		$error = 2;	
		}
	} else {
	$error = 1;	
	}
		}
} else {
$error = 3;	
}
if($error == 0) {
?>
<p class="slm_success">Poprawnie zalogowano jako: <?php echo $_POST['username']; ?>!</p>
<?php
return(0);	
} else {
	if($error == 1) {
	?>
	<p class="slm_error">Błąd SLM: Podany użytkownik nie istnieje!</p><br />
	<?php	
	} else if($error == 2) {
	?>
	<p class="slm_error">Błąd SLM: Złe hasło!</p><br />
	<?php	
	}
?>
<h1 class="slm_header">Logowanie do systemu SLM</h1><br /><br />
<p class="slm_text">Aby uzyskać dostęp do tej strony, musisz być zalogowany za pomocą systemu SLM. <?php if($register == 1) { ?>Jeżeli nie masz jeszcze konta, <a href="<?php echo $registerpage; ?>" alt="Register">zarejestruj się</a>.<?php } ?></p><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Nazwa użytkownika: <input type="text" name="username" /><br />
Hasło: <input type="password" name="password" /><br />
<input type="hidden" name="login" value="1" />
<input type="submit" value="Zaloguj" /><br /><br />
</form>
<?php
die();
}
}
}
?>
