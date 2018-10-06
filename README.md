<img src="/docs/logo.png" alt="avatar generator"/>

# Avatar Generator

This sources take some params, hashes the given wallet address and generate an unique image. 
The output size of generated png image is 192 x 192 pixel. It is wrapped into an json object.

```
{
	"walletAddress" : "1BoatSLRHtKNngkdXEeobR76b53LETtpyT",
	"hashData" : "3893794d2870f0abbe0093dcbf6a9c6b48cf209cd1ab61064d887e72b4481694",
	"imageData" : "data:image\/png;base64,iVBOR... CYII=",
	"errorCode" : 0
}
```

## Getting Started

Copy folder 'api' in your web space on your web server and copy in all sources.

### Prerequisites

Your web server should be able to execute PHP scripts. On your local machine you can install XAMPP for instance.

```
Install xampp
```

### Installing

Copy folder 'api' into folder for example '\htdocs'.

```
\htdocs\api
```

### Using

Call '\api\' and send suitable post params like 'walletaddress' and 'module'.

```html
<p>url : http://localhost/api/index.php</p>

<ul><li>post variable :
	<ul>
		<li>"module" : "AvatarGenerator"</li>
		<li>"walletAddress" : "1BoatSLRHtKNngkdXEeobR76b53LETtpyT"</li>
	</ul>
	</li>
</ul>
```

Without any params an module test overview will be displayed. 