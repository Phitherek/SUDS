<?php
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie wstawić plik stylu CSS -->
<?php
if($_GET['action'] == "show") {
	$action="show";
} else {
$action="list";	
}
if($action == "show") {
	if(isset($_GET['catid'] )) {
$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	if($_GET['catid'] == 0) {
		?>
		<title>Bez kategorii - powered by CategoryView Extension for Phitherek_' s SUDS</title>
		</head>
		<body>
		<?php
				$query=mysql_query("SELECT `id`,`filename`,`desc`,`added` FROM ".$dbprefix."files_main WHERE `category`=0");
				$crows = mysql_num_rows($query);
				if($crows != NULL) {
				?>
			<h3 class="suds_category">Bez kategorii:</h3><hr />
			<?php
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
		<br /><br />
		<?php
			}
				} else {
				?>
				<p class="suds_info">Brak plików w tej kategorii</p><br />
				<?php
				}
				?>
				<hr />
				<?php
	} else {
	$query=mysql_query("SELECT * FROM ".$dbprefix."files_categories WHERE id=".$_GET['catid']);
	$catinfo=mysql_fetch_array($query) or die("Nie udało się poprawnie pobrać danych o kategorii");
	?>
	<title>Kategoria: <?php echo $catinfo['category']; ?> - powered by CategoryView Extension for Phitherek_' s SUDS</title>
	</head>
	<body>
	<?php
	$query=mysql_query("SELECT `category` FROM ".$dbprefix."files_categories WHERE `id`=".$_GET['catid']);
				$category=mysql_fetch_array($query);
				$query=mysql_query("SELECT `id`,`filename`,`desc`,`added` FROM ".$dbprefix."files_main WHERE `category`=".$_GET['catid']);
				$crows = mysql_num_rows($query);
				if($crows != NULL) {
				?>
				<h3 class="suds_category">Kategoria: <?php echo $category['category']; ?></h3><hr />
				<?php
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
		<br /><br />
		<?php
	}
				} else {
				?>
				<p class="suds_info">Brak plików w tej kategorii</p><br />
				<?php
				}
				?>
				<hr />
				<?php
	}
	?>
	<a class="suds_admin" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=list">Lista kategorii CategoryView</a><br /><hr />
	<?php
	mysql_close($baza);
	} else {
	?>
	<p class="suds_info">Brak ID kategorii! Wyświetlam listę...</p><br />
	<?php	
	}
}
if($action == "list") {
?>
<title>Lista kategorii - powered by CategoryView Extension for Phitherek_' s SUDS</title>
</head>
<body>
<?php
?>
<h3 class="suds_category">Lista kategorii</h3><hr />
<a class="catlink" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=show&catid=0">Bez kategorii</a><br />
<?php
$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
$catall=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$catrows=mysql_num_rows($catall);
		for($catid = 1; $catid <= $catrows; $catid++) {
		$query=mysql_query("SELECT `category` FROM ".$dbprefix."files_categories WHERE `id`=".$catid);
		$catinfo=mysql_fetch_array($query) or die("Nie udało się poprawnie pobrać danych o kategorii");
		?>
		<a class="catlink" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=show&catid=<?php echo $catid; ?>"><?php echo $catinfo['category']; ?></a><br />
		<?php
		}
		?>
		<hr />
		<?php
		mysql_close($baza);
		}
} else {
?>
<html>
<head>
<title>Błąd - powered by CategoryView Extension for Phitherek_' s SUDS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie wstawić plik stylu CSS -->
</head>
<body>
<p class="suds_error">Plik ustawień nie istnieje! Czy na pewno uruchomiłeś install.php?</p>
<?php
}
?>
<a class="suds_main_link" href="suds.php">Indeks systemu SUDS</a><br />
<a class="suds_admin" href="suds_mod.php">Moderacja</a><br />
<hr />
<p class="suds_footer">Powered by CategoryView Extension for Phitherek_' s SUDS | &copy; 2011 by Phitherek_</p>
</body>
</html>
