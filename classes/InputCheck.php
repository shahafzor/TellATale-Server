<?php
class InputCheck
{
	public static function validateCredentials($username, $password)
	{
		if (!self::validateUsername($username))
		{
			return false;
		}
		
		return self::validatePassword($password);
	}
	
	public static function validateUsername($username)
	{
		if (strlen($username) < 3 or strlen($username) > 12)
		{
			echo "username must be 3-20 charachters <br> <br>";
			return false;
		}
		
		if (!preg_match("/^[a-zA-Z0-9]*$/",$username))
		{
			echo "username: only letters and numbers are allowed <br> <br>";
			return false;
		}
		return true;
	}
	
	public static function validatePassword($password)
	{
		if (strlen($password) < 6 or strlen($password) > 12)
		{
			echo "password must be 6-10 charachters <br> <br>";
			return false;
		}
		
		if (!preg_match("/^[a-zA-Z0-9]*$/",$password))
		{
			echo "password: only letters and numbers are allowed <br> <br>";
			return false;
		}
		return true;
	}
}
?>
