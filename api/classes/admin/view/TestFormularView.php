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
.description {
	display: inline;
	float: right;
}
  </style>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
 </head>
 <body>
<form id="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<fieldset><legend>Settings</legend>
	<div class="settings">
		<div><label>Module:</label><select name="module" class="field">
		<option value="AvatarGenerator">AvatarGenerator</option>
		<option value="UserManagementLogin" disabled="disabled">UserManagementLogin</option>
		<option value="UserManagementAutoLogin" disabled="disabled">UserManagementAutoLogin</option>
		<option value="RequestPasswordReset" disabled="disabled">RequestPasswordReset</option>
		<option value="SubmitPasswordReset" disabled="disabled">SubmitPasswordReset</option>
		<option value="UserManagementLogout" disabled="disabled">UserManagementLogout</option>
		<option value="">TestFormular</option>
		</select></div>
		<div><label>UserEmail:</label><input type="text" class="field" value="" placeholder="email" name="userEmail" /></div>
		<div><label>UserPass:</label><input type="text" class="field" value="" placeholder="password" name="userPass" /></div>
		<div><label>UserName:</label><input type="text" class="field" value="" placeholder="username" name="userName" /></div>
		<div><label>UserPhone:</label><input type="text" class="field" value="" placeholder="phone" name="userPhone" /></div>
		<div><label>UserToken:</label><input type="text" class="field" value="" placeholder="token" name="userToken" /></div>
		<div><label>WalletAddress:</label><input type="text" class="field" value="" placeholder="walletAddress" name="walletAddress" /></div>
		<div><label></label><input type="submit" class="field" value="submit" /></div>
	</div>
	<div class="description">
		<ul>
			<li>url : <?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?></li>
			<li>post variable : 
				<ul>
					<li>"module" : "AvatarGenerator"</li>
					<li>"walletAddress" : "1BoatSLRHtKNngkdXEeobR76b53LETtpyT"</li>
				</ul>
			</li>
		</ul>
	</div>
</fieldset>
<fieldset><legend>Avatar Gallery</legend>
<div id="avatars"><div>
</fieldset>
<fieldset><legend>Avatar</legend>
	<div class="settings">
		<div><label>WalletAddress:</label><input type="text" class="field" value="" placeholder="walletAddress" id="walletAddress" /></div>
		<div><label></label><input type="button" class="field" id="generator" value="check" /></div>
		<div>
			<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar192" width="192" title="192">
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

	if (!addAvatar(walletAddress)) {
		$.post(
			"/api/", { module: "AvatarGenerator", walletAddress: walletAddress }
		).done(
			function( data ) {
				console.log(data);
				var obj = jQuery.parseJSON(data);
				console.log(data);
				$("#avatar192").attr("src", obj.imageData);
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
}

function addAvatar(walletAddress) {
	var imageData =  "/api/img/loading.svg";
	var done = 1;
	
	if (walletAddress != "") {
		done = 0;
		
		$('#avatars').find('img').each(function(i) { 
			if ($(this).prop("title") == walletAddress) {
				done = 1;
				return false;
			}
		}); 
		
		if (!done) {
			$("#avatars").append("<div style=\"display:inline;margin-right:2px;margin-bottom:2px;\"><img src=\"" + imageData +  "\" width=\"64\" title=\"" + walletAddress + "\"></div>");
		}
	}
	
	return done;
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

