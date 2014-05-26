<?php
require_once 'app/init.php';

$db = new DB;
$googleClient = new Google_Client;
$auth = new GoogleAuth($db,$googleClient);

if($auth->checkRedirectCode())
{
	//die($_GET['code']);
	header('Location: index.php');
}
if(isset($_SESSION['access_token']))
{
	echo $_SESSION['access_token'];
}

?>
<!DOCTYPE html>
<html>
<head>

	<title>Google Auth</title>
</head>
<body>

<?php if(!$auth->isLoggedIn()): ?>
	<a href="<?php echo $auth->getAuthUrl(); ?>">Sign in with Google</a>
<?php else: ?>
	You are signed in <a href="logout.php">Sign out</a>
<?php endif; ?>

</body>
</html>