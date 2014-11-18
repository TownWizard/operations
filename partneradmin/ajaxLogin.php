<?php
//include("db.php");
session_start();
if(isSet($_POST['username']) && isSet($_POST['password']))
{
	
// username and password sent from Form
/*$username=mysqli_real_escape_string($db,$_POST['username']); 
$password=md5(mysqli_real_escape_string($db,$_POST['password'])); 

$result=mysql_query($db,"SELECT uid FROM users WHERE username='$username' and password='$password'");
$count=mysql_num_rows($result);

$row=mysql_fetch_array($result,MYSQLI_ASSOC);*/
// If result matched $myusername and $mypassword, table row must be 1 row
	$username = "devang";
	$password = "devang123";
	if($_POST['username'] == $username && $_POST['password'] == $password)
	{
	$_SESSION['login_user']=$username;
	echo $username;
	}

}
?>