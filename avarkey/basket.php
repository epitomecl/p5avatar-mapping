<?php

$userId = 1;

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
          <a href="login.php">Login</a>
        </li>		
		<li class="sidebar-nav-item">
          <a href="profile.php">Profile</a>
        </li>		
		<li class="sidebar-nav-item">
          <a href="canvas.php">Management</a>
        </li>
		<li class="sidebar-nav-item">
          <a href="wishlist.php">Wishlist &#127873;<span class="wishlist">0</span></a>
        </li>
		<li class="sidebar-nav-item active">
          <a href="basket.php">Basket &#127873;<span class="basket">0</span></a>
        </li>	
		<li class="sidebar-nav-item">
          <a href="avatar.php">Avatar &#127873;<span class="avatar">0</span></a>
        </li>		
		<li class="sidebar-nav-item">
          <a href="logout.php">Logout</a>
        </li>		
      </ul>
    </nav>
	
<section class="home-section" id="home">
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1>Your basket</h1> 
    <p>Now it's time to go to cashier counter...</p> 
  </div>
</div>
</section>

<div class="container preview mb-3">
	<div class="card-columns">
	</div>
</div>

<section class="more-section" id="more">
</section>

<div class="jumbotron text-center" style="margin-bottom:0">
  <p>Copyright © EpitomeCL <?php echo date("Y"); ?></p>
</div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded js-scroll-trigger" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
	
<script id="preview" type="text/x-custom-template">
	<div class="card preview" data-itemid="${itemId}">
		<img class="card-img-top" src="${src}" alt="${alt}" title="${title}">
		<div class="card-body">
			<h5 class="card-title">Preview ${canvasName}</h5>
			<p class="card-text">Buy it for <span class="fee">${fee}</span> ${currency}</p>
			<form method="post" action="/api/">
				<input type="hidden" id="fileIds" name="fileIds" value="${fileIds}" />
				<input type="hidden" id="userId" name="userId" value="<?php echo $userId; ?>" />
				<input type="hidden" id="module" name="module" value="preview" />
				<button type="button" class="btn btn-primary wishlist">Wishlist</button>
				<button type="button" class="btn btn-primary remove">&#128541;</button>
			</form>
		</div>
	</div>	
</script>

<script id="payment" type="text/x-custom-template">
	<div class="card payment border-dark">
		<div class="card-body">
			<p class="card-text">Total <span class="counter">${counter}</span> items for <span class="price">${price}</span></p>
			<form method="post" action="/api/">
				<input type="hidden" id="userId" name="userId" value="<?php echo $userId; ?>" />
				<input type="hidden" id="module" name="module" value="payment" />
				<button type="button" class="btn btn-primary donate">Donate now</button>
				<button type="button" class="btn btn-primary buckshee">For free</button>
			</form>
		</div>
	</div>
</script>
		
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.scattercdn.com/file/scatter-cdn/js/latest/scatterjs-core.min.js"></script>
<script src="https://cdn.scattercdn.com/file/scatter-cdn/js/latest/scatterjs-plugin-eosjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/eosjs@16.0.9/lib/eos.min.js"></script>	
<script type="text/javascript">

function callPreview(itemId, fileIds) {
	var formData = new FormData();
    formData.append('module', 'preview');
    formData.append('fileIds', fileIds.join(","));
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	if ((fileIds.join(",")).length > 0) {
		requestPost(data, function(obj) {
			var previewtpl = $("#preview").text();
			var items = {
				itemId : itemId,
				canvasName: obj.canvas,
				fee: obj.fee,
				currency: obj.currency,
				fileIds : obj.fileIds.join(","),
				alt : obj.canvas,
				title : obj.canvas,
				src: obj.imageData
			};

			$(".card-columns").prepend(render(previewtpl, items));
		});
	}
}

function callPayment(userId) {
	var formData = new FormData();
    formData.append('module', 'payment');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	requestGet(data, function(obj) {
		var paymenttpl = $("#payment").text();
		var items = {
			counter : obj.counter,
			price: getPriceList(obj.price).join(", ")
		};

		$(".card-columns").append(render(paymenttpl, items));
		
		callBasket(userId);
	});
}

function getPriceList(obj) {
	var price = new Array();
	
	$.each(obj, function(key, value) {
		price.push("(" + value.fee + " " + value.currency + ")");
	});
	
	return price;
}

function callBasket(userId) {
	var formData = new FormData();
    formData.append('module', 'basket');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var counter = obj.basket.length;
		
		$(".sidebar-nav span.basket").html(counter);
		
		$.each(obj.basket, function(key, value) {
			callPreview(value.id, value.fileIds);
		});
	});
}

function deleteBasket(userId, basketId) {
	var formData = new FormData();
    formData.append('module', 'basket');
    formData.append('userId', userId);
	formData.append('basketId', basketId);
	formData.append('NGINX', 'DELETE');
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestPost(data, function(obj) {
		var counter = obj.basket.length;
		
		$(".sidebar-nav span.basket").html(counter);
		$(".card-columns").find("[data-itemId='" + basketId + "']").remove();
		
		updatePayment(userId);
	});	
}

function updatePayment(userId) {
	var formData = new FormData();
    formData.append('module', 'payment');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));

	requestGet(data, function(obj) {
		var paymenttpl = $("#payment").text();
		var items = {
			counter : obj.counter,
			price: getPriceList(obj.price).join(", ")
		};

		$(".card-columns .payment").replaceWith(render(paymenttpl, items));
	});
}

function updateWishlist(userId) {
	var formData = new FormData();
    formData.append('module', 'wishlist');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var counter = obj.wishlist.length;
		
		$(".sidebar-nav span.wishlist").html(counter);
	});
}

function updateAvatar(userId) {
	var formData = new FormData();
    formData.append('module', 'avatar');
    formData.append('userId', userId);
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestGet(data, function(obj) {
		var counter = obj.avatar.length;
		
		$(".sidebar-nav span.avatar").html(counter);
	});
}

function proceedPayment(userId, basketId) {
	var formData = new FormData();
    formData.append('module', 'payment');
    formData.append('userId', userId);
	formData.append('basketId', basketId);
	formData.append('NGINX', 'PUT');
	
	var data = jQuery.parseJSON(JSON.stringify(Array.from(formData).reduce((o,[k,v])=>(o[k]=v,o),{})));
	
	requestPost(data, function(obj) {
		var counter = obj.counter;
		
		$(".sidebar-nav span.basket").html(counter);
		$(".card-columns").find("[data-itemId='" + basketId + "']").remove();
		
		updatePayment(userId);
		updateAvatar(userId);
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
	
	callPayment(<?php echo $userId; ?>);
	updateWishlist(<?php echo $userId; ?>);
	updateAvatar(<?php echo $userId; ?>);
});

$('div.card-columns').on('click', 'button.buckshee', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');
	var card = form.closest(".card");

	card.removeClass( "border-dark" ).addClass( "border-warning" );
	
	if (form.prop("method") == "post") {
		form.find("#module").val("payment");
		requestPost(form.serialize(), function(obj) {
			$.each(obj.items, function(key, item) {
				let basketId = item.basketId;
				let accountName = item.address; // 'designerAccount'
				let amount = item.amount; // '1.0000 EOS'
				let memo = item.memo; // 'avarkey canvas name';
						
				//console.log(basketId + " :: " + accountName + " :: " + amount + " :: " + memo);
				
				proceedPayment(<?php echo $userId; ?>, basketId);
			});
			
			card.removeClass( "border-warning" ).addClass( "border-success" );
		});
	}
});

$('div.card-columns').on('click', 'button.donate', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');
	var card = form.closest(".card");

	// Don't forget to tell ScatterJS which plugins you are using.
	ScatterJS.plugins( new ScatterEOS() );

	// Networks are used to reference certain blockchains.
	// They let you get accounts and help you build signature providers.
	const network = {
		blockchain:'eos',
		protocol:'https',
		host:'nodes.get-scatter.com',
		port:443,
		chainId:'aca376f206b8fc25a6ed44dbdc66547c36c6c33e3a119ffbeaef943642f0e906'
	}

	// First we need to connect to the user's Scatter.
	ScatterJS.scatter.connect('Avarkey').then(connected => {

		// If the user does not have Scatter or it is Locked or Closed this will return false;
		if(!connected) return false;

		const scatter = ScatterJS.scatter;

		// Now we need to get an identity from the user.
		// We're also going to require an account that is connected to the network we're using.
		const requiredFields = { accounts:[network] };
		scatter.getIdentity(requiredFields).then(() => {

			// Always use the accounts you got back from Scatter. Never hardcode them even if you are prompting
			// the user for their account name beforehand. They could still give you a different account.
			const account = scatter.identity.accounts.find(x => x.blockchain === 'eos');

			// You can pass in any additional options you want into the eosjs reference.
			const eosOptions = { expireInSeconds:60 };

			// Get a proxy reference to eosjs which you can use to sign transactions with a user's Scatter.
			const eos = scatter.eos(network, Eos, eosOptions);

			// ----------------------------
			// Now that we have an identity,
			// an EOSIO account, and a reference
			// to an eosjs object we can send a transaction.
			// ----------------------------


			// Never assume the account's permission/authority. Always take it from the returned account.
			const transactionOptions = { authorization:[`${account.name}@${account.authority}`] };

			card.removeClass( "border-dark" ).addClass( "border-warning" );
			
			if (form.prop("method") == "post") {
				form.find("#module").val("payment");
				requestPost(form.serialize(), function(obj) {
					$.each(obj.items, function(key, item) {
						let basketId = item.basketId;						
						let accountName = item.address; // 'designerAccount'
						let amount = item.amount; // '1.0000 EOS'
						let memo = item.memo; // 'avarkey canvas name';
						
						if (accountName != "") {
							eos.transfer(account.name, accountName, amount, memo, transactionOptions).then(trx => {
								// That's it!
								console.log(`Transaction ID: ${trx.transaction_id}`);
							
								proceedPayment(<?php echo $userId; ?>, basketId);
							}).catch(error => {
								console.error(error);
								
								proceedPayment(<?php echo $userId; ?>, basketId);
							});
						} else {
							proceedPayment(<?php echo $userId; ?>, basketId);
						}
					});
					
					card.removeClass( "border-warning" ).addClass( "border-success" );
				});
			}			
			
		}).catch(error => {
			// The user rejected this request, or doesn't have the appropriate requirements.
			console.error(error);
		});
	});
});

$('div.card-columns').on('click', 'button.wishlist', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var form = $(this).parents('form:first');

	if (form.prop("method") == "post") {
		form.find("#module").val("wishlist");
		requestPost(form.serialize(), function(obj) {
			var counter = obj.wishlist.length;
			
			$(".sidebar-nav span.wishlist").html(counter);
		});
	}
});

$('div.card-columns').on('click', 'button.remove', function() {
	event.preventDefault();
	event.stopPropagation();
	
	var card = $(this).closest("div.card.preview");
	var itemId = card.data('itemid');
	
	if (confirm('Are you sure deleting this item?')) {
		deleteBasket(<?php echo $userId; ?>, itemId);
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