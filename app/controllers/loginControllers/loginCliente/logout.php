<?php
session_start();
session_destroy();
header("Location: ../../../views/loginViews/loginCliente.php");
exit();
?>
