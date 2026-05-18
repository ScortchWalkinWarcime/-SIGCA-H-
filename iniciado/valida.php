<?php 
	session_start();
	if (!isset($_SESSION['n_usuario'])) {
		$_SESSION['n_usuario']=null;
		session_destroy();
		header("Location:../index.html");
	}
 ?>