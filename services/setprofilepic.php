<?php
	header("Content-Type: application/html; charset=utf-8");
	require_once('../AppInfo.php');
	require_once('../sdk/src/facebook.php');
	// create response object
	$json = array();
	
	$facebook = new Facebook(array(
	  'appId'  => AppInfo::appID(),
	  'secret' => AppInfo::appSecret(),
	  'sharedSession' => true,
	  'trustForwarded' => true,
	));

	$user_id = $facebook->getUser();
	if ($user_id) 
		{
		try {
			// Fetch the viewer's basic information
			$basic = $facebook->api('/me');
			} 
		catch (FacebookApiException $e) {
			// If the call fails we check if we still have a user. The user will be
			// cleared if the error is because of an invalid accesstoken
			if (!$facebook->getUser()) {
				header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
				exit();
				}
			}
		try {
			$picture = $facebook->api('/'.$data['id']);
			$json['piclink']=$picture['link'];
			//header("Location: ".$pictue['link']."&makeprofile=1");
			}
		catch(Exception $e) 
			{
			$json['exception']=$e;
			}
		$json['errorNumber'] = 1;
		}
	else
		{
		$json['errorNumber'] = 100;
		}
  

	$json['returned'] = $_GET['photoid'];
	$encoded = json_encode($json);
	echo $encoded;
?>