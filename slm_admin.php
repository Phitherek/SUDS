<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Administracja systemem SLM</title>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
include("slm_include/prefixinclude.php");
prefixinclude("suds_prefix.php");
include("slm_include/loginform.php");
include("slm_include/adminonly.php");
include("slm_include/footer.php");
slm_loginpage_sub();
slm_adminonly("index.php", "slm_admin.php", "Strona główna panelu administracyjnego SLM");
if(file_exists("slm_install.php")) {
?>
<p class="slm_error">Poważne zagrożenie bezpieczeństwa - nie usunąłeś slm_install.php!</p><br /><br />
<?php
}
if($_POST['auth'] == 1) {
	include("slm_users/".$_SESSION[$prefix.'slm_username'].".php");
	if($_POST['authpass'] == $pass) {
	$_SESSION[$prefix.'slm_adminpanel'] = 1;	
	} else {
	?>
	<p class="slm_error">Błąd: Złe hasło!</p>
	<?php	
	}
}
if($_GET['action'] == "unauth") {
$_SESSION[$prefix.'slm_modifyuser'] = NULL;
$_SESSION[$prefix.'slm_adminpanel'] = 0;
}
if($_SESSION[$prefix.'slm_adminpanel'] == 1) {
	if($_GET['action'] == "unmoduser") {
	$_SESSION[$prefix.'slm_modifyuser'] = NULL;	
	}
	if($_POST['choice'] == 1) {
		if(file_exists("slm_users/".$_POST['modusername'].".php")) {
	$_SESSION[$prefix.'slm_modifyuser'] = $_POST['modusername'];	
		} else {
		?>
		<p class="slm_error">Błąd: Użytkownik nie istnieje!</p><br />
		<?php
		}
	}
	if($_SESSION[$prefix.'slm_modifyuser'] != NULL) {
		?>
		<h3 class="slm_header">Akcje:</h3><br />
		<a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=change" alt="change">Zmień dane użytkownika</a><br />
		<a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=ban" alt="ban">Zbanuj/Odbanuj użytkownika</a><br />
		<a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=delete" alt="delete">Usuń użytkownika</a><hr />
		<p class="slm_adminpanel_info">Wybrany użytkownik: <?php echo $_SESSION[$prefix.'slm_modifyuser']; ?> (<a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=unmoduser" alt="unmoduser">zmień</a>)(<a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=unauth" alt="unauth">wyjdź</a>)</p><hr />
		<?php
		if($_GET['action'] == "change") {
			if($_POST['changed'] == 1) {
			if($_POST['newpass'] == $_POST['newpasschk']) {
			$fn="slm_users/".$_SESSION[$prefix.'slm_modifyuser'].".php";
			unlink($fn);
			$f = fopen($fn, 'w');
			flock($f, LOCK_EX);
			fputs($f, '<?php'."\n");
			fputs($f, '$pass="'.$_POST['newpass'].'";'."\n");	
			fputs($f, '$type="'.$_POST['newtype'].'";'."\n");
			fputs($f, '?>');
			flock($f, LOCK_UN);
			fclose($f);
			?>
			<p class="slm_success">SLM: Dane zmienione pomyślnie!</p>
			<?php
			} else {
			$error = 1;	
			?>
			<p class="slm_error">Błąd SLM: Hasła się nie zgadzają!</p><br /><br />
			<?php
			}
			} else {
		include("slm_users/".$_SESSION[$prefix.'slm_modifyuser'].".php");
		if($type == "admin") {
		?>
		To konto jest kontem administratora. Nie masz uprawnień do jego edycji, można je zmienić tylko przez zmianę pliku na serwerze (do głównego administratora).
		<?php
		} else {
		?>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=change" method="post">
		Nowe hasło (pozostaw domyślne, jeżeli nie chcesz zmieniać): <input type="password" name="newpass" value="<?php echo $pass; ?>" /><br />
		Powtórz hasło: <input type="password" name="newpasschk" value="<?php echo $pass; ?>" /><br />
		Typ konta (podstawowe: admin/user): <input type="text" name="newtype" value="<?php echo $type; ?>" /><br />
		<input type="hidden" name="changed" value=1 />
		<input type="submit" value="Zmień" />
		</form>
		<?php
		}
		}
		} else if($_GET['action'] == "ban") {
			if($_POST['banaction'] == "ban") {
			$fn="slm_bans/".$_SESSION[$prefix.'slm_modifyuser'].".php";
			$f=fopen($fn,'w');
			flock($f,LOCK_EX);
			fputs($f,'<?php'."\n");
			fputs($f,'$reason="'.$_POST['banreason'].'";'."\n");
			fputs($f,'?>'."\n");
			flock($f,LOCK_UN);
			fclose($f);
			?>
			<p class="slm_success">SLM: Ban dodany pomyślnie.</p>
			<?php
			} else if($_POST['banaction'] == "unban") {
				unlink("slm_bans/".$_SESSION[$prefix.'slm_modifyuser'].".php");
				?>
			<p class="slm_success">SLM: Ban usunięty pomyślnie.</p>
			<?php
			} else {
		if(file_exists("slm_bans/".$_SESSION[$prefix.'slm_modifyuser'].".php")) {
		include("slm_bans/".$_SESSION[$prefix.'slm_modifyuser'].".php");
		?>
		<p class="slm_baninfo">Ten użytkownik jest zbanowany.<br />
		<?php
		if($reason == "no") {
		?>
		Brak powodu.</p><br />
		<?php
		} else {
		?>
		Ban z powodu: <?php echo $reason; ?></p><br />
		<?php
		}
		?>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=ban" method="post">
		<input type="hidden" name="banaction" value="unban" />
		<input type="submit" value="Odbanuj">
		</form>
		<?php
		} else {
		include("slm_users/".$_SESSION[$prefix.'slm_modifyuser'].".php");
		if($type == "admin") {
		?>
		To konto jest kontem administratora. Nie masz uprawnień do jego banowania, można je zbanować tylko przez zmianę pliku na serwerze (do głównego administratora).
		<?php
		} else {
		?>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=ban" method="post">
		Powód bana (wpisz &quot;no&quot; dla żadnego): <input type="text" name="banreason" value="no"><br />
		<input type="hidden" name="banaction" value="ban" />
		<input type="submit" value="Zbanuj">
		</form>
		<?php
		}
		}
		}
		} else if($_GET['action'] == "delete") {
			if($_POST['delete'] == 1) {
			unlink("slm_users/".$_SESSION[$prefix.'slm_modifyuser'].".php");
			$_SESSION[$prefix.'slm_modifyuser'] = NULL;
			} else {
		include("slm_users/".$_SESSION[$prefix.'slm_modifyuser'].".php");
		if($type == "admin") {
		?>
		To konto jest kontem administratora. Nie masz uprawnień do jego usuwania, można je usunąć tylko przez usunięcie danych z serwera (do głównego administratora).
		<?php
		} else {
		?>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=delete" method="post">
		<input type="hidden" name="delete" value=1 />
		<input type="submit" value="Usuń">
		</form>
		<?php
		}
		}
		}
	} else {
	?>
	<p class="slm_text">Wpisz nazwę użytkownika, którym chcesz zarządzać, lub <a class="slm_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=unauth" alt="unauth">opuść panel administracyjny</a>.</p><br /><br />
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
	<input type="text" name="modusername" /><br />
	<input type="hidden" name="choice" value=1 />
	<input type="submit" value="Wybierz" /><br />
	</form>
	<?php
	}
} else {
?>
<p class="slm_text">Aby wejść do panelu administracyjnego SLM, musisz ponownie podać swoje hasło.</p><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="password" name="authpass" /><br />
<input type="hidden" name="auth" value=1 />
<input type="submit" value="Autoryzuj" /><br />
</form>
<?php
}
slm_footer("suds.php", "Indeks systemu SUDS");
?>
</body>
</html>
