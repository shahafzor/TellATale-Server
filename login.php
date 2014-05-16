<?php
include_once 'common.php';
include_once 'classes/InputCheck.php';


$username = $_GET ['username'];
$password = $_GET ['password'];

// validate user input
if (!InputCheck::validateCredentials($username, $password))
{
	exitError(STATUS_ILLEGAL_INPUT);
}

// try to login with username and password
$user = UserTable::logIn($username, $password);

if ($user) // successfull login
{
	exitError(STATUS_LOGIN_OK, $user->getPermission());
}
elseif ($user === null) // unseccssful login
{
	exitError(STATUS_ERROR_CREDENTIALS);
}
else
{
	exitError(STATUS_ERROR);
}
?>
