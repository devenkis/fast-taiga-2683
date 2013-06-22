<?php
// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

$user_id = $facebook->getUser();
if ($user_id) {
  try {
    // Fetch the viewer's basic information
    $basic = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    // If the call fails we check if we still have a user. The user will be
    // cleared if the error is because of an invalid accesstoken
    if (!$facebook->getUser()) {
      header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
      exit();
    }
  }

  // This fetches some things that you like . 'limit=*" only returns * values.
  // To see the format of the data you are retrieving, use the "Graph API
  // Explorer" which is at https://developers.facebook.com/tools/explorer/
  $likes = idx($facebook->api('/me/likes?limit=4'), 'data', array());

  // This fetches 4 of your friends.
  $friends = idx($facebook->api('/me/friends?limit=4'), 'data', array());

  // And this returns 16 of your photos.
  $photos = idx($facebook->api('/me/photos?limit=16'), 'data', array());

  // Here is an example of a FQL call that fetches all of your friends that are
  // using this app
  $app_using_friends = $facebook->api(array(
    'method' => 'fql.query',
    'query' => 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
  ));
}

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title>Yet to be names: tentatively Phobr</title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->
    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="My first app" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

    <script type="text/javascript">
	function logResponse(response) {if (console && console.log)	console.log('The response was', response);}

      $(function(){
        // Set up so we handle click on the buttons
        $('#postToWall').click(function() {
          FB.ui(
            {method : 'feed', link : $(this).attr('data-url')},
            function (response) 
				{
				// If response is null the user canceled the dialog
				if (response != null) logResponse(response);
				});});

        $('#sendToFriends').click(function() {
          FB.ui(
            { method : 'send', link : $(this).attr('data-url')},
            function (response) 
				{
				// If response is null the user canceled the dialog
				if (response != null) logResponse(response);
				});});

        $('#sendRequest').click(function() {
          FB.ui(
            {method  : 'apprequests', message : $(this).attr('data-message')},
            function (response)
				{
				// If response is null the user canceled the dialog
				if (response != null) logResponse(response);
				});});
				
        $('#uploadfbpic').click(function() { uploadphoto2fb();});
      });
	function resetstuff()
		{
		g_fpfile='';
		$('#yourtextcontent').val('Your thoughts here');
		$('#preview').html('');
		}  

	var g_fpfile='';
	function pickthefilebuddy()
		{
		/*
		filepicker.setKey('A8yWvMi9gSBWGuUXMoKFfz');
		filepicker.pick(
			{mimetype: 'image/*'}, 
			function(fpfile) 
				{//alert(fpfile.url);
				filepicker.stat(
					fpfile, {width: true, height: true}, 
					function(metadata)
						{//alert('width:'+ metadata.width +' height:' +metadata.height);
						if(metadata.width>700) // need to do the conversion part of the image
							{//if the image is too big to let our brand ad be appropriate on it
							filepicker.convert(
								fpfile, {width: 700},
								function(new_FPFile)
									{//alert("Converting...image to upload");console.log(new_FPFile.url);
									$('#preview').html('<img id="pictoload" src="'+new_FPFile.url+'" />');
									g_fpfile=new_FPFile.url;
									});
							}
						else if(metadata.height>700)
							{
							filepicker.convert(
								fpfile, {height: 700},
								function(new_FPFile)
									{//alert("Converting...image to upload");console.log(new_FPFile.url);
									$('#preview').html('<img id="pictoload" src="'+new_FPFile.url+'" />');
									g_fpfile=new_FPFile.url;
									});
							}
						else
							{
							$('#preview').html('<img id="pictoload" src="'+fpfile.url+'" />');
							g_fpfile=fpfile.url;
							}
						});
				});
		*/
		g_fpfile='https://www.filepicker.io/api/file/xKxc09VlSlKRWa1J9MC9';
		$('#preview').html('<img id="pictoload" src="'+g_fpfile+'" />');
		}
	function uploadphoto2fb()
		{/*
		if(g_fpfile=='')	
			{alert("Please upload or select a pic to continue");	return;}
		if($('#yourtextcontent').val()=='Your thoughts here' || $('#yourtextcontent').val()=='')	
			{alert("Please put your thoughts to continue");	return;}
		var imgURL = g_fpfile;
		var messagedetail = $('#yourtextcontent').val();//+brandad()
	    FB.api('/me/photos', 'post', {message:messagedetail, url:imgURL} 
				, function(response)
					{
					if (!response || response.error) {alert('Error occured'+JSON.stringify(response.error));} 
					else //on success
						{
						alert('Post ID: ' + response.id);
						setprofilepic(response.id);
						}
			   });*/
		setprofilepic(10152025014424778);	   
		/* use photos instead of feed, url instead of pictures for uploading images to album*/
		}

		function setprofilepic(photoid)
			{
			$.ajax({
				type: "GET",
				url: "/services/setprofilepic.php?photoid="+photoid, 
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				success: function(req) 
					{
					responseDur = req;//eval('(' + req + ')');
					if(responseDur!=null)
						{
						if( responseDur.errorNumber==1 ) //SUCCESS
							{
							alert(" returned from service php"+ responseDur.returned);
							}
						else
							{
							alert("some service php generated error");
							}
						}
					},
				error: function(err) 
					{
					alert(err.toString());
					alert('Error:' + err.responseText + '  Status: ' + err.status);
					}
				});	
			}
    </script>

    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
  </head>
  <body>
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
     // Load file picker io Asynchronously
	(function(a){if(window.filepicker){return}var b=a.createElement("script");b.type="text/javascript";b.async=!0;b.src=("https:"===a.location.protocol?"https:":"http:")+"//api.filepicker.io/v1/filepicker.js";var c=a.getElementsByTagName("script")[0];c.parentNode.insertBefore(b,c);var d={};d._queue=[];var e="pick,pickMultiple,pickAndStore,read,write,writeUrl,export,convert,store,storeUrl,remove,stat,setKey,constructWidget,makeDropPane".split(",");var f=function(a,b){return function(){b.push([a,arguments])}};for(var g=0;g<e.length;g++){d[e[g]]=f(e[g],d._queue)}window.filepicker=d})(document); 
	</script>
    <header class="clearfix">
      <?php if (isset($basic)) { ?>
      <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>

      <div>
        <h1>Welcome, <strong><?php echo he(idx($basic, 'name')); ?></strong></h1>
        <p class="tagline">
          This is our app
          <a href="<?php echo he(idx($app_info, 'link'));?>" target="_top"><?php echo he($app_name); ?></a>
        </p>

		
        <div id="share-app">
          <p>Share your app buddy:</p>
          <ul>
        
			<li><a href="#" class="facebook-button" id="postToWall" data-url="<?php echo AppInfo::getUrl(); ?>"><span class="plus">Post to Wall</span></a></li>
            <li><a href="#" class="facebook-button speech-bubble" id="sendToFriends" data-url="<?php echo AppInfo::getUrl(); ?>"><span class="speech-bubble">Send Message</span></a></li>
            <li><a href="#" class="facebook-button apprequests" id="sendRequest" data-message="Test this awesome app"><span class="apprequests">Send Requests</span></a></li>
			<li><a href="#" class="facebook-button apprequests" id="uploadfbpic" data-message="Test this awesome app"><span class="apprequests">Upload picture</span></a></li>
			
          </ul>
			<form name="publishForm" action="success.php" onsubmit="return uploadphoto2fb()" method="post">
				<div id="preview">sdfdsf</div>        
				<input type="textarea" id="yourtextcontent" value="Your thoughts here"/>
				<input type="submit" onclick="pickthefilebuddy()" value="Pick your file"/>
				<input type="submit" onclick="resetstuff()" value="Reset"/>
				First name: <input type="text" name="fname"><br/>
				Age: <input type="text" name="age">
				<input type="submit" value="Publish">
			</form>
        </div>
      </div>
      <?php } else { ?>
      <div>
        <h1>Welcome</h1>
        <div class="fb-login-button" data-scope="user_likes,user_photos,user_interests, manage_pages,publish_stream"></div>
      </div>
      <?php } ?>
    </header>

    <section id="get-started">
      <p>Welcome to your Facebook app, running on <span>heroku</span>!</p>
      <a href="https://devcenter.heroku.com/articles/facebook" target="_top" class="button">Learn How to Edit This App</a>
    </section>

    <?php
      if ($user_id) {
    ?>

    <section id="samples" class="clearfix">
      <h1>Examples of the Facebook Graph API</h1>

      <div class="list">
        <h3>A few of your friends</h3>
        <ul class="friends">
          <?php
            foreach ($friends as $friend) {
              // Extract the pieces of info we need from the requests above
              $id = idx($friend, 'id');
              $name = idx($friend, 'name');
          ?>
          <li>
            <a href="https://www.facebook.com/<?php echo he($id); ?>" target="_top">
              <img src="https://graph.facebook.com/<?php echo he($id) ?>/picture?type=square" alt="<?php echo he($name); ?>">
              <?php echo he($name); ?>
            </a>
          </li>
          <?php
            }
          ?>
        </ul>
      </div>

      <div class="list inline">
        <h3>Recent photos</h3>
        <ul class="photos">
          <?php
            $i = 0;
            foreach ($photos as $photo) {
              // Extract the pieces of info we need from the requests above
              $id = idx($photo, 'id');
              $picture = idx($photo, 'picture');
              $link = idx($photo, 'link');

              $class = ($i++ % 4 === 0) ? 'first-column' : '';
          ?>
          <li style="background-image: url(<?php echo he($picture); ?>);" class="<?php echo $class; ?>">
            <a href="<?php echo he($link); ?>" target="_top"></a>
          </li>
          <?php
            }
          ?>
        </ul>
      </div>

      <div class="list">
        <h3>Things you like</h3>
        <ul class="things">
          <?php
            foreach ($likes as $like) {
              // Extract the pieces of info we need from the requests above
              $id = idx($like, 'id');
              $item = idx($like, 'name');

              // This display's the object that the user liked as a link to
              // that object's page.
          ?>
          <li>
            <a href="https://www.facebook.com/<?php echo he($id); ?>" target="_top">
              <img src="https://graph.facebook.com/<?php echo he($id) ?>/picture?type=square" alt="<?php echo he($item); ?>">
              <?php echo he($item); ?>
            </a>
          </li>
          <?php
            }
          ?>
        </ul>
      </div>

      <div class="list">
        <h3>Friends using this app</h3>
        <ul class="friends">
          <?php
            foreach ($app_using_friends as $auf) {
              // Extract the pieces of info we need from the requests above
              $id = idx($auf, 'uid');
              $name = idx($auf, 'name');
          ?>
          <li>
            <a href="https://www.facebook.com/<?php echo he($id); ?>" target="_top">
              <img src="https://graph.facebook.com/<?php echo he($id) ?>/picture?type=square" alt="<?php echo he($name); ?>">
              <?php echo he($name); ?>
            </a>
          </li>
          <?php
            }
          ?>
        </ul>
      </div>
    </section>

    <?php
      }
    ?>

    <section id="guides" class="clearfix">
      <h1>Learn More About Heroku &amp; Facebook Apps</h1>
      <ul>
        <li>
          <a href="https://www.heroku.com/?utm_source=facebook&utm_medium=app&utm_campaign=fb_integration" target="_top" class="icon heroku">Heroku</a>
          <p>Learn more about <a href="https://www.heroku.com/?utm_source=facebook&utm_medium=app&utm_campaign=fb_integration" target="_top">Heroku</a>, or read developer docs in the Heroku <a href="https://devcenter.heroku.com/" target="_top">Dev Center</a>.</p>
        </li>
        <li>
          <a href="https://developers.facebook.com/docs/guides/web/" target="_top" class="icon websites">Websites</a>
          <p>
            Drive growth and engagement on your site with
            Facebook Login and Social Plugins.
          </p>
        </li>
        <li>
          <a href="https://developers.facebook.com/docs/guides/mobile/" target="_top" class="icon mobile-apps">Mobile Apps</a>
          <p>
            Integrate with our core experience by building apps
            that operate within Facebook.
          </p>
        </li>
        <li>
          <a href="https://developers.facebook.com/docs/guides/canvas/" target="_top" class="icon apps-on-facebook">Apps on Facebook</a>
          <p>Let users find and connect to their friends in mobile apps and games.</p>
        </li>
      </ul>
    </section>
  </body>
</html>
