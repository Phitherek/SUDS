<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Instalacja SLM dla SUDS MOD: SLMlock</title>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
if($_POST['setprefix'] == 1) {
$prefixfile=fopen("suds_prefix.php","w");
flock($prefixfile, LOCK_EX);
fputs($prefixfile, '<?php'."\n");
fputs($prefixfile, '$prefix="'.$_POST['prefix'].'";'."\n");
fputs($prefixfile, '?>');
flock($prefixfile, LOCK_UN);
fclose($prefixfile);
if(file_exists("suds_prefix.php")) {
echo("Prefiks został zapisany pomyślnie!<br />");	
} else {
echo("Nie udało się zapisać pliku z prefiksem! Sprawdź uprawnienia katalogu i spróbuj ponownie!<br />");	
}
}
if(file_exists("suds_prefix.php")) {
if($_POST['step'] == "end") {
	if($_POST['adminpass'] == $_POST['adminpchk']) {
$path="slm_users/".$_POST['adminname'].".php";
$f=fopen($path, 'w');
flock($f, LOCK_EX);
fputs($f, '<?php'."\n");
fputs($f, '$pass="'.$_POST['adminpass'].'";'."\n");
fputs($f, '$type="admin";'."\n");
fputs($f, '?>');
flock($f, LOCK_UN);
fclose($f);
echo("Instalacja zakończona pomyślnie!");
$step="none";
	} else {
	echo("Hasła się nie zgadzają!");
	$step="setadmin";
	}
} else if($_POST['step'] == "setadmin" OR $step == "setadmin") {
mkdir("slm_users");
mkdir("slm_bans");
?>
<h1>Tworzenie konta administratora</h1><br />
Skrypt utworzy teraz konto administratora.<br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Nazwa użytkownika: <input type="text" name="adminname" value="admin" /><br />
Hasło: <input type="password" name="adminpass" /><br />
Powtórz hasło: <input type="password" name="adminpchk" /><br />
<input type="hidden" name="step" value="end" />
<input type="submit" value="Stwórz i zakończ" />
</form>
<?php
} else if($_POST['step'] == "setfolders") {
?>
<h1>Tworzenie folderów danych</h1><br />
Skrypt utworzy teraz foldery dla danych SLM - &quot;slm_users&quot; i &quot;slm_bans&quot;. Kliknij przycisk &quot;Dalej&quot;.<br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="step" value="setadmin" />
<input type="submit" value="Dalej" />
</form>
<?php
} else {
?>
<h1>Instalacja SLM</h1><br />
Witaj w skrypcie instalacyjnym Phitherek_' s SLM dla SUDS MOD: SLMlock. Zanim rozpoczniesz, upewnij się, że folder, w którym umieściłeś SLM, ma uprawnienia do zapisu dla wszystkich, najlepiej 777 (rwxrwxrwx). Jeżeli jesteś tego pewien, kliknij przycisk &quot;Dalej&quot;.<br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="step" value="setfolders" />
<input type="submit" value="Dalej" />
</form>
<?php
}
} else {
echo("Ze względów bezpieczeństwa wymagane jest podanie prefiksu dla tej instalacji SUDS. NIGDY nie instaluj dwóch systemów z tym samym prefiksem! Jeżeli jest to twoja pierwsza i jedyna instalacja SUDS, zaleca się pozostawienie domyślnego prefiksu. Prefiks zostanie zapisany nawet, jeżeli instalacja nie zostanie ukończona.<br />");
?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="text" name="prefix" value="suds_" /><br />
<input type="hidden" name="setprefix" value="1" />
<input type="submit" value="Ustaw prefiks i kontynuuj" />
</form>
<?php
}	
?>
</body>
</html>
