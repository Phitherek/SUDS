<html>
<head>
<title>Phitherek_' s SUDS - MOD: SLMlock - Rejestracja użytkownika SLM</title>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
include("slm_include/register.php");
include("slm_include/footer.php");
include("slm_include/adminonly.php");
slm_adminonly("suds.php","suds.php","Indeks systemu SUDS");
slm_register(1,"suds_mod.php");
slm_footer("suds_mod.php","Administracja SUDS");
?>
</body>
</html>
