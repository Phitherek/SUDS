<html>
<head>
<title>Phitherek_' s SUDS - Główny plik systemu - ten tytuł można później zmienić</title>
<META http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie dołączyć plik stylu CSS -->
</head>
<body>
<?php
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	$dball=mysql_query("SELECT * FROM ".$prefix."files_main");
	$rows=mysql_num_rows($dball);
	if($rows != NULL) {
		for($id = 1; $id <= $rows; $id++) {
			$query=mysql_query("SELECT filename FROM ".$prefix."files_main WHERE id=".$id);
			$filename=mysql_fetch_array($query);
		$query=mysql_query("SELECT desc FROM ".$prefix."files_main WHERE id=".$id);
		$desc=mysql_fetch_array($query);
		if($filename != NULL) {
		if($desc != NULL) {
		?>
		<a class = "suds_link_ok" href="suds_files/<?php echo $filename; ?>"><?php echo $desc; ?></a><br />
		<?php
		} else {
		?>
		<a class = "suds_link_nodesc" href="suds_files/<?php echo $filename; ?>"><?php echo $filename; ?></a><br />
		<?php
		}
		} else if($desc != NULL) {
		?>
		<p class="suds_desconly"><?php echo $desc; ?> (Brak odnośnika!)</p><br />
		<?php
		} else {
		?>
		<p class="suds_broken">Zły wpis (brak odnośnika i opisu)!</p><br />
		<?php
		}
		$query=mysql_query("SELECT added FROM ".$prefix."files_main WHERE id=".$id);
		$added=mysql_fetch_array($query);
		?>
		<p class="suds_date">Ostatnia modyfikacja pliku: <?php echo $added['added']; ?></p><br /><br />
		<?php
		}
	} else {
	?>
<p class="suds_info">Brak rekordów w bazie danych</p>
<?php
	}
	mysql_close($baza);
} else {
?>
<p class="suds_error">Plik ustawień nie istnieje! Czy na pewno uruchomiłeś install.php?</p>
<?php
}
?>
<a class="suds_admin" href="suds_mod.php" title="Moderacja">Moderacja</a><br />
<hr />
<p class="suds_footer">Powered by SUDS | &copy; 2010 by Phitherek_</p>
</body>
</html>
