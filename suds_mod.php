<html>
<head>
<title>Phitherek_' s SUDS - MOD: SLMlock - System moderacji - tytuł może być później zmieniony</title>
<META http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- Tutaj ewentualnie dołączyć plik stylu CSS -->
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['started'])) {
session_regenerate_id();
$_SESSION['started'] = true;
}
include("slm_include/adminonly.php");
include("slm_include/userinfo.php");
include("slm_include/loginform.php");
slm_userinfo(1,0,"login.php","logout.php");
slm_loginpage_sub(1,0,"register.php");
slm_adminonly("suds.php","suds.php","Indeks systemu SUDS");
if(file_exists("suds_settings.php")) {
	include("suds_settings.php");
	if(file_exists("install.php")) {
	?>
	<p class="suds_error">Poważne zagrożenie bezpieczeństwa - nie usunąłeś install.php!</p><br /><br />
	<?php
	}
	?>
	<h2 class="suds_modmenu">Menu systemu moderacji:</h2><br /><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=show" title="Wyświetl i moderuj pliki">Wyświetl i moderuj pliki</a><br />
	<a class="suds_modmenu" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=upload" title="Dodaj nowy plik">Dodaj nowy plik</a><br />
	<a class="suds_modmenu" href="register.php" title="Zarejestruj użytkownika SLM">Zarejestruj użytkownika SLM</a><br />
	<a class="suds_modmenu" href="logout.php" title="Wyloguj">Wyloguj</a><br />
	<hr />
	<?php
	if($_GET['action'] == "show") {
		$baza=mysql_connect($serek, $dbuser, $dbpass) or die("Nie można się połączyć z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
	mysql_select_db($dbname);
	$dball=mysql_query("SELECT * FROM ".$prefix."files_main");
	$rows=mysql_num_rows($dball);
	if($rows != NULL) {
		for($id = 1; $id <= $rows; $id++) {
			$query=mysql_query("SELECT filename FROM ".$prefix."files_main WHERE id=".$id);
			$filename=mysql_fetch_array($query);
		$query=mysql_query("SELECT `desc` FROM ".$prefix."files_main WHERE id=".$id);
		$desc=mysql_fetch_array($query);
		if($filename != NULL) {
		if($desc != NULL) {
		?>
		<a class = "suds_link_ok" href="suds_files/<?php echo $filename['filename']; ?>"><?php echo $desc['desc']; ?></a><br />
		<?php
		} else {
		?>
		<a class = "suds_link_nodesc" href="suds_files/<?php echo $filename['filename']; ?>"><?php echo $filename['filename']; ?></a><br />
		<?php
		}
		} else if($desc != NULL) {
		?>
		<p class="suds_desconly"><?php echo $desc['desc']; ?> (Brak odnośnika!)</p><br />
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
				<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
				<input type="hidden" name="id" value=<?php echo $id; ?> />
				<input type="submit" value="Edytuj opis" />
				</form>
				<br />
				<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_delete" method="post">
				<input type="hidden" name="id" value=<?php echo $id; ?> />
				<input type="submit" value="Usuń" />
				</form>
				<?php
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
		$dball=mysql_query("SELECT * FROM ".$prefix."files_main");
		$numrows=mysql_num_rows($dball);
		$ai=$numrows+1;
		$query=mysql_query("ALTER TABLE ".$prefix."files_main AUTO_INCREMENT = ".$ai);
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
		$query=mysql_query("INSERT INTO ".$prefix."files_main VALUES (NULL,".'"'.$_FILES['upfile']['name'].'"'.",".'"'.$_POST['updesc'].'"'.",NULL)");
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
	?>
	<h3 class="suds_title">Dodawanie nowego pliku:</h3><br /><br />
	<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=upload" method="post" enctype="multipart/form-data">
	Wybierz plik: <input type="file" name="upfile" /><br />
	Opis pliku: <input type="text" name="updesc" /><br />
	<input type="hidden" name="newset" value="1" />
	<input type="submit" value="Wyślij" />
	</form>
	<br />
	<?php
	}
	} else if($_GET['action'] == "file_edit") {
		if($_POST['edset'] == 1) {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$query=mysql_query("UPDATE ".$prefix."files_main SET `desc`=".'"'.$_POST['desc'].'"'." WHERE id=".$_POST['id']);
		if($query == 1) {
		?>
		<p class="suds_info">Opis pliku zaktualizowany pomyślnie!</p><br />
		<?php
		} else {
		?>
		<p class="suds_error">Nie udało się zaktualizować opisu pliku!</p><br />
		<?php
		}
		} else {
		$baza=mysql_connect($serek,$dbuser,$dbpass) or die("Nie można połączyć się z serwerem MySQL! Czy na pewno instalacja dobiegła końca?");
		mysql_select_db($dbname);
		$id = $_POST['id'];
		if($id != NULL) {
		$query=mysql_query("SELECT `desc` FROM ".$prefix."files_main WHERE id=".$id);
		$desc=mysql_fetch_array($query);
		?>
		<h3 class="suds_title">Modyfikacja opisu pliku:</h3><br />
		<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=file_edit" method="post">
		<input type="text" name="desc" value="<?php echo $desc['desc']; ?>" /><br />
		<input type="hidden" name="edset" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="submit" value="Zapisz" />	
		</form>
		<?php
		mysql_close($baza);
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID pliku! Opis nie może zostać zmodyfikowany!</p><br />
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
		$dball=mysql_query("SELECT * FROM ".$prefix."files_main");
		$rows=mysql_num_rows($dball);
		$all=mysql_fetch_array($dball);
		$delname=$all['filename'];
		$query=mysql_query("DELETE FROM ".$prefix."files_main WHERE id=".$id);
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
			$query=mysql_query("SELECT added FROM ".$prefix."files WHERE id=".$i);
			$added=mysql_fetch_array($query);
			$sid=$i-1;
			$query=mysql_query("UPDATE ".$prefix."files_main SET id=".$sid." WHERE id=".$i);
			$query=mysql_query("UPDATE ".$prefix."files_main SET added=".$added['added']." WHERE id=".$sid);
			}
			mysql_close($baza);
			}
		} else {
		?>
		<p class="suds_error">Nie udało się wczytać ID pliku! Plik nie mógł zostać usunięty!</p><br />
		<?php
		}
		}	
	} else {
	?>
	<p class="suds_text">Witaj w systemie moderacji SUDS! Wybierz działanie z menu, znajdującego się na górze strony.</p>
	<?php
	}
} else {
?>
<p class="suds_error">Plik ustawień nie istnieje! Czy na pewno uruchomiłeś install.php?</p>
<?php
}
?>
<br />
<a class="suds_main_link" href="suds.php" title="Indeks systemu SUDS">Indeks systemu SUDS</a><br />
<a class="suds_slmadmin" href="slm_admin.php">Administracja SLM</a><hr />
<p class="suds_footer">Powered by SUDS</a> | &copy; 2010 by Phitherek_<br />
MOD: SLMlock | &copy; 2010 by Phitherek_ | uses SLM &copy; 2010 by Phitherek_ </p>
</body>
</html>
