<?php
	require_once('AppInfo.php');
	require_once('sdk/src/facebook.php');
	echo '<br>yourtextcontent: '.$_POST['yourtextcontent'];
	echo '<br>Test ppic:'.$_POST['ppic'];
	echo '<br>Test bpic:'.$_POST['bpic'];
	echo '<br>Test swall:'.$_POST['swall'];
	echo '<br>Test mpage:'.$_POST['mpage'];
	echo '<br>Test fpfile:'.$_POST['fpfile'];
	echo '<br>Test postid:'.$_POST['postid'];
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
		}
	else
		{
		
		}
?>