# Avatar Generator

This sources take some params, hashes the given wallet address and generate an unique image.

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

```
url : http://localhost/api/index.php

<ul><li>post variable :
	<ul>
		<li>"module" : "AvatarGenerator"</li>
		<li>"walletAddress" : "1BoatSLRHtKNngkdXEeobR76b53LETtpyT"</li>
	</ul>
	</li>
</ul>
```

Without any params an module test overview will be displayed. 