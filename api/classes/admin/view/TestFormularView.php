<!DOCTYPE html>
<html lang="en">
<head>
  <title>Api Avarkey Modules</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>

<div class="jumbotron text-center mb-3">
  <h1>Api Avarkey</h1>
  <p>Test each module separately and checkout your possible personal avartar based on your walletAddress</p> 
</div>
<div class="container mb-3">
<div class="card">
  <div class="card-header">Settings</div>
  <div class="card-body">
		<div class="form-group">
			<label for="module">module:</label>
			<select id="module" class="form-control">
			<option value="">Please select...</option>
		<?php foreach ($modules as $data) { ?>
			<option value="<?php echo $data->name; ?>" <?php echo (($data->selected) ? "selected=\"selected\"" : ""); ?>><?php echo $data->name; ?></option>
		<?php } ?>
			</select>
		</div>
		<?php foreach ($modules as $data) { ?>
			<p class="card-text text-info paramset <?php echo strtolower($data->name); ?>" <?php echo ((!$data->selected) ? "style=\"display:none;\"" : ""); ?>>
				<?php echo $data->description; ?>
			</p>
			<?php foreach ($data->blocks as $key => $params) { ?>
			<form method="<?php echo $methods[$key]; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="paramset <?php echo strtolower($data->name); ?>" <?php echo ((!$data->selected) ? "style=\"display:none;\"" : ""); ?>>
				<input type="hidden" name="NGINX" value="<?php echo $key; ?>" />
				<input type="hidden" name="module" value="<?php echo strtolower($data->name); ?>" />
				<div class="card mb-3 border-info">
					<div class="card-header"><?php echo $key; ?></div>
					<div class="card-body">
				<?php foreach ($params as $index => $input) { ?>
				<div class="form-group">
					<label for="<?php echo $input->id; ?>"><?php echo $input->label; ?>:</label>
					<input type="<?php echo $input->input; ?>" class="form-control" value="" placeholder="<?php echo $input->placeholder; ?>" id="<?php echo $input->id; ?>" name="<?php echo $input->name; ?>" />
				</div>
				<?php } ?>
				<div class="form-check mb-3">
					<input type="checkbox" class="form-check-input" id="directOutput_<?php echo strtolower($data->name); ?>">
					<label class="form-check-label" for="directOutput_<?php echo strtolower($data->name); ?>">Check me for direct output</label>
				</div>
				<button type="button" class="btn btn-primary moduletest">Submit</button>
					</div>
				</div>  
			</form>	
			<?php } ?>
		<?php } ?>
  </div>
  <div class="card-footer">
	<div class="container-fluid">
		<div class="form-group">
			<label for="exampleFormControlTextarea1">Result:</label>
			<textarea class="form-control" id="jsondata" rows="3"></textarea>
		</div>
	</div>
  </div>  
</div>		
</div>
<div class="container mb-2">
<form id="avatarform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="card">
  <div class="card-header">Avatar Life Preview</div>
  <div class="card-body">
		<div class="form-group">
		<label for="walletAddress">Wallet address:</label>
		<input type="text" class="form-control" value="" placeholder="Please insert your wallet address..." id="walletAddress" />
		</div>
		<button type="button" id="generator" class="btn btn-primary">Submit</button>
  </div> 
  <div class="card-footer">
	<div class="container-fluid d-none">
		<img class="float-left" style="margin:5px; width:50%" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar256" alt="256">
		<img class="float-left" style="margin:5px; width:25%" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar128" alt="128">
		<img class="float-left" style="margin:5px; width:12.5%" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="avatar64" alt="64">
	</div>
  </div>
</div>
</form>
</div>
<div class="jumbotron text-center" style="margin-bottom:0">
  <p>Copyright © EpitomeCL 2018</p>
</div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
	$(".paramset").hide();
	$(".paramset :input").prop("disabled", true);
	
	if ($("#module").val() != "") {
		$(".paramset." + $("#module").val().toLowerCase()).show();
		$(".paramset." + $("#module").val().toLowerCase() + " :input").prop("disabled", false);
	}
});

$("#module").on("change", function(event) {
	event.preventDefault();
	event.stopPropagation();
	
	$(".paramset").hide();
	$(".paramset :input").prop("disabled", true);
	$(".paramset." + this.value.toLowerCase()).show();
	$(".paramset." + this.value.toLowerCase() + " :input").prop("disabled", false);
	$("#jsondata").text("");
});

$(".moduletest").on("click", function(event) {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');

	if ($("#module").val() != "") {
		if (form.find(".form-check-input").prop("checked")) {
			form.submit();
		} else if (form.prop("method") == "post") {
			requestPost(form.serialize());
		} else {
			requestGet(form.serialize());
		}
	}
});

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

function requestGet(data) {
	$.get(
		"/api/", data
	).done(
		function( data ) {
			$("#jsondata").text(data);
		}
	).fail( function(xhr, textStatus, error) {
        $("#jsondata").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
    });
}

function requestPost(data) {
	$.post(
		"/api/", data
	).done(
		function( data ) {
			$("#jsondata").text(data);
		}
	).fail( function(xhr, textStatus, error) {
        $("#jsondata").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
    });
}

function requestAvatar(address) {
	$.post(
		"/api/", { module: "AVATAR", address: address }
	).done(
		function( data ) {
			var obj = jQuery.parseJSON(data);

			$("#avatar256").attr("src", obj.imageData);
			$("#avatar128").attr("src", obj.imageData);
			$("#avatar64").attr("src", obj.imageData);
			
			$("#avatar256").parent().removeClass("d-none");
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

