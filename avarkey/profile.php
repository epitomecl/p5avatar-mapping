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


#leftPanel{
    background-color:#0079ac;
    color:#fff;
    text-align: center;
}

#rightPanel{
    min-height:415px;
}

/* Credit to bootsnipp.com for the css for the color graph */
.colorgraph {
  height: 5px;
  border-top: 0;
  background: #c4e17f;
  border-radius: 5px;
  background-image: -webkit-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: -moz-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: -o-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
  background-image: linear-gradient(to right, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
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
          <a href="login.php">Login</a>
        </li>		
		<li class="sidebar-nav-item active">
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
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1>Profile Management</h1> 
    <p>Adjust your profile, smile and upload a new profile picture...</p> 
  </div>
</div>
</section>

<div class="container profile mb-3 ">
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
	
<script id="profile" type="text/x-custom-template">
	<div class="row" id="main">
        <div class="col-md-4" id="leftPanel">
            <div class="row">
                <div class="col-md-12">
						<div class="p-5">
							<img src="${src}" alt="${alt}"  title="${alt}" class="img-fluid rounded-circle">
						</div>
        				<h2>${firstName} ${lastName}</h2>
        				<p>${about}</p>
        		</div>
            </div>
        </div>
        <div class="col-md-8" id="rightPanel">
            <div class="row">
				<div class="col-md-12">
					<form method="POST" action="" class="paramset" enctype="multipart/form-data">
						<input type="hidden" name="module" value="profile">
						<input type="hidden" name="profileId" value="${profileId}">
						<input type="hidden" name="imageData" value="">
						<h2>Edit your profile</h2>
						<hr class="colorgraph">
						<div class="form-group">
							<label for="profile_post_firstname">firstName:</label>
							<input type="text" class="form-control" placeholder="The first name of user (string)" id="profile_post_firstname" name="firstName" value="${firstName}">
						</div>
						<div class="form-group">
							<label for="profile_post_lastname">lastName:</label>
							<input type="text" class="form-control" placeholder="The last name of user (string)" id="profile_post_lastname" name="lastName" value="${lastName}">
						</div>
						<div class="form-group">
							<label for="profile_post_alias">alias:</label>
							<input type="text" class="form-control" placeholder="The alias name as designer (string)" id="profile_post_alias" name="alias" value="${alias}">
						</div>
						<div class="form-group">
							<label for="profile_post_email">email:</label>
							<input type="text" class="form-control" placeholder="The email of user (string)" id="profile_post_email" name="email" value="${email}">
						</div>
						<div class="form-group">
							<label for="profile_post_about">about:</label>
							<textarea class="form-control" rows="5" placeholder="Something about me (string)" id="profile_post_about" name="about">${about}</textarea>
						</div>						
						<div class="custom-file">
							<label class="custom-file-label" for="profile_post_file">file:</label>
							<input type="file" class="custom-file-input" placeholder="The foto upload of profile image (file)" id="profile_post_file" name="file">
						</div>
						<hr class="colorgraph">
						<div class="row">
							<div class="col-xs-12 col-md-6"></div>
							<div class="col-xs-12 col-md-6"><a href="#" class="btn btn-success btn-block btn-lg">Save</a></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>		
</script>
	
<script id="canvas" type="text/x-custom-template">
	<button type="button" class="btn btn-outline-${color} mr-1 mb-1" data-canvasid="${canvasId}" ${disabled}>
		${canvasName}, ${counter}&#127873;
	</button>
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script type="text/javascript">

function callProfile(userId) {
	var formData = new FormData();
    formData.append('module', 'profile');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var profiletpl = $("#profile").text();
		var items = {
			profileId : obj.profileId,
			firstName : obj.firstName,
			lastName : obj.lastName,
			about : obj.about,
			alias : obj.alias,
			email: obj.email,
			src : obj.imageData,
			alt : obj.firstName + " " + obj.lastName
		};

		$(".container.profile").prepend(render(profiletpl, items));
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
        $("#jsondata").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
    });
}

function requestPost(data, callback) {
	$.post(
		"/api/", data
	).done(
		function( data ) {
			console.log(data);
			callback(jQuery.parseJSON(data));
		}
	).fail( function(xhr, textStatus, error) {
        $("#jsondata").text(xhr.status + " :: " + xhr.statusText + " :: " + xhr.responseText);
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
	
	callProfile(1);
});

$('div.container.profile').on('click', 'a.btn.btn-success', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');
	var data = form.serialize();
			
	requestPost(data, function(obj) {
		var profiletpl = $("#profile").text();
		var items = {
			profileId : obj.profileId,
			firstName : obj.firstName,
			lastName : obj.lastName,
			about : obj.about,
			alias : obj.alias,
			email: obj.email,
			src : obj.imageData,
			alt : obj.firstName + " " + obj.lastName
		};

		$("div.container.profile").empty();
		$("div.container.profile").append(render(profiletpl, items));
	});			
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




