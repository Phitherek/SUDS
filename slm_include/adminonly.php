<?php
function slm_adminonly($link="index.php",$footerlinkpage="index.php", $footerlinktext="Indeks") {
	session_start();
	if (!isset($_SESSION['started'])) {
	session_regenerate_id();
	$_SESSION['started'] = true;
}
	if($_SESSION['slm_type'] != "admin") {
		?>
		<p class="slm_error">Ta strona dostępna jest tylko dla użytkownika SLM ze statusem administratora! Jeżeli powinieneś mieć do niej dostęp, skontaktuj się z administratorem.<br /><br /><a class="slm_link" href="<?php echo $link; ?>" alt="link">Przejdź do strony głównej</a></p><br /><br />
		<?php
		?>
<hr />
<p class="slm_footer">Powered by SLM | &copy; 2010 by Phitherek_<br /><br />
<a class="slm_link" href="<?php echo $footerlinkpage; ?>" alt="link"><?php echo $footerlinktext; ?></a></p>
<?php
		die();
	}
}
?>
