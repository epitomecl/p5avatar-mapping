<?php

$canvasId = intval($_POST["canvasId"]);

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
    <h1>Canvas Management</h1> 
    <p>Update name, hashtags, currency of canvas. Adjust position or change the name of each layer.</p> 
  </div>
</div>
</section>

<div class="container buttons mb-3">
	<div class="row">
		<div class="col-lg-12 mx-auto">
			<div class="btn-toolbar justify-content-center">

			</div>
		</div>
	</div>
</div>

<div class="container cards mb-3">
	<div class="card-columns">
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
	
<script id="canvas" type="text/x-custom-template">
	<button type="button" class="btn ${shape}-${color} mr-1 mb-1" data-canvasid="${canvasId}" data-counter="${counter}">
		${canvasName}, ${counter}&#127873;
	</button>
</script>

<script id="preview" type="text/x-custom-template">
	<div class="card preview">
		<img class="card-img-top" src="${src}" alt="${alt}">
		<div class="card-body">
			<h5 class="card-title">Preview ${canvasName}</h5>
			<p class="card-text">Current price for this preview is ${fee} ${currency}. 
			At the moment there are ${counter} layers. The size for this avatar is ${width} x ${height} pixel.</p>
		  <form method="POST" action="#">
		    <input type="hidden" name="canvasId" value="${canvasId}">
			<div class="form-group">
			  <label for="canvasName_${canvasId}">Name:</label>
			  <input type="text" class="form-control" id="canvasName_${canvasId}" name="name" value="${canvasName}">
			</div>
			<div class="form-group">
			  <label for="hashtags_${canvasId}">Hashtags:</label>
			  <input type="text" class="form-control" id="hashtags_${canvasId}" name="hashtags" value="${hashtags}">
			</div>
			<div class="form-group">
			  <label for="currency_${canvasId}">Currency:</label>
			  <input type="text" class="form-control" id="currency_${canvasId}" name="currency" value="${currency}">
			</div>			
			<button type="submit" class="btn btn-primary">Update</button>
		  </form>	
		</div>
	</div>	
</script>
		
<script id="layer" type="text/x-custom-template">
	<div class="card border-dark">
		<div class="card-body">
			<h5 class="card-title"><span class="layerName">${layerName}</span> <span class="float-right">${counter}&#127873;</span></h5>
		  <form method="POST" action="layer.php">
			<input type="hidden" name="module" value="layer">
		    <input type="hidden" name="canvasId" value="${canvasId}">
			<input type="hidden" name="layerId" value="${layerId}">
			<div class="form-group">
			  <label for="layerName_${layerId}">Name:</label>
			  <input type="text" class="form-control" id="layerName_${layerId}" name="name" value="${layerName}">
			</div>
			<div class="form-group">
			  <label for="position_${layerId}">Position:</label>
			  <select class="form-control" id="position_${layerId}" name="position">
				  ${positions}
			  </select>
			</div>
			<button type="button" class="btn btn-primary update">Update</button>
			<button type="submit" class="btn btn-primary">Add more &#127873;</button>
		  </form>			
		</div>
	</div>
</script>

<script id="option" type="text/x-custom-template">
	<option value="${value}" ${selected}>${text}</option>
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
			width : obj.width,
			height : obj.height,
			currency: obj.currency,
			hashtags : obj.hashtags.join(", "),
			counter : Object.keys(obj.parts).length,
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
		var needleId = <?php echo $canvasId; ?>;
		
		for (var index = 0; index < canvases.length; index++) {
			var canvas = canvases[index];
			var activeId = (needleId == 0 && index == 0) ? canvas.id : needleId;
			var canvastpl = $("#canvas").text();
			var items = {
				canvasId: canvas.id,
				canvasName : canvas.name,
				currency : canvas.currency,
				counter : canvas.counter,
				userId : canvas.userId,
				shape : ((activeId == canvas.id) ? 'btn' : 'btn-outline'),
				color: (canvas.counter > 0) ? "success" : "warning"
			};		
		
			$(".container.buttons .btn-toolbar").append(render(canvastpl, items));
			
			if (activeId == canvas.id) {
				callPreview(activeId);
				callCanvas(activeId);
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
			callLayer(canvasId, layerIds[index], layerIds.length);
		}
	});
}

function callLayer(canvasId, layerId, max) {
	var formData = new FormData();
    formData.append('module', 'layer');
    formData.append('layerId', layerId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj){
		var fileIds = obj.fileIds;
		var layertpl = $("#layer").text();
		var items = {
			canvasId : canvasId,
			layerName: obj.name,
			layerId : layerId,
			counter : fileIds.length,
			positions : renderPositions(max, obj.position)
		};		
		
		$(".card-columns").append(render(layertpl, items));
	});	
}

function renderPositions(max, needle) {
	var options = new Array();
	var optiontpl = $("#option").text();

	for (var position = 1; position <= max; position++) {
		var selected = (position == needle) ? "selected=\"selected\"" : "";
		var text = "...";
		
		switch (position) {
			case 1 : 
				text = position + ": bottom"; 
				break;
			case max : 
				text = max + ": top"; 
				break;
			default : 
				text = position + ". position"; 
				break;
		}
		
		var items = {
			value: position,
			text : text,
			selected : selected
		};
		options.push(render(optiontpl, items));
	}
	
	return options.join("\n");
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
	
	callCanvases();
});

$('div.container.buttons').on('click', 'button', function() {
	event.preventDefault();
	event.stopPropagation();
	
	$(".card-columns").empty();
	
	var canvasId = $(this).data("canvasid");
	
	callPreview(canvasId);
	callCanvas(canvasId);
	
	$(".container.buttons").find("button").each(function( index ) {
		var counter = $( this ).data("counter");
		var activeId = $(this).data("canvasid");
		var color = (counter = 0) ? "warning" : "success";
		var shape = ((activeId == canvasId) ? 'btn-' : 'btn-outline-');
		$( this ).removeClass( "btn-warning" );
		$( this ).removeClass( "btn-success" );
		$( this ).removeClass( "btn-outline-success" );
		$( this ).removeClass( "btn-outline-warning" );
		$( this ).addClass( shape + color);
	});
});

$('div.container.cards').on('click', 'button.update', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');
	var card = form.closest(".card");
	
	if (form.prop("method") == "post") {
		card.removeClass( "border-dark" ).addClass( "border-warning" );
					
		requestPost(form.serialize(), function(obj) {
			var layerName = obj.layerName;
			
			card.find(".layerName").html(layerName);
			card.removeClass( "border-warning" ).addClass( "border-dark" );
		});			
	}
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