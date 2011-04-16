<?php
function prefixinclude($prefixflie="suds_prefix.php") {
global $prefixexists;
if($_POST['setprefix'] == 1) {
$prefixfile=fopen($prefixflie,"w");
flock($prefixfile, LOCK_EX);
fputs($prefixfile, '<?php'."\n");
fputs($prefixfile, '$prefix="'.$_POST['prefix'].'";'."\n");
fputs($prefixfile, '?>');
flock($prefixfile, LOCK_UN);
fclose($prefixfile);
if(file_exists($prefixflie)) {
echo("Prefiks został zapisany pomyślnie!<br />");	
} else {
echo("Nie udało się zapisać pliku z prefiksem! Sprawdź uprawnienia katalogu i spróbuj ponownie!<br />");	
}
}
if(file_exists($prefixflie)) {
include($prefixflie);
$prefixexists = true;
} else {
$prefixexists = false;	
}
if($prefixexists == false) {
echo("Ze względów bezpieczeństwa wymagane jest podanie prefiksu dla tej instalacji SLM. NIGDY nie instaluj dwóch systemów z tym samym prefiksem! Jeżeli jest to twoja pierwsza i jedyna instalacja SLM, zaleca się pozostawienie domyślnego prefiksu.<br />");
?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="text" name="prefix" value="suds_" /><br />
<input type="hidden" name="setprefix" value="1" />
<input type="submit" value="Ustaw prefiks i kontynuuj" />
</form>
<?php
die();
}
}
?>
