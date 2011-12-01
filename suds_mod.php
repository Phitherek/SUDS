<html>
<head>
<title>Phitherek_' s SUDS - MOD: Categories+ExtensionEngine - System moderacji - tytuł może być później zmieniony</title>
<META http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie dołączyć plik stylu CSS -->
</head>
<body>
<?php
if(file_exists("ee_mode.php")) {
		include("ee_mode.php");
	} else {
		$eemode = "locandrem";	
	}
	if($eemode == "local") {
		if(file_exists("ee_list")) {
	$local_eelist = file_get_contents("ee_list");
	} else {
		echo('<p class="ee_error">(ExtensionEngine)(Błąd) Nie znaleziono lokalnego pliku z listą, a tryb ustawiono na &quot;local&quot;! Skontaktuj się z administratorem!</p><br />');	
		}
	} else if($eemode == "locandrem") {
		$official_eelist = extensionengine_get_remote_list("http://www.suds.phitherek.cba.pl/download/ee/ee_list");
		if($official_eelist == 2) {
		echo('<p class="ee_error">(ExtensionEngine)(Błąd) Twój serwer nie obsługuje pobierania plików przez wbudowane funkcje PHP ani przez CURL! ExtensionEngine nie potrafi pobrać listy z serwera!</p><br />');	
		}
		if(file_exists("ee_custom")) {
		$eecustom = file_get_contents("ee_custom");
		$feecustom = fopen('data:text/plain,'.$eecustom, 'rb');
		$custom_eelists = array();
		while(($line = fgets($feecustom)) != false) {
		$custom_eelists[] = extensionengine_get_remote_list(trim($line));	
		}
		}
		if(file_exists("ee_list")) {
			$local_eelist = file_get_contents("ee_list");
		}
		} else if($eemode == "locorrem") {
		if(!file_exists("ee_list")) {
		$official_eelist = extensionengine_get_remote_list("http://www.suds.phitherek.cba.pl/download/ee/ee_list");	
		if($official_eelist == 2) {
		echo('<p class="ee_error">(ExtensionEngine)(Błąd) Twój serwer nie obsługuje pobierania plików przez wbudowane funkcje PHP ani przez CURL! ExtensionEngine nie potrafi pobrać listy z serwera!</p><br />');	
		}
		if(file_exists("ee_custom")) {
		$eecustom = file_get_contents("ee_custom");
		$feecustom = fopen('data:text/plain,'.$eecustom, 'rb');
		$custom_eelists = array();
		while(($line = fgets($feecustom)) != false) {
		$custom_eelists[] = extensionengine_get_remote_list(trim($line));	
		}	
		}
		} else {
			$local_eelist = file_get_contents("ee_list");
		}
	} else if($eemode == "remote") {
		$official_eelist = extensionengine_get_remote_list("http://www.suds.phitherek.cba.pl/download/ee/ee_list");
		if($official_eelist == 2) {
		echo('<p class="ee_error">(ExtensionEngine)(Błąd) Twój serwer nie obsługuje pobierania plików przez wbudowane funkcje PHP ani przez CURL! ExtensionEngine nie potrafi pobrać listy z serwera!</p><br />');	
		} else if($official_eelist == 1) {
		echo('<p class="ee_error">(ExtensionEngine)(Błąd) ExtensionEngine nie może pobrać listy, a tryb ustawiono na &quot;remote&quot;! Skontaktuj się z administratorem!</p><br />');	
		}
		if(file_exists("ee_custom")) {
		$eecustom = file_get_contents("ee_custom");
		$feecustom = fopen('data:text/plain,'.$eecustom, 'rb');
		$custom_eelists = array();
		while(($line = fgets($feecustom)) != false) {
		$custom_eelists[] = extensionengine_get_remote_list(trim($line));	
		}
		}
	}
function extensionengine_get_remote_list($link) {
if(ini_get('allow_url_fopen') == 1) {
$eelist = file_get_contents($link);
if($eelist != false) {
	return($eelist);
} else {
return(1);	
}
} else {
	if(function_exists('curl_init')) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$eelist = curl_exec($ch);
	curl_close($ch);
	return($eelist);
	} else {
	return(2);	
	}
}
}
function extensionengine_parse_adminlinks($eelist) {
	$feelist = fopen('data:text/plain,'.$eelist, 'rb');
	$action = "detect";
	$name = "";
	while(($line = fgets($feelist)) != false) {
		if($line[0] == '[' and $action == "detect") {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$filename .= $line[$i];
			}
			if(file_exists($filename)) {
			$action = "parse";
			$filename = "";
			continue;
			} else {
			$action = "skip";
			$filename = "";
			continue;
			}
		} else if($action == "parse") {
			if($line[0] == '[') {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$parsed .= $line[$i];
			}
			if($parsed == "name") {
			$action = "name";	
			} else if($parsed == "adminlink") {
			$action = "adminlink";	
			} else if($parsed == "end") {
			$action = "detect";	
			}
			$parsed = "";
			}
		} else if($action == "skip") {
			if($line[0] == '[') {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$parsed .= $line[$i];
			}
			}
			if($parsed == "end") {
			$action = "detect";	
			}
			} else if($action == "name") {
				$name = $line;
				$action = "parse";
			} else if($action == "adminlink") {
			echo('<a class="ee_adminlink" href="'.trim($line).'">');
			if($name == "") {
				echo("(ExtensionEngine) Ustawienia nienazwanego rozszerzenia</a><br />");
			} else {
				echo("(ExtensionEngine) Ustawienia rozszerzenia: ".$name."</a><br />");	
			}
			$name = "";
			$action = "parse";
			}
		}
}
function extensionengine_parse_info($eelist, $type) {
	$feelist = fopen('data:text/plain,'.$eelist, 'rb');
	$action = "detect";
	$name = "";
	$author = "";
	$date = "";
	while(($line = fgets($feelist)) != false) {
		if($line[0] == '[' and $action == "detect") {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$filename .= $line[$i];
			}
			if(file_exists($filename)) {
			$action = "parse";
			$filename = "";
			continue;
			} else {
			$action = "skip";
			$filename = "";
			continue;
			}
			} else if($action == "parse") {
			if($line[0] == '[') {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$parsed .= $line[$i];
			}
			if($parsed == "name") {
			$action = "name";
			} else if($parsed == "author") {
			$action = "author";
			} else if($parsed == "date") {
			$action = "date";	
			} else if($parsed == "end") {
				if($type == 'o') {
				if($name == "") {
					echo("Extension: /unnamed/");
				} else {
					echo("Extension: ".$name);
				}
				} else if($type == 'u') {
					if($name == "") {
						echo("(unofficial) Extension: /unnamed/");
					} else {
						echo("(unofficial) Extension: ".$name);	
					}
				} else if($type == 'l') {
					if($name == "") {
						echo("(local) Extension: /unnamed/");	
					} else {
						echo("(local) Extension: ".$name);	
					}
				}
			if($author != "") {
				if($date == "") {
					echo(" | &copy; by ".$author);	
				} else {
					echo(" | &copy; ".$date." by ".$author);
				}
			}
			echo("<br />");
			$name = "";
			$author = "";
			$date = "";
			$action = "detect";
			}
			}
			$parsed = "";
			} else if($action == "skip") {
			if($line[0] == '[') {
			for($i = 1; $i < strlen($line)-2; $i++) {
				$parsed .= $line[$i];
			}
			}
			if($parsed == "end") {
			$action = "detect";
			$parsed = "";
			}
			} else if($action == "name") {
				$name = $line;
				$action = "parse";
			} else if($action == "date") {
				$date = $line;
				$action = "parse";
			} else if($action == "author") {
			$author = $line;
			$action = "parse";
			}
		}
}
?>
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
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	
	if($_POST['modlogin'] == 1) {
	if($_POST['modlogin_pass'] == $modpass) {
	$_SESSION[$prefix.'mod_login'] = 1;
	session_regenerate_id();
	}
	}
	if($_SESSION[$prefix.'mod_login'] == 1) {
	if(file_exists("install.php")) {
	?>
	<p class="suds_error">Poważne zagrożenie bezpieczeństwa - nie usunąłeś install.php!</p><br /><br />
	<?php
	}
	?>
	<h2 class="suds_modmenu">Menu systemu moderacji:</h2><br /><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=show" title="Wyświetl i moderuj pliki">Wyświetl i moderuj pliki</a><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=upload" title="Dodaj nowy plik">Dodaj nowy plik</a><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=categories_show" title="Wyświetl i moderuj kategorie">Wyświetl i moderuj kategorie</a><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=add_category" title="Dodaj kategorię">Dodaj kategorię</a><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=ee_mode" title="Ustaw tryb ExtensionEngine">Ustaw tryb ExtensionEngine</a><br />
	<?php
	if($eemode == "local") {
		if(isset($local_eelist)) {
		extensionengine_parse_adminlinks($local_eelist, $id);
		}
	} else if($eemode == "locandrem") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_adminlinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_adminlinks($custom_eelist, $id);	
		}
		}
		if(isset($local_eelist)) {
		extensionengine_parse_adminlinks($local_eelist, $id);
		}
	} else if($eemode == "locorrem") {
		if(!isset($local_eelist)) {
			if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_adminlinks($official_eelist, $id);	
		}
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_adminlinks($custom_eelist, $id);	
		}
		}
		} else {
		extensionengine_parse_adminlinks($local_eelist, $id);
		}
	} else if($eemode == "remote") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_adminlinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_adminlinks($custom_eelist, $id);	
		}
		}
	}
	?>
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=logout" title="Wyloguj">Wyloguj</a><br />
	<hr />
	<?php
	if($_GET['action'] == "show") {
			$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	$dball=mysql_query("SELECT * FROM ".$dbprefix."files_main");
	$rows=mysql_num_rows($dball);
	if($rows != NULL) {
		$catall=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$catrows=mysql_num_rows($catall);
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
			for($catid = 0; $catid <= $catrows; $catid++) {
				if($catid == 0) {
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
				}
			?>
			<hr />
			<?php
				} else {
				$query=mysql_query("SELECT `category` FROM ".$dbprefix."files_categories WHERE `id`=".$catid);
				$category=mysql_fetch_array($query);
				$query=mysql_query("SELECT `id`,`filename`,`desc`,`added` FROM ".$dbprefix."files_main WHERE `category`=".$catid);
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
		}
	} else {
	?>
<p class="suds_info">Brak rekordów w bazie danych</p>
<?php
	}
	mysql_close($baza);
	} else if($_GET['action'] == "upload") {
	if($_POST['newset'] == 1) {
		if(isset($_FILES['upfile'])) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$dball=mysql_query("SELECT * FROM ".$dbprefix."files_main");
		$numrows=mysql_num_rows($dball);
		$ai=$numrows+1;
		$query=mysql_query("ALTER TABLE ".$dbprefix."files_main AUTO_INCREMENT = ".$ai);
		if($query != 1) {
		?>
		<p class="suds_error">Nie udało się ustawić poprawnej wartości AUTO_INCREMENT!</p>
		<?php
		} else {
			if(file_exists("suds_files/".$_FILES['upfile']['name'])) {
			?>
			<p class="suds_error">Błąd: Plik o takiej nazwie już istnieje! Zmień nazwę pliku lub usuń istniejący.</p>
			<?php
			} else {
			if(move_uploaded_file($_FILES['upfile']['tmp_name'],"./suds_files/".$_FILES['upfile']['name'])) {

		$query=mysql_query("INSERT INTO ".$dbprefix."files_main VALUES (NULL,".'"'.$_FILES['upfile']['name'].'"'.",".'"'.$_POST['updesc'].'",'.$_POST['category'].",NULL)");
		if($query == 1) {
		?>
		<p class="suds_info">Plik został przesłany!</p><br />
		<?php
		} else {
		?>
		<p class="suds_error">Nie udało się dodać pliku do bazy danych! Usuwam plik...</p><br />
		<?php
		chdir("suds_files");
		unlink($_FILES['upfile']['name']);
		chdir("..");
		}
			} else {
			?>
			<p class="suds_error">Wystąpił błąd przy przesyłaniu pliku: <?php
			switch($_FILES['upfile']['error']) {
		case 1: echo("Plik jest większy, niż pozwala na to serwer");break;
		case 2: echo("Plik jest większy, niż limit SUDS");break;
		case 3: echo("Plik został przesłany tylko częściowo");break;
		case 4: echo("Plik nie został przesłany");break;		
			}
			?>
			</p><br />
			<?php
			}
		}
		}
		} else {
		?>
		<p class="suds_error">Brak pliku w przesłanych danych!</p>
		<?php
		}
		mysql_close($baza);
	} else {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$catall=mysql_query("SELECT * FROM ".$prefix."files_categories");
		$catrows=mysql_num_rows($catall);
	?>
	<h3 class="suds_title">Dodawanie nowego pliku:</h3><br /><br />
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=upload" method="post" enctype="multipart/form-data">
	Wybierz plik: <input type="file" name="upfile" /><br />
	Opis pliku: <input type="text" name="updesc" /><br />
	Wybierz kategorię: <br />
	<select size=<?php echo($catrows+1); ?> name="category">
	<option selected value=0>Bez kategorii</option>
	<?php
	while($row = mysql_fetch_array($catall)) {
	?>
	<option value=<?php echo($row['id']); ?>><?php echo($row['category']); ?></option>
	<?php	
	}
	?>
	</select>
	<input type="hidden" name="newset" value="1" />
	<input type="submit" value="Wyślij" />
	</form>
	<br />
	<?php
	}
	} else if($_GET['action'] == "categories_show") {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$catall=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$catrows=mysql_num_rows($catall);
		if($catrows == NULL) {
			?>
			<p class="suds_info">Brak kategorii</p><br />
			<?php
		} else {
			while($row = mysql_fetch_array($catall)) {
		?>
		<h3 class="suds_category">Kategoria: <?php echo $row['category']; ?></h3><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=edit_category" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Edytuj" />
		</form>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=delete_category" method="post">
		<input type="hidden" name="id" value=<?php echo $row['id']; ?> />
		<input type="submit" value="Usuń" />
		</form>
		<?php
			}
		}
	} else if($_GET['action'] == "add_category") {
		?>
		<h3 class="suds_title">Dodawanie kategorii</h3>
		<?php
		if($_POST['catset'] == 1) {
			if($_POST['category'] != NULL) {
			$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$dball=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$numrows=mysql_num_rows($dball);
		$ai=$numrows+1;
		$query=mysql_query("ALTER TABLE ".$dbprefix."files_categories AUTO_INCREMENT = ".$ai);
		if($query != 1) {
		?>
		<p class="suds_error">Nie udało się ustawić poprawnej wartości AUTO_INCREMENT!</p>
		<?php
		} else {
		$query=mysql_query("INSERT INTO ".$dbprefix."files_categories VALUES (NULL,".'"'.$_POST['category'].'")');
		if($query == 1) {
		?>
		<p class="suds_info">Kategoria została dodana!</p><br />
		<?php
		} else {
		?>
		<p class="suds_error">Nie udało się dodać kategorii!</p><br />
		<?php
		}
		}
			} else {
				?>
				<p class="suds_error">Pusta nazwa kategorii!</p><br />
				<?php
			}
		} else {
		?>
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=add_category" method="post">
		Nazwa kategorii: <input type="text" name="category" /><br />
		<input type="hidden" name="catset" value=1 />
		<input type="submit" value="Dodaj" />
		</form>
		<?php
		}
	} else if($_GET['action'] == "edit_category") {
				if($_POST['catedset'] == 1) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$query=mysql_query("UPDATE ".$dbprefix."files_categories SET `category`=".'"'.$_POST['category'].'"'." WHERE `id`=".$_POST['id']);
		if($query == 1) {
		?>
		<p class="suds_info">Kategoria zaktualizowana pomyślnie!</p><br />
		<?php
		} else {
		?>
		<p class="suds_error">Nie udało się zaktualizować kategorii!</p><br />
		<?php
		}
		} else {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$id = $_POST['id'];
		if($id != NULL) {
		$query=mysql_query("SELECT `category` FROM ".$dbprefix."files_categories WHERE `id`=".$id);
		$category=mysql_fetch_array($query);
		?>
		<h3 class="suds_title">Modyfikacja kategorii:</h3><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=edit_category" method="post">
		<input type="text" name="category" value="<?php echo $category['category']; ?>" /><br />
		<input type="hidden" name="catedset" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="submit" value="Zapisz" />	
		</form>
		<?php
		mysql_close($baza);
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID kategorii! Kategoria nie może zostać zmodyfikowana!</p><br />
		<?php
		mysql_close($baza);
		}
		}
	} else if($_GET['action'] == "delete_category") {
		?>
		<h3 class="suds_title">Usuwanie kategorii</h3><br />
		<?php
		$id=$_POST['id'];
		if($id != NULL) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$dball=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$rows=mysql_num_rows($dball);
		$all=mysql_fetch_array($dball);
		$query=mysql_query("DELETE FROM ".$dbprefix."files_categories WHERE `id`=".$id);
		if($query == 1) {
		?>
		<p class=suds_info>Kategoria została pomyślnie usunięta!</p><br />
		<?php
		$query=mysql_query("UPDATE ".$dbprefix."files_main SET `category`=0 WHERE `category`=".$id);
			$nid=$id+1;
			if($nid<=$rows) {
			for($i=$nid;$i<=$rows;$i++) {
			$sid=$i-1;
			$query=mysql_query("UPDATE ".$dbprefix."categories_main SET `id`=".$sid." WHERE `id`=".$i);
			}
			mysql_close($baza);
			}
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID kategorii! Kategoria nie mogła zostać usunięta!</p><br />
		<?php
		}
		}
	} else if($_GET['action'] == "file_edit") {
		if($_POST['edset'] == 1) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$query=mysql_query("UPDATE ".$dbprefix."files_main SET `desc`=".'"'.$_POST['desc'].'", `category`='.$_POST['category']." WHERE `id`=".$_POST['id']);
		$query=mysql_query("UPDATE ".$dbprefix."files_main SET `desc`=".'"'.$_POST['desc'].'"'." WHERE id=".$_POST['id']);
		if($query == 1) {
		?>
		<p class="suds_info">Dane pliku zaktualizowane pomyślnie!</p><br />
		<?php
		} else {
		?>
		<p class="suds_error">Nie udało się zaktualizować danych pliku!</p><br />
		<?php
		}
		} else {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$id = $_POST['id'];
		if($id != NULL) {
		$query=mysql_query("SELECT `desc` FROM ".$dbprefix."files_main WHERE id=".$id);
		$desc=mysql_fetch_array($query);
		$catall=mysql_query("SELECT * FROM ".$dbprefix."files_categories");
		$catrows=mysql_num_rows($catall);
		$catq=mysql_query("SELECT `category` FROM ".$dbprefix."files_main WHERE `id`=".$id);
		$cat=mysql_fetch_array($catq);
		?>
		<h3 class="suds_title">Modyfikacja danych pliku:</h3><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
		Opis: <input type="text" name="desc" value="<?php echo $desc['desc']; ?>" /><br />
		Wybierz kategorię: <br />
	<select size=<?php echo($catrows+1); ?> name="category">
	<option <?php if($cat['category'] == 0) echo("selected"); ?> value=0>Bez kategorii</option>
	<?php
	while($row = mysql_fetch_array($catall)) {
	?>
	<option <?php if($cat['category'] == $row['id']) echo("selected"); ?> value=<?php echo($row['id']); ?>><?php echo($row['category']); ?></option>
	<?php	
	}
	?>
	</select>
		<input type="hidden" name="edset" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="submit" value="Zapisz" />	
		</form>
		<?php
		mysql_close($baza);
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID pliku! Dane nie mogą zostać zmodyfikowane!</p><br />
		<?php
		mysql_close($baza);
		}
		}
	} else if($_GET['action'] == "file_delete") {
		?>
		<h3 class="suds_title">Usuwanie pliku</h3><br />
		<?php
		$id=$_POST['id'];
		if($id != NULL) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$dball=mysql_query("SELECT * FROM ".$dbprefix."files_main");
		$rows=mysql_num_rows($dball);
		$all=mysql_fetch_array($dball);
		$delname=$all['filename'];
		$query=mysql_query("DELETE FROM ".$dbprefix."files_main WHERE id=".$id);
		if($query == 1) {
		chdir("suds_files");
		unlink($delname);
		chdir("..");
		?>
		<p class=suds_info>Plik został pomyślnie usunięty!</p><br />
		<?php
			$nid=$id+1;
			if($nid<=$rows) {
			for($i=$nid;$i<=$rows;$i++) {
			$query=mysql_query("SELECT added FROM ".$dbprefix."files WHERE id=".$i);
			$added=mysql_fetch_array($query);
			$sid=$i-1;
			$query=mysql_query("UPDATE ".$dbprefix."files_main SET id=".$sid." WHERE id=".$i);
			$query=mysql_query("UPDATE ".$dbprefix."files_main SET added=".$added['added']." WHERE id=".$sid);
			}
			mysql_close($baza);
			}
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID pliku! Plik nie mógł zostać usunięty!</p><br />
		<?php
		}
		}
	} else if($_GET['action'] == "ee_mode") {
		if($_POST['eeset'] == 1) {
			if(file_exists("ee_mode.php")) {
			unlink("ee_mode.php");
			}
			$eemodefile=fopen("ee_mode.php","w");
			flock($eemodefile, LOCK_EX);
			fputs($eemodefile, '<?php'."\n");
			fputs($eemodefile, '$eemode="'.$_POST['eemode'].'";'."\n");
			fputs($eemodefile, '?>');
			flock($eemodefile, LOCK_UN);
			fclose($eemodefile);
			if(file_exists("ee_mode.php")) {
				echo('<p class="suds_info">Ustawienia zostały zapisane pomyślnie!</p><br />');	
			} else {
				echo('<p class="suds_error">Nie udało się zapisać pliku z ustawieniami! Sprawdź uprawnienia katalogu i spróbuj ponownie!</p><br />');	
			}	
		} else {
		?>
		<h3 class="smpbns_title">Ustawienia trybu ExtensionEngine:</h3><br />
		<form action="<?php echo $_SERVER["PHP_SELF"];?>?action=ee_mode" method="post">
		<select name="eemode">
		<option value="local" <?php if($eemode=="local") echo("selected"); ?>>Tryb lokalny</option>
		<option value="locandrem" <?php if($eemode=="locandrem" or !isset($eemode)) echo("selected"); ?>>Tryb lokalny i sieciowy</option>
		<option value="locorrem" <?php if($eemode=="locorrem") echo("selected"); ?>>Tryb lokalny lub sieciowy</option>
		<option value="remote" <?php if($eemode=="remote") echo("selected"); ?>>Tryb sieciowy</option>
		</select>
		<input type="hidden" name="eeset" value="1" />
		<input type="submit" value="Zatwierdź" />
		</form>
		<?php
		}
	} else if($_GET['action'] == "logout") {
		$_SESSION[$prefix.'mod_login'] = 0;
		?>
		<p class="suds_info">Wylogowano Cię z systemu moderacji SUDS! Możesz teraz przejść na stronę główną systemu, lub zalogować się jeszcze raz, ponownie wchodząc na tą stronę.</p>
		<?php
	} else {
	?>
	<p class="suds_text">Witaj w systemie moderacji SUDS! Wybierz działanie z menu, znajdującego się na górze strony. Kiedy skończysz pracę, wyloguj się.</p>
	<?php
	}
	} else {
	?>
	<p class="suds_modlogin_text">Podaj hasło moderatora:</p><br />
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
		<input type="password" name="modlogin_pass" /><br />
		<input type="hidden" name="modlogin" value="1" />
		<input type="submit" value="Zaloguj" />
	</form>
<?php
	}
} else {
?>
<p class="suds_error">Plik ustawień nie istnieje! Czy na pewno uruchomiłeś install.php?</p>
<?php
}
} else {
echo("Ze względów bezpieczeństwa wymagane jest podanie prefiksu dla tej instalacji SUDS. NIGDY nie instaluj dwóch systemów z tym samym prefiksem! Jeżeli jest to twoja pierwsza i jedyna instalacja SUDS, zaleca się pozostawienie domyślnego prefiksu.<br />");
?>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<input type="text" name="prefix" value="suds_" /><br />
<input type="hidden" name="setprefix" value="1" />
<input type="submit" value="Ustaw prefiks i kontynuuj" />
</form>
<?php
}
?>
<br />
<a class="suds_main_link" href="suds.php" title="Indeks systemu SUDS">Indeks systemu SUDS</a><hr />
<p class="suds_footer">Powered by SUDS</a> | &copy; 2010-2011 by Phitherek_<br />
MOD: Categories | &copy; 2010-2011 by Phitherek_<br />
MOD: ExtensionEngine | &copy; 2011 by Phitherek_<br />
<?php
	if($eemode == "local") {
		if(isset($local_eelist)) {
		extensionengine_parse_info($local_eelist, 'l');
		}
	} else if($eemode == "locandrem") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_info($official_eelist, 'o');	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_info($custom_eelist, 'u');	
		}
		}
		if(isset($local_eelist)) {
		extensionengine_parse_info($local_eelist, 'l');
		}
	} else if($eemode == "locorrem") {
		if(!isset($local_eelist)) {
			if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_info($official_eelist, 'o');	
		}
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_info($custom_eelist, 'u');	
		}
		}
		} else {
		extensionengine_parse_info($local_eelist, 'l');
		}
	} else if($eemode == "remote") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_info($official_eelist, 'o');	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_info($custom_eelist, 'u');	
		}
		}
	}
	?>
</p>
</body>
</html>
