<!DOCTYPE html>
<html>
 <head>
  <meta charset="charset=utf-8">
  <title>Module Test</title>
  <style>
label {
	width: 100px;
	margin-right: 10px;
	display:inline-block;
}
div {
	margin:2px;
	display:block;
}
.field {
	width: 300px;
}
input {
	box-sizing: border-box;
}
fieldset {
	margin-bottom: 20px;
}
.settings {
	display: inline;
	float: left;
}
  </style>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
 </head>
 <body>
<form id="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<fieldset><legend>Settings</legend>
	<div class="settings">
		<div><label>Module:</label><select name="module" class="field">
				<option value="APIKEY"<?php echo ((strcmp(trim($module), "APIKEY") == 0) ? " selected=\"selected\"" : ""); ?>>APIKEY</option>
				<option value="AVATAR"<?php echo ((strcmp(trim($module), "AVATAR") == 0) ? " selected=\"selected\"" : ""); ?>>AVATAR</option>
				<option value="CLOSE"<?php echo ((strcmp(trim($module), "CLOSE") == 0) ? " selected=\"selected\"" : ""); ?>>CLOSE</option>
				<option value="CREATE"<?php echo ((strcmp(trim($module), "CREATE") == 0) ? " selected=\"selected\"" : ""); ?>>CREATE</option>
				<option value="CURRENCY"<?php echo ((strcmp(trim($module), "CURRENCY") == 0) ? " selected=\"selected\"" : ""); ?>>CURRENCY</option>
				<option value="HASHTAG"<?php echo ((strcmp(trim($module), "HASHTAG") == 0) ? " selected=\"selected\"" : ""); ?>>HASHTAG</option>
				<option value="LAYER"<?php echo ((strcmp(trim($module), "LAYER") == 0) ? " selected=\"selected\"" : ""); ?>>LAYER</option>
				<option value="LOGIN"<?php echo ((strcmp(trim($module), "LOGIN") == 0) ? " selected=\"selected\"" : ""); ?>>LOGIN</option>
				<option value="LOGOUT"<?php echo ((strcmp(trim($module), "LOGOUT") == 0) ? " selected=\"selected\"" : ""); ?>>LOGOUT</option>
				<option value="PROFILE"<?php echo ((strcmp(trim($module), "PROFILE") == 0) ? " selected=\"selected\"" : ""); ?>>PROFILE</option>
				<option value="ROLE"<?php echo ((strcmp(trim($module), "ROLE") == 0) ? " selected=\"selected\"" : ""); ?>>ROLE</option>
				<option value="SIGNUP"<?php echo ((strcmp(trim($module), "SIGNUP") == 0) ? " selected=\"selected\"" : ""); ?>>SIGNUP</option>
				<option value="TITLE"<?php echo ((strcmp(trim($module), "TITLE") == 0) ? " selected=\"selected\"" : ""); ?>>TITLE</option>
				<option value="UPLOAD"<?php echo ((strcmp(trim($module), "UPLOAD") == 0) ? " selected=\"selected\"" : ""); ?>>UPLOAD</option>
		</select></div>
		<div><label>Identity:</label><input type="text" class="field" value="" placeholder="identity" name="identity" /></div>
		<div><label>Password:</label><input type="text" class="field" value="" placeholder="password" name="password" /></div>
		<div><label>ssId:</label><input type="text" class="field" value="<?php echo trim($ssId); ?>" placeholder="ssId" name="ssId" /></div>		
		<div><label>WalletAddress:</label><input type="text" class="field" value="" placeholder="walletAddress" name="walletAddress" /></div>
		<div><label></label><input type="submit" class="field" value="submit" /></div>
	</div>
</fieldset>
<fieldset><legend>Avatar</legend>
	<div class="settings">
		<div><label>WalletAddress:</label><input type="text" class="field" value="" placeholder="walletAddress" id="walletAddress" /></div>
		<div><label></label><input type="button" class="field" id="generator" value="check" /></div>
		<div>
			<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar256" width="256" title="256">
			<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar128" width="128" title="128">
			<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar64" width="64" title="64">
		</div>
	</div>
</fieldset>
</form>

<script type="text/javascript">
$("#walletAddress").on("change keyup", function(event) {
	event.preventDefault();
	event.stopPropagation();
	
	requestAvatar($("#walletAddress").val());
});

$("#generator").on("click", function(event) {
	event.preventDefault();
	event.stopPropagation();
	
	requestAvatar($("#walletAddress").val());
});

function requestAvatar(walletAddress) {
	$.post(
		"/api/", { module: "AVATAR", walletAddress: walletAddress }
	).done(
		function( data ) {
			var obj = jQuery.parseJSON(data);

			$("#avatar256").attr("src", obj.imageData);
			$("#avatar128").attr("src", obj.imageData);
			$("#avatar64").attr("src", obj.imageData);
			
			$('#avatars').find('img').each(function(i) { 
				if ($(this).prop("title") == obj.walletAddress) {
					$(this).prop("src", obj.imageData);
					return false;
				}
			}); 
		}
	);
}

$(document).ready(function(){
    $(window).keydown(function(event){
        if(event.keyCode == 13 && event.target.nodeName!='TEXTAREA')
        {
          event.preventDefault();
          return false;
        }
    });
});
</script>
</body>
</html>

