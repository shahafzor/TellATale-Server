<html>
<head>
	<title>Login</title>
</head>
<body>

<?php
include_once 'classes/UserTable.php';
include_once 'classes/InputCheck.php';

function login($username, $password)
{
	// try to login with username and password
	$user = UserTable::logIn($username, $password);

	// close database connection after use
	UserTable::closeDB();

	// successfull login
	if ($user)
	{
		$permission = $user->getPermission();

		// call android function to tell the application the login succeeded
		echo "<script> " .
				"Android.login('$username', $permission, '$password');" .
			"</script>";
		return 0;
	}
	elseif ($user === null)
	{
		echo "wrong login details <br> <br>";
	}
	else
	{
		echo "Please get some sleep <br> <br>";
	}
	
	return -1;
}

// Script starts here
if (isset($_POST['login']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// validate user input
	if (InputCheck::validateCredentials($username, $password))
	{
		$status = login($username, $password);
		if ($status != -1)
		{
			// exit for success and all errors excpet wrong login details
			exit();
		}
	}
}
?>
	<a href="add_user.php"> New User </a> <br> <br>
	
	<form action="index.php" method="post">
		username:
		<input type="text" name="username" value="<?php echo $username ?>"> <br>
		password:
		<input type="password" name="password"> <br>
		<input type="submit" name="login" value="login">
	</form>
</body>
</html>
