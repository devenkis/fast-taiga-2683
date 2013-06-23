<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
		<title>Yet to be names: tentatively Phobr</title>
		<link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
		<link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />
		<script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

		<script>// Load file picker io Asynchronously
	(function(a){if(window.filepicker){return}var b=a.createElement("script");b.type="text/javascript";b.async=!0;b.src=("https:"===a.location.protocol?"https:":"http:")+"//api.filepicker.io/v1/filepicker.js";var c=a.getElementsByTagName("script")[0];c.parentNode.insertBefore(b,c);var d={};d._queue=[];var e="pick,pickMultiple,pickAndStore,read,write,writeUrl,export,convert,store,storeUrl,remove,stat,setKey,constructWidget,makeDropPane".split(",");var f=function(a,b){return function(){b.push([a,arguments])}};for(var g=0;g<e.length;g++){d[e[g]]=f(e[g],d._queue)}window.filepicker=d})(document); 
	</script>
		<script type="text/javascript">
		var g_fpfile='';
		function pickthefilebuddy()
			{
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

			/*g_fpfile='https://www.filepicker.io/api/file/xKxc09VlSlKRWa1J9MC9';
			$('#preview').html('<img id="pictoload" src="'+g_fpfile+'" />');
			$('#fpfile').val(g_fpfile);
			*/	
			return false;
			}
		function resetstuff()
			{
			g_fpfile='';
			$('#yourtextcontent').val('Your thoughts here');
			$('#preview').html('');
			return false;
			}
		function publish()
			{
			if(g_fpfile=='')	{alert("Please upload or select a pic to continue");	return false;}
			if($('#yourtextcontent').val()=='Your thoughts here' || $('#yourtextcontent').val()=='')	
				{alert("Please put your thoughts to continue");	return false;}
			return true;
			}
		function sethiddenfield()
			{
			if($('#cprofpic').is(':checked'))	$('#ppic').val("1");
			else								$('#ppic').val("0");
			if($('#cbannerpic').is(':checked'))	$('#bpic').val("1");
			else								$('#bpic').val("0");
			if($('#cshareonmywall').is(':checked'))	$('#swall').val("1");
			else								$('#swall').val("0");
			if($('#cmanagepage').is(':checked'))	$('#mpage').val("1");
			else								$('#mpage').val("0");
			}
		function trialrun()
			{
			filepicker.setKey('A8yWvMi9gSBWGuUXMoKFfz');
			filepicker.pickAndStore(
				{mimetype: 'image/*'}, 
				function(fpfile)
					{
					console.log(JSON.stringify(fpfile));
					
					});
			}
		</script>
		
	</head>
	<body>
		<input type="submit" value="fuck it" onclick="trialrun()"/>
		<form name="publishForm" action="successpublish.php" method="post">
			<div id="preview">sdfdsf</div>        
			<input type="textarea" id="yourtextcontent" name="yourtextcontent" value="Your thoughts here"/>
			<input type="submit" onclick="return pickthefilebuddy()" value="Pick your file"/>
			<input type="submit" onclick="return resetstuff()" value="Reset"/>
			<input type="submit" value="Publish" onclick="return publish();"/>
			<input type="checkbox" id="cprofpic" onclick="return sethiddenfield();"/> Set as Profile pic &nbsp;&nbsp;&nbsp; 
			<input type="checkbox" id="cbannerpic" onclick="return sethiddenfield();"/> Set as BannerImage &nbsp;&nbsp;&nbsp; 
			<input type="checkbox" id="cshareonmywall" onclick="return sethiddenfield();"/> Share on my wall &nbsp;&nbsp;&nbsp; 
			<input type="checkbox" id="cmanagepage" onclick="return sethiddenfield();"/> Share on my page &nbsp;&nbsp;&nbsp; 
			<input type='hidden' id="ppic" name="ppic" value="0"/>
			<input type='hidden' id="bpic" name="bpic" value="0"/>
			<input type='hidden' id="swall" name="swall" value="0"/>
			<input type='hidden' id="mpage" name="mpage" value="0"/>
			<input type='hidden' id="fpfile" name="fpfile"/>
		</form>
	</body>
</html>