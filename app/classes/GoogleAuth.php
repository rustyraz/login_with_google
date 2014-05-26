<?php
class GoogleAuth{

	//Declare variables
	protected $client;
	protected $db;

	//
	public function __construct(DB $db = null, Google_Client $googelClient = null)
	{
		$this->db = $db;
		$this->client = $googelClient;

		if($this->client)
		{
			$this->client->setClientId('');//Enter the Client ID from google app
			$this->client->setClientSecret('');//Enter Client Secret code
			$this->client->setRedirectUri('http://example.com/project/index.php');//Enter the URl the user will be redirected after authentications on google
			$this->client->setScopes('email');
		}
	}

	//return the current logged in session
	public function isLoggedIn()
	{
		return isset($_SESSION['access_token']);
	}

	//get the generated url for the user to login
	public function getAuthUrl()
	{
		return $this->client->createAuthUrl();
	}

	//after the user has been redirected we check for the code that is returned
	public function checkRedirectCode()
	{
		if(isset($_GET['code'])){
			$this->client->authenticate($_GET['code']);
			$this->setToken($this->client->getAccessToken());

			//store the logged in session into the database
			$this->storeUser($this->getPayLoad());

			return true;
		}
		return false;
	}

	//set an access token for the user
	public function setToken($token)
	{
		$_SESSION['access_token'] = $token;
		$this->client->setAccessToken($token);
	}

	//logout the user
	public function logout()
	{
		unset($_SESSION['access_token']);
	}

	//the the array sent back after the authentication
	protected function getPayLoad()
	{
		$payLoad = $this->client->verifyIdToken()->getAttributes()['payload'];
		return $payLoad;
	}

	//store the user info into the db using a query statement
	protected function storeUser($payload)
	{
		$sql = "
		INSERT INTO google_users (`google_id`, `email`) VALUES ({$payload['id']}, '{$payload['email']}')
		ON DUPLICATE KEY UPDATE id = id
		";

		$this->db->query($sql);
	}
}
