
function checkUsername()
{
	var username = document.getElementById('username').value;
	if (username.length < 3 || username.length > 20)
	{
		alert("username must be 3-20 charachters");
		return false;
	}
	return true;
}