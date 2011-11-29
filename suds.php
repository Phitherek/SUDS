<html>
<head>
<title>Phitherek_' s SUDS - MOD: ExtensionEngine - Główny plik systemu - ten tytuł można później zmienić</title>
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
function extensionengine_parse_menulinks($eelist) {
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
			} else if($parsed == "menulink") {
			$action = "menulink";	
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
			$parsed = "";
			}
			} else if($action == "name") {
				$name = $line;
				$action = "parse";
			} else if($action == "menulink") {
			echo('<a class="ee_menulink" href="'.trim($line).'">');
			if($name == "") {
				echo("(ExtensionEngine) Nienazwane rozszerzenie</a><br />");
			} else {
				echo("(ExtensionEngine) ".$name."</a><br />");	
			}
			$name = "";
			$action = "parse";
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
function extensionengine_parse_filelinks($eelist, $fileid) {
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
			} else if($parsed == "filelink") {
			$action = "filelink";	
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
			$parsed = "";
			}
			} else if($action == "name") {
				$name = $line;
				$action = "parse";
			} else if($action == "filelink") {
			echo('|<a class="ee_filelink" href="'.trim($line)."fileid=".$fileid.'">');
			if($name == "") {
				echo("(ExtensionEngine)(Plik) Nienazwane rozszerzenie</a>");
			} else {
				echo("(ExtensionEngine)(Plik) ".$name."</a>");	
			}
			$name = "";
			$action = "parse";
			}
		}
		echo("<br /><br />");
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
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	if($eemode == "local") {
		if(isset($local_eelist)) {
		extensionengine_parse_menulinks($local_eelist, $id);
		}
	} else if($eemode == "locandrem") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_menulinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_menulinks($custom_eelist, $id);	
		}
		}
		if(isset($local_eelist)) {
		extensionengine_parse_menulinks($local_eelist, $id);
		}
	} else if($eemode == "locorrem") {
		if(!isset($local_eelist)) {
			if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_menulinks($official_eelist, $id);	
		}
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_menulinks($custom_eelist, $id);	
		}
		}
		} else {
		extensionengine_parse_menulinks($local_eelist, $id);
		}
	} else if($eemode == "remote") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_menulinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_menulinks($custom_eelist, $id);	
		}
		}
	}
	$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	$dball=mysql_query("SELECT * FROM ".$dbprefix."files_main");
	$rows=mysql_num_rows($dball);
	if($rows != NULL) {
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
		<p class="suds_date">Ostatnia modyfikacja pliku: <?php echo $added['added']; ?></p><br /><br />
		<?php
		if($eemode == "local") {
		if(isset($local_eelist)) {
		extensionengine_parse_filelinks($local_eelist, $id);
		}
	} else if($eemode == "locandrem") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_filelinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_filelinks($custom_eelist, $id);	
		}
		}
		if(isset($local_eelist)) {
		extensionengine_parse_filelinks($local_eelist, $id);
		}
	} else if($eemode == "locorrem") {
		if(!isset($local_eelist)) {
			if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_filelinks($official_eelist, $id);	
		}
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_filelinks($custom_eelist, $id);	
		}
		}
		} else {
		extensionengine_parse_filelinks($local_eelist, $id);
		}
	} else if($eemode == "remote") {
		if(isset($official_eelist)) {
		if($official_eelist != 1 and $official_eelist != 2) {
		extensionengine_parse_filelinks($official_eelist, $id);	
		}	
		}
		if(isset($custom_eelists)) {
		foreach($custom_eelists as $custom_eelist) {
		extensionengine_parse_filelinks($custom_eelist, $id);	
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
} else {
?>
<p class="suds_error">Plik ustawień nie istnieje! Czy na pewno uruchomiłeś install.php?</p>
<?php
}
?>
<a class="suds_admin" href="suds_mod.php" title="Moderacja">Moderacja</a><br />
<hr />
<p class="suds_footer">Powered by SUDS | &copy; 2010-2011 by Phitherek_<br />
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
