<?php
class User
{
	private $Username;
	private $Password;
	private $Permission = 0;
	private $Id;
	private $Languages = array();
	
	public function __construct($username, $password, $permission, $id)
	{
		$this->Username = $username;
		$this->Password = $password;
		$this->Permission = $permission;
		if (isset($id))
		{
			$this->Id = $id;
		}
		
	}
	
	public function getPassword()
	{
		return $this->Password;
	}
	
	public function getUsername()
	{
		return $this->Username;
	}
	
	public function getId()
	{
		return $this->Id;
	}
	
	public function getPermission()
	{
		return $this->Permission;
	}
	
	public function getLanguages()
	{
		return $this->Languages;
	}
	
	public function setPassword($password)
	{
		$this->Password = $password;
	}
	
	public function setUsername($username)
	{
		$this->Username = $username;
	}
	
	public function setPermission($permission)
	{
		$this->Permission = $permission;
	}
	
	public function addLanguage($newLanguage)
	{
		$this->Languages[] = $newLanguage;
	}
}
?>
