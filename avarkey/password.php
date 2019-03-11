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
		<li class="sidebar-nav-item active">
          <a href="login.php">Login</a>
        </li>		
		<li class="sidebar-nav-item">
          <a href="profile.php">Profile</a>
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
<div class="jumbotron">
  <h1 class="display-4">Hello, welcome to Avarkey!</h1>
  <p class="lead">This is a simple hero unit, a creative component for calling extra attention to your crypto currency account.</p>
</div>
</section>

<div class="container mb-3">
	<div class="row">
		<div class="col-lg-12 mx-auto">
			<div class="btn-toolbar">
				<button type="button" class="btn btn-outline-info mr-1 mb-1" data-link="apikey">Apikey</button>
				<button type="button" class="btn btn-outline-warning mr-1 mb-1" data-link="signup">Signup</button>
				<button type="button" class="btn btn-outline-success mr-1 mb-1" data-link="login">Login</button>
				<button type="button" class="btn btn-secondary mr-1 mb-1" data-link="password">Forgot password</button>
			</div>
		</div>
	</div>
</div>

<div class="container mb-3">
	<p class="text-info">
				If user session is alive, password data can be managed inside a time slot of 15 minutes.
A POST request will send an email with an token to the email receiver.
A GET request will perform some action on client side (maybe show a password input form).
A PUT request will be handle current token and password.			</p>
	<form method="POST" action="" class="paramset password" style="">
		<input type="hidden" name="module" value="password">
		<div class="card mb-3 border-info">
			<div class="card-header">POST</div>
			<div class="card-body">
				<div class="form-group">
					<label for="password_post_email">email:</label>
					<input type="text" class="form-control" value="" placeholder="The email for receiving request token (string)" id="password_post_email" name="email">
				</div>
				<div class="form-group">
					<label for="password_post_firstname">firstName:</label>
					<input type="text" class="form-control" value="" placeholder="The firstname of user" id="password_post_firstname" name="firstName">
				</div>
				<button type="button" class="btn btn-primary mt-3">Submit</button>
			</div>
			<div class="card-footer bg-transparent border-info"><p id="failed" class="card-text"></p></div>			
		</div>  
	</form>
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
	
<script id="canvas" type="text/x-custom-template">
	<button type="button" class="btn btn-outline-${color} mr-1 mb-1" data-canvasid="${canvasId}" ${disabled}>
		${canvasName}, ${counter}&#127873;
	</button>
</script>

<script id="preview" type="text/x-custom-template">
	<div class="card preview">
		<img class="card-img-top" src="${src}" alt="${alt}">
		<div class="card-body">
			<h5 class="card-title">Preview ${canvasName}</h5>
			<p class="card-text">Keep it for ${fee} ${currency}</p>
			<a href="#" class="card-link wishlist">On the wishlist</a>
			<a href="#" class="card-link booking">Booking</a>
		</div>
	</div>	
</script>
		
<script id="layer" type="text/x-custom-template">
	<div class="card">
		<div id="carousel_${layerId}" class="carousel slide" data-ride="carousel">
		  <ol class="carousel-indicators">
		  </ol>
		  <div class="carousel-inner">
		  </div>
		  <a class="carousel-control-prev" href="#carousel_${layerId}" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <a class="carousel-control-next" href="#carousel_${layerId}" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
		</div>	
		<div class="card-body">
			<h5 class="card-title">${layerName}</h5>
		</div>
	</div>
</script>

<script id="indicator" type="text/x-custom-template">
	<li data-target="#carousel_${layerId}" data-slide-to="${index}" class="${active}"></li>
</script>

<script id="image" type="text/x-custom-template">
	<div class="carousel-item ${active}" data-fileid="${fileId}">
		<img class="d-block w-100" src="${src}" alt="${alt}">
		<div class="carousel-caption d-none d-md-block">
			<h5>${fee} ${currency}</h5>
			<p>${fileName}</p>
		</div>		
	</div>
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script type="text/javascript">

function callPreview(canvasId) {
	var formData = new FormData();
    formData.append('module', 'preview');
    formData.append('canvasId', canvasId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var previewtpl = $("#preview").text();
		var items = {
			canvasName: obj.canvas,
			fee: obj.fee,
			currency: obj.currency,
			src: obj.imageData
		};

		$(".card-columns").prepend(render(previewtpl, items));
	});
}

function callCanvases() {
	var formData = new FormData();
    formData.append('module', 'canvases');
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj){
		var canvases = obj;
				
		for (var index = 0; index < canvases.length; index++) {
			data = canvases[index];
			
			var canvastpl = $("#canvas").text();
			var items = {
				canvasId: data.id,
				canvasName : data.name,
				currency : data.currency,
				counter : data.counter,
				userId : data.userId,
				disabled : (data.counter == 0) ? "disabled" : "",
				color: (data.counter > 0) ? "success" : "warning"
			};		
		
			$(".container.canvas .btn-toolbar").append(render(canvastpl, items));
			
			if (index == 0) {
				callPreview(data.id);
				callCanvas(data.id);
			}
		}
	});	
}

function callCanvas(canvasId) {
	var formData = new FormData();
    formData.append('module', 'canvas');
    formData.append('canvasId', canvasId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var layerIds = obj.layerIds;

		for (var index = 0; index < layerIds.length; index++) {
			callLayer(layerIds[index]);
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
			layerName: obj.name,
			layerId : layerId,
			layerPos : obj.position
		};		
		
		$(".card-columns").append(render(layertpl, items));
		
		$("#carousel_" + layerId).on('slid.bs.carousel', function () {
			updatePreview();
		});
		
		for (var index = 0; index < fileIds.length; index++) {
			callImage(layerId, fileIds[index]);
		}
	});	
}

function callImage(layerId, fileId) {
	var formData = new FormData();
    formData.append('module', 'image');
    formData.append('fileId', fileId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	requestPost(data, function(obj){
		var index = $("#carousel_"+layerId+" div.carousel-inner").children().length;
		var imagetpl = $("#image").text();
		var indicatortpl = $("#indicator").text();		
		var items = {
			src: obj.imageData,
			alt: obj.fileName,
			layerId : layerId,
			fileId : fileId,
			fileName : obj.fileName,
			fee : obj.fee,
			currency : obj.currency,
			index : index,
			active : (index == 0) ? "active" : ""
		};
		$("#carousel_"+layerId+" div.carousel-inner").append(render(imagetpl, items));
		$("#carousel_"+layerId+" ol.carousel-indicators").append(render(indicatortpl, items));
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

function updatePreview() {
	var fileIds = new Array();
	$(".card-columns").find(".carousel-item.active").each(function(i, el){
		fileIds.push($(el).data("fileid"));
	});

	var formData = new FormData();
    formData.append('module', 'preview');
    formData.append('fileIds', fileIds.join(","));
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestPost(data, function(obj) {
		var previewtpl = $("#preview").text();
		var items = {
			canvas: obj.canvas,
			fee: obj.fee,
			currency: obj.currency,
			src: obj.imageData
		};

		$(".card-columns .preview").replaceWith(render(previewtpl, items));
	});	
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
});

$('div.card-columns').on('click', 'a.wishlist', function() {
	event.preventDefault();
	event.stopPropagation();
	
    alert("click wishlist");
});

$('div.card-columns').on('click', 'a.booking', function() {
	event.preventDefault();
	event.stopPropagation();
	
    alert("click booking");
});

$("div.container form.paramset").on("click", 'button', function(event) {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');

	if ($("#module").val() != "") {
		if (form.prop("method") == "post") {
			requestPost(form.serialize(), function(obj) {
				$(location).attr('href', "login.php");
			});
		}
	}
});

$('div.container div.btn-toolbar').on('click', 'button', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var link = $(this).data("link");
	
	$(location).attr('href', link + ".php");
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