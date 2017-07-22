<?php
	$password = '123456';
	$crypted = password_hash($password, PASSWORD_DEFAULT);
	var_dump($crypted);

	var_dump(password_verify($password, $crypted));
?>
