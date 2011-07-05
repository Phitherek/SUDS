<html>
<head>
<title>Phitherek_' s SUDS - MOD: Categories - Główny plik systemu - ten tytuł można później zmienić</title>
<META http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie dołączyć plik stylu CSS -->
</head>
<body>
<?php
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	$dball=mysql_query("SELECT * FROM ".$dbprefix."files_main");
	$rows=mysql_num_rows($dball);
	if($rows != NULL) {
		$catall=mysql_query("SELECT * FROM ".$prefix."files_categories");
		$catrows=mysql_num_rows($dball);
		if($catrows == NULL) {
			?>
			<h3 class="suds_category">Bez kategorii:</h3><hr />
			<?php
		for($id = 1; $id <= $rows; $id++) {
			$query=mysql_query("SELECT filename FROM ".$dbprefix."files_main WHERE id=".$id);
			$filename=mysql_fetch_array($query);
		$query=mysql_query("SELECT `desc` FROM ".$dbprefix."files_main WHERE id=".$id);
		$desc=mysql_fetch_array($query);
		if($filename['filename'] != NULL) {
		if($desc['desc'] != NULL) {
		?>
		<a class = "suds_link_ok" href="suds_files/<?php echo rawurlencode($filename['filename']); ?>"><?php echo $desc['desc']; ?></a><br />
		<?php
		} else {
		?>
		<a class = "suds_link_nodesc" href="suds_files/<?php echo rawurlencode($filename['filename']); ?>"><?php echo $filename['filename']; ?></a><br />
		<?php
		}
		} else if($desc['desc'] != NULL) {
		?>
		<p class="suds_desconly"><?php echo $desc['desc']; ?> (Brak odnośnika!)</p><br />
		<?php
		} else {
		?>
		<p class="suds_broken">Zły wpis (brak odnośnika i opisu)!</p><br />
		<?php
		}
		$query=mysql_query("SELECT added FROM ".$dbprefix."files_main WHERE id=".$id);
		$added=mysql_fetch_array($query);
		?>
			<p class="suds_date">Ostatnia modyfikacja pliku: <?php echo $added['added']; ?></p><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
		<input type="hidden" name="id" value=<?php echo $id; ?> />
		<input type="submit" value="Edytuj" />
		</form>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_delete" method="post">
		<input type="hidden" name="id" value=<?php echo $id; ?> />
		<input type="submit" value="Usuń" />
		</form>
		<br /><br />
		<?php
		}
		} else {
			for($catid = 0; $catid < $catrows; $catid++) {
				if($catid == 0) {
				?>
			<h3 class="suds_category">Bez kategorii:</h3><hr />
			<?php
			$query=mysql_query("SELECT `id`,`filename`,`desc`,`added` FROM ".$prefix."files_main WHERE `category`=0");
			while($row = mysql_fetch_array($query)) {
		if($row['filename'] != NULL) {
		if($row['desc'] != NULL) {
		?>
		<a class = "suds_link_ok" href="suds_files/<?php echo $row['filename']; ?>"><?php echo $row['desc']; ?></a><br />
		<?php
		} else {
		?>
		<a class = "suds_link_nodesc" href="suds_files/<?php echo $row['filename']; ?>"><?php echo $row['filename']; ?></a><br />
		<?php
		}
		} else if($row['desc'] != NULL) {
		?>
		<p class="suds_desconly"><?php echo $row['desc']; ?> (Brak odnośnika!)</p><br />
		<?php
		} else {
		?>
		<p class="suds_broken">Zły wpis (brak odnośnika i opisu)!</p><br />
		<?php
		}
		?>
		<p class="suds_date">Ostatnia modyfikacja pliku: <?php echo $row['added']; ?></p><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Edytuj" />
		</form>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_delete" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Usuń" />
		</form>
		<br /><br />
		<?php
			}
			?>
			<hr />
			<?php
				} else {
				$query=mysql_query("SELECT `category` FROM ".$dbprefix."files_categories WHERE `id`=".$catid);
				$category=mysql_fetch_array($query);
				?>
				<h3 class="suds_category">Kategoria: <?php echo $category['category']; ?></h3><hr />
				<?php
				$query=mysql_query("SELECT `id`,`filename`,`desc`,`added` FROM ".$dbprefix."files_main WHERE `category`=".$catid);
			while($row = mysql_fetch_array($query)) {
		if($row['filename'] != NULL) {
		if($row['desc'] != NULL) {
		?>
		<a class = "suds_link_ok" href="suds_files/<?php echo $row['filename']; ?>"><?php echo $row['desc']; ?></a><br />
		<?php
		} else {
		?>
		<a class = "suds_link_nodesc" href="suds_files/<?php echo $row['filename']; ?>"><?php echo $row['filename']; ?></a><br />
		<?php
		}
		} else if($row['desc'] != NULL) {
		?>
		<p class="suds_desconly"><?php echo $row['desc']; ?> (Brak odnośnika!)</p><br />
		<?php
		} else {
		?>
		<p class="suds_broken">Zły wpis (brak odnośnika i opisu)!</p><br />
		<?php
		}
		?>
		<p class="suds_date">Ostatnia modyfikacja pliku: <?php echo $row['added']; ?></p><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Edytuj" />
		</form>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_delete" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Usuń" />
		</form>
		<br /><br />
		<?php
				}
				?>
				<hr />
				<?php
			}
		}
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
<p class="suds_footer">Powered by SUDS | &copy; 2010-2011 by Phitherek_<br />
MOD: Categories | &copy; 2010-2011 by Phitherek_</p>
</body>
</html>
