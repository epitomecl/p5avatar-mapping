<?php

$canvasId = intval($_POST["canvasId"]);
$layerId = intval($_POST["layerId"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Avarkey</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  

    <!-- Custom Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

<style>	
#sidebar-wrapper{position:fixed;z-index:2;right:0;width:250px;height:100%;-webkit-transition:all .4s ease 0s;transition:all .4s ease 0s;-webkit-transform:translateX(250px);transform:translateX(250px);background:#1d809f;border-left:1px solid rgba(255,255,255,.1)}.sidebar-nav{position:absolute;top:0;width:250px;margin:0;padding:0;list-style:none}.sidebar-nav li.sidebar-nav-item a{display:block;text-decoration:none;color:#fff;padding:15px}.sidebar-nav li a:hover{text-decoration:none;color:#fff;background:rgba(255,255,255,.2)}.sidebar-nav li a:active,.sidebar-nav li a:focus{text-decoration:none}.sidebar-nav>.sidebar-brand{font-size:1.2rem;background:rgba(52,58,64,.1);height:80px;line-height:50px;padding-top:15px;padding-bottom:15px;padding-left:15px}.sidebar-nav>.sidebar-brand a{color:#fff}.sidebar-nav>.sidebar-brand a:hover{color:#fff;background:0 0}#sidebar-wrapper.active{right:250px;width:250px;-webkit-transition:all .4s ease 0s;transition:all .4s ease 0s}

.more-toggle{position:fixed;right:75px;top:15px;width:50px;height:50px;text-align:center;color:#fff;background:rgba(52,58,64,.5);line-height:50px;z-index:999}
.menu-toggle{position:fixed;right:15px;top:15px;width:50px;height:50px;text-align:center;color:#fff;background:rgba(52,58,64,.5);line-height:50px;z-index:999}.menu-toggle:focus,.menu-toggle:hover{color:#fff}.menu-toggle:hover{background:#343a40}

.scroll-to-top{position:fixed;right:15px;bottom:15px;display:none;width:50px;height:50px;text-align:center;color:#fff;background:rgba(52,58,64,.5);line-height:45px}.scroll-to-top:focus,.scroll-to-top:hover{color:#fff}.scroll-to-top:hover{background:#343a40}.scroll-to-top i{font-weight:800}

.sidebar-nav-item.active {background:#FFC000;}
 
.sidebar-nav li.sidebar-nav-item.active a {color:#1d809f;}

.dropzone {
	cursor: pointer;	
}

</style>
</head>
<body id="page-top">

<!-- Navigation -->
    <a class="more-toggle rounded js-scroll-trigger" href="#more">
      <i class="fas fa-quote-left"></i>
    </a>
	<a class="menu-toggle rounded" href="#">
      <i class="fas fa-bars"></i>
    </a>
    <nav id="sidebar-wrapper">
      <ul class="sidebar-nav">
        <li class="sidebar-brand">
          <a href="index.php">Home</a>
        </li>
		<li class="sidebar-nav-item">
          <a href="canvas.php">Management</a>
        </li>
		<li class="sidebar-nav-item">
          <a href="logout.php">Logout</a>
        </li>		
      </ul>
    </nav>
	
<section class="home-section" id="home">
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1>Layer Management</h1> 
    <p>Upload images and give them a value...</p> 
  </div>
</div>
</section>

<div class="container buttons mb-3">
	<div class="row">
		<div class="col-lg-12 mx-auto">
			<div class="btn-toolbar justify-content-center">
				<form method="POST" action="canvas.php">
					<input type="hidden" name="canvasId" value="<?php echo $canvasId; ?>">
					<button type="submit" class="btn btn-primary canvas mr-1 mb-1">Back</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="container images mb-3">
	<input type="file" class="multiple" multiple="multiple" accept=".png,.gif,.jpg" style="display:none"> 
	<div class="card-columns">
		<div class="card border-info upload dropzone">
			<img class="card-img-top" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==" alt="upload area">
			<div class="card-body">
				<h5 class="card-title">Upload Area</h5>
				<p class="card-text">Drag and drop or click to open file upload dialog. Upload file size must smaller than one MByte. Uploaded files stored as PNG.</p>
			</div>
			<div class="card-footer bg-transparent border-info"><p id="failed" class="card-text"></p></div>
		</div>	
	</div>
</div>

<section class="more-section" id="more">
</section>

<div class="jumbotron text-center" style="margin-bottom:0">
  <p>Copyright © EpitomeCL 2018</p>
</div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded js-scroll-trigger" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
	
<script id="layer" type="text/x-custom-template">
	<button type="button" class="btn ${shape}-${color} layer mr-1 mb-1" data-layerid="${layerId}">
		${layerName}, <span class="counter">${counter}</span>&#127873;
	</button>
</script>

<script id="image" type="text/x-custom-template">
	<div class="card border-dark image" id="cardId_${cardId}">
		<img class="card-img-top" id="fileId_${fileId}" src="${src}" alt="${alt}">
		<div class="card-body">
			<h5 class="card-title fileName">${fileName}</h5>
			<div class="form-group">
			  <label for="fee_${cardId}">Fee (<b class="text-info currency">${currency}</b>):</label>
			  <input type="text" class="form-control fee" id="fee_${cardId}" name="fee" value="${fee}">
			</div>
			<button type="button" class="btn btn-primary update">Update</button>
			<button type="button" class="btn btn-primary remove">Image delete</button>
		</div>
	</div>
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script type="text/javascript">

function callCanvas(canvasId, needle) {
	var formData = new FormData();
    formData.append('module', 'canvas');
    formData.append('canvasId', canvasId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var layerIds = obj.layerIds;

		for (var index = 0; index < layerIds.length; index++) {
			var layerId = layerIds[index];
			
			callLayer(layerId);
						
			if (needle == layerId) {
				callImages(layerId);
			}
		}
	});
}

function callLayer(layerId) {
	var formData = new FormData();
    formData.append('module', 'layer');
    formData.append('layerId', layerId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj){
		var fileIds = obj.fileIds;
		var layertpl = $("#layer").text();
		var items = {
			layerName : obj.name,
			layerId : layerId,
			counter : fileIds.length,
			shape : ((layerId == <?php echo $layerId; ?>) ? 'btn' : 'btn-outline'),
			color : (fileIds.length > 0) ? "success" : "warning"
		};		
		
		$(".container.buttons .btn-toolbar").append(render(layertpl, items));
	});	
}

function callImages(layerId) {
	var formData = new FormData();
    formData.append('module', 'layer');
    formData.append('layerId', layerId);

	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj){
		var fileIds = obj.fileIds;
		
		for (var index = 0; index < fileIds.length; index++) {
			callImage(layerId, fileIds[index]);
		}		
	});		
}

function callImage(cardId, fileId) {
	var formData = new FormData();
    formData.append('module', 'image');
    formData.append('fileId', fileId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	requestPost(data, function(obj){
		var imagetpl = $("#image").text();
		var items = {
			src : obj.imageData,
			alt : obj.fileName,
			cardId : cardId,
			fileId : fileId,
			fileName : obj.fileName,
			fee : obj.fee,
			currency : obj.currency,
		};
		
		$(".card-columns").append(render(imagetpl, items));
	});
}

function callPrice(fileId) {
	var formData = new FormData();
    formData.append('module', 'price');
    formData.append('fileId', fileId);
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	requestGet(data, function(obj){
		var fileId = obj.fileId;
		var card = $("#fileId_"+fileId).closest(".card");
		card.find(".fee").val(obj.fee);
		card.find(".currency").html(obj.currency);
	});
}

function updatePrice(fileId, fee) {
	var formData = new FormData();
    formData.append('module', 'price');
    formData.append('fileId', fileId);
	formData.append('fee', fee);
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	var card = $("#fileId_"+fileId).closest(".card");

	card.removeClass( "border-dark" ).addClass( "border-warning" );
					
	requestPost(data, function(obj){
		card.removeClass( "border-warning" ).addClass( "border-success" );
	});	
}

function render(template, items) {
	return template
		.split(/\$\{(.+?)\}/g)
		.map(function(value, index) {
			return (index % 2 == 0) ? value : items[value];
		})
		.join("");	
}

function requestGet(data, callback) {
	$.get(
		"/api/", data
	).done(
		function( data ) {
			callback(jQuery.parseJSON(data));
		}
	).fail( function(xhr, textStatus, error) {
        $("#failed").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
    });
}

function requestPost(data, callback) {
	$.post(
		"/api/", data
	).done(
		function( data ) {
			callback(jQuery.parseJSON(data));
		}
	).fail( function(xhr, textStatus, error) {
        $("#failed").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
    });
}

$(document).ready(function(){
    $(window).keydown(function(event){
        if(event.keyCode == 13 && event.target.nodeName!='TEXTAREA')
        {
          event.preventDefault();
          return false;
        }
    });
	
	callCanvas(<?php echo $canvasId; ?>, <?php echo $layerId; ?>);
});

$('div.container.buttons').on('click', 'button.layer', function() {
	event.preventDefault();
	event.stopPropagation();
	
	$(".card-columns").find("div.card.image").remove();
	
	callImages($(this).data("layerid"));
});

$('div.container.images').on('click', 'button.update', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var card = $(this).closest("div.card.image");
	var fileId = card.find("img").prop('id').replace("fileId_", "");
	var fee = card.find(".fee").val();
	
	updatePrice(fileId, fee);
});

$('div.container.images').on('click', 'button.remove', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var card = $(this).closest("div.card.image");
	var fileId = card.find("img").prop('id').replace("fileId_", "");
	
	if (confirm('Are you sure deleting this image?')) {
		deleteFile(new Array(fileId));
	}
});
</script>
<script type="text/javascript">
	
document.addEventListener("DOMContentLoaded", init, false);
	
function init() {
	document.querySelector('input.multiple').addEventListener('change', fileselect_handler, false);
	document.querySelector('div.dropzone').addEventListener('drop', fileselect_handler, false);
	
	/* events fired on the drop targets */
	document.querySelector('div.dropzone').addEventListener("dragover", function( event ) {
		// prevent default to allow drop
		event.preventDefault();
	}, false);	
}
  
function fileselect_handler(event) {
	if(!window.FileReader) return;
		
	event.preventDefault();
	  
	var element = $("div.filezone").first();
	var files = event.target.files || event.dataTransfer.files;
	var filesArr = Array.prototype.slice.call(files);
		
	filesArr.forEach(function(f) {
		console.log("file check..." + f.type);
		
		if(!f.type.match("image.*")) {
			return;
		}
	
		var reader = new FileReader();
		reader.onload = function (e) {
			var src = e.target.result;
			var d = new Date();
			var n = d.getTime();			
			var cardId = '' + n + "_" + $("div.card.image").length + 1;
			var imagetpl = $("#image").text();
			var items = {
				src : src,
				alt : f.name,
				cardId : cardId,
				fileId : 0,
				fileName : f.name,
				fee : 0,
				currency : 'N/A'
			};
		
			$(".card-columns .dropzone").after(render(imagetpl, items));

			sendFile(f, cardId);
		}
		reader.readAsDataURL(f); 
	});
	
	resetFileInput();
}

function sendFile(file, cardId) {
	var formData = new FormData();
	formData.append('module', 'UPLOAD');
	formData.append('file', file);	
	formData.append('cardId', cardId);
	formData.append('layerId', '<?php echo $layerId ; ?>');

	var xhttp = new XMLHttpRequest();
	xhttp.upload.addEventListener("progress", updateProgress);
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 200) {
				console.log(this.responseText);
				var uploads = JSON.parse(this.responseText);
				
				for (var i = 0; i < uploads.length; i++) {
					var upload = uploads[i];
					var fileId = upload.fileId;
					var cardId = upload.cardId;
					var fileName = upload.fileName;
					
					$("#cardId_"+cardId).find("img").prop('id', "fileId_"+fileId);
					$("#cardId_"+cardId).find(".fileName").html(fileName);
					$("#cardId_"+cardId).removeClass( "border-dark" ).addClass( "border-success" );
					
					var counter = $("div.card.image").length;
					$(".container.buttons").find("[data-layerid='" + <?php echo $layerId; ?> + "']").find(".counter").html(counter);
					
					callPrice(fileId);
				}
			} else {
				$("#failed").text(xhttp.status + " :: " + xhttp.statusText + " :: " + xhttp.responseText);
			}
		}
	};
	
	xhttp.open("POST", "/api/");
	xhttp.send(formData);
}

function deleteFile(fileIds) {
	var formData = new FormData();
	formData.append('module', 'UPLOAD');
	formData.append('unlink[]', fileIds);
	formData.append('NGINX', 'DELETE');

	var xhttp = new XMLHttpRequest();
	xhttp.upload.addEventListener("progress", updateProgress);
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if (this.status == 200) {
				console.log(this.responseText);
				var uploads = JSON.parse(this.responseText);
				
				for (var i = 0; i < uploads.length; i++) {
					var upload = uploads[i];
					var fileId = upload.fileId;
					var filename = upload.filename;
					var original = upload.original;
					
					$("#fileId_"+fileId).closest(".card").remove();
					
					var counter = $("div.card.image").length;
					$(".container.buttons").find("[data-layerid='" + <?php echo $layerId; ?> + "']").find(".counter").html(counter);
				}
			} else {
				$("#failed").text(xhttp.status + " :: " + xhttp.statusText + " :: " + xhttp.responseText);
			}
		}
	};
	
	xhttp.open("POST", "/api/");
	xhttp.send(formData);
}

function updateProgress (event) {
	if (event.lengthComputable) {
		var loaded = Math.round(event.loaded * 100 / event.total);
	}
}

function resetFileInput() {
	var obj = $("input.multiple").clone();
	
	$("input.multiple").replaceWith(obj);
	$("input.multiple").val('');

	var elm = document.querySelector("input.multiple");
	
	elm.removeEventListener('change', fileselect_handler, false);
	elm.addEventListener('change', fileselect_handler, false);
}

$("div.dropzone").click(function(event) {
	event.stopPropagation();
	event.preventDefault();	

	$("input.multiple").trigger('click'); 
});

</script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script type="text/javascript">
!function(a){"use strict";a(".menu-toggle").click(function(e){e.preventDefault(),a("#sidebar-wrapper").toggleClass("active"),a(".menu-toggle > .fa-bars, .menu-toggle > .fa-times").toggleClass("fa-bars fa-times"),a(this).toggleClass("active")}),a('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function(){if(location.pathname.replace(/^\//,"")==this.pathname.replace(/^\//,"")&&location.hostname==this.hostname){var e=a(this.hash);if((e=e.length?e:a("[name="+this.hash.slice(1)+"]")).length)return a("html, body").animate({scrollTop:e.offset().top},1e3,"easeInOutExpo"),!1}}),a("#sidebar-wrapper .js-scroll-trigger").click(function(){a("#sidebar-wrapper").removeClass("active"),a(".menu-toggle").removeClass("active"),a(".menu-toggle > .fa-bars, .menu-toggle > .fa-times").toggleClass("fa-bars fa-times")}),a(document).scroll(function(){100<a(this).scrollTop()?a(".scroll-to-top").fadeIn():a(".scroll-to-top").fadeOut()})}(jQuery);var onMapMouseleaveHandler=function(e){var a=$(this);a.on("click",onMapClickHandler),a.off("mouseleave",onMapMouseleaveHandler),a.find("iframe").css("pointer-events","none")},onMapClickHandler=function(e){var a=$(this);a.off("click",onMapClickHandler),a.find("iframe").css("pointer-events","auto"),a.on("mouseleave",onMapMouseleaveHandler)};$(".map").on("click",onMapClickHandler);

(function($) {
  "use strict"; // Start of use strict

  $(".more-toggle").click(function(e) {
    e.preventDefault();
	$(this).fadeOut();
  });
  
  $(".scroll-to-top").click(function(e) {
		e.preventDefault();
		$('.more-toggle').fadeIn();
  });
  
  $(".menu-toggle").click(function(e) {
    e.preventDefault();
    
	if ($(this).hasClass("active")) {
		$('.more-toggle').fadeOut();
	} else {
		$('.more-toggle').fadeIn();
	}
  });
  
})(jQuery); // End of use strict
</script>

</body>
</html>