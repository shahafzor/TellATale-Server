<?php
include_once 'common.php';
include_once 'classes/InputCheck.php';

// Script starts here
$username = $_GET['username'];
$password = $_GET['password'];

// check input
if (!InputCheck::validateCredentials($username, $password))
{
	exitError(STATUS_ILLEGAL_INPUT);
}

// create a user object
$user = new User($username, $password, PERMISION_BASIC);

// add the user to the database
$status = addUser($user);

exitError($status);
?>
