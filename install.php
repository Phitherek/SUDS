<html>
<head>
<title>Phitherek_' s SUDS - Instalacja</title>
<META http-equiv="content-type" content="text/html; charset=utf-8" />
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
include("suds_prefix.php");
$prefixexists = true;
} else {
$prefixexists = false;	
}
if($prefixexists == true) {
session_start();
if (!isset($_SESSION[$prefix.'started'])) {
session_regenerate_id();
$_SESSION[$prefix.'started'] = true;
}
if($_POST['beginpass']=="sNx36PmO") { 
	$_SESSION[$prefix.'login'] = 1;
	session_regenerate_id();
}
if($_SESSION[$prefix.'login'] == 1) {
$step = $_POST['go'];
if($step == 4) {
if($_POST['modpass']!=NULL) {	
if($_POST['modpass'] != $_POST['modcheck']) {
$step = 3;
echo("Hasło moderatora nie zgadza się z powtórzonym hasłem moderatora!");
}
} else {
$step = 3;
echo("Nie wpisałeś hasła moderatora!");
}
}
if($step == 1) {
?>
<h1>Ustawianie MySQL</h1><br />
Czy chcesz utworzyć nową bazę danych MySQL?<br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="go" value="2" />
<input type="submit" value="Tak" />
</form>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="go" value="3" />
<input type="submit" value="Nie" />
</form>

<?php
} else if($step == 2) {
?>
<h1>Ustawianie MySQL</h1><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Adres serwera MySQL: <input type="text" name="serek" value="localhost" /><br />
Nazwa użytkownika MySQL: <input type="text" name="dbuser" value="root" /><br />
Hasło MySQL: <input type="password" name="dbpass" /><br />
Nazwa nowej bazy danych: <input type="text" name="dbname" value="suds" /><br />
<input type="hidden" name="go" value="3" />
<input type="hidden" name="newdb" value="1" />
<input type="submit" value="Wykonaj" />
</form>
<?php
} else if($step == 3) {
if($_POST['newdb'] == 1) {
echo("<h1>Ustawianie MySQL</h1><br />");
$baza=mysql_connect($_POST['serek'],$_POST['dbuser'],$_POST['dbpass']) 
or die("Połączenie z serwerem MySQL nieudane!");
echo("Połączono z serwerem MySQL!<br />");
$zapytanie=mysql_query("CREATE DATABASE ".$_POST['dbname']);
if($zapytanie == 1) {
echo("Nowa baza danych utworzona poprawnie!<br />");
} else {
?>
Błąd podczas tworzenia nowej bazy danych!<br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="go" value="2" />
<input type="submit" value="Powrót" />
</form>
<?php
}
echo("Zamykam połączenie z serwerem MySQL...<br />");
mysql_close($baza);
?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Adres serwera MySQL: <input type="text" name="serek" value="<?php echo $_POST['serek']; ?>" /><br />
Nazwa użytkownika MySQL: <input type="text" name="dbuser" value="<?php echo $_POST['dbuser']; ?>" /><br />
Hasło MySQL: <input type="password" name="dbpass" /><br />
Nazwa bazy danych: <input type="text" name="dbname" value="<?php echo $_POST['dbname']; ?>" /><br />
Prefiks tabeli: <input type="text" name="dbprefix" value="suds_" /><br />
Hasło moderatora: <input type="password" name="modpass" /><br />
Powtórz hasło moderatora: <input type="password" name="modcheck" /><br />
<input type="hidden" name="go" value="4" />
<input type="submit" value="Wykonaj i zapisz" />
</form>
<?php
} else {
?>
<h1>Ustawianie MySQL</h1><br /><br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
Adres serwera MySQL: <input type="text" name="serek" value="localhost" /><br />
Nazwa użytkownika MySQL: <input type="text" name="dbuser" value="root" /><br />
Hasło MySQL: <input type="password" name="dbpass" /><br />
Nazwa bazy danych: <input type="text" name="dbname" value="suds" /><br />
Prefiks tabeli: <input type="text" name="dbprefix" value="suds_" /><br />
Hasło moderatora: <input type="password" name="modpass" /><br />
Powtórz hasło moderatora: <input type="password" name="modcheck" /><br />
<input type="hidden" name="go" value="4" />
<input type="submit" value="Wykonaj i zapisz" />
</form>

<?php
}
} else if($step==4) {
echo("<h1>Ustawianie MySQL i zapisywanie ustawień</h1><br /><br />");
$baza=mysql_connect($_POST['serek'],$_POST['dbuser'],$_POST['dbpass'])
or die("Połączenie z serwerem MySQL nieudane!");
echo("Połączono z serwerem MySQL!<br />");
mysql_select_db($_POST['dbname']);
$zapytanie=mysql_query("CREATE TABLE `".$_POST['dbprefix']."files_main` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `filename` VARCHAR(70), `desc` VARCHAR(100), `added` TIMESTAMP)");
if($zapytanie == 1) {
echo("Tabela została utworzona poprawnie!<br />");
} else {
?>
Błąd! Tabela nie została utworzona! Ustawienia nie zostaną zapisane!<br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="hidden" name="go" value="3" />
<input type="submit" value="Powrót" />
</form>
<?php
$fail=1;
}
if($fail!=1) {
echo("Tworzenie podkatalogu dla plików SUDS...<br />");
mkdir("suds_files");
$ustawienia=fopen("suds_settings.php","w");
flock($ustawienia,LOCK_EX);
fputs($ustawienia,'<?php '."\n");
fputs($ustawienia,'$serek="'.$_POST['serek'].'"'.";\n");
fputs($ustawienia,'$dbuser="'.$_POST['dbuser'].'"'.";\n");
fputs($ustawienia,'$dbpass="'.$_POST['dbpass'].'"'.";\n");
fputs($ustawienia,'$dbname="'.$_POST['dbname'].'"'.";\n");
fputs($ustawienia,'$dbprefix="'.$_POST['dbprefix'].'"'.";\n");
fputs($ustawienia,'$modpass="'.$_POST['modpass'].'"'.";\n");
fputs($ustawienia,'?>');
flock($ustawienia,LOCK_UN);
fclose($ustawienia);
if(file_exists("suds_settings.php")) {
echo("Ustawienia zostały zapisane!<br />");
} else {
echo("Nie można było zapisać ustawień! Sprawdź, czy katalog z plikami systemu SUDS ma uprawnienia 777 (lub rwxrwxrwx), jeżeli nie, to zmień je, a następnie usuń tabelę (prefix)_files_main (i bazę danych) z serwera MySQL, zakończ sesję przeglądarki, a następnie uruchom ten plik install.php ponownie!<br />");
}
echo("<br /> Koniec instalacji! WAŻNE: Skasuj ten plik install.php z serwera, aby nikt nie mógł zmienić Twoich ustawień!");
}
}
} else {
echo("Aby kontynuować, podaj hasło, które jest w pliku informacyjnym dołączonym do systemu: <br />");
?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="password" name="beginpass" /><br />
<input type="hidden" name="go" value="1" />
<input type="submit" value="Kontynuuj" />
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
