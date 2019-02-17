# Avatar Layer Generation Tool

Web interface for user interaction between homepage and backend.

The layered generation tool provides interface between webapp and centralized/distributed data and avatar handling. 
The user can act in different roles. Admin is responsible for management, user is searching for avatars, designer 
creating avatars, supervisor monitoring compliance with the rules of creating avatars.

Guest are able to play with default avatar. They can enter their wallet address for request an suitable avatar (cat). 
If this avatar is used by user after internal check, than “used” marker can be show up.

User are able to choose from avatar selection ordered by designer or avatar. If user selected one avatar, 
this avatar is locked. Request for such kind of locked avatars leads into positive used response.

At the moment using an api key is not necessary. 
Therefore requesting for apikey is more for implement your own ideas based on this api.

### Steps for creating an account:

```
Call POST request SIGNUP 
Call GET request SIGNUP
```

### Steps for create an basic workplace with one category and 6 layers

```
Call POST request LOGIN
Call POST request CREATE
```

### Steps for show up and update own profile data:

```
Call POST request LOGIN
Call GET request PROFILE
Call POST request PROFILE
```

### Steps for upload and preview images as possible avatar

```
Call POST request UPLOAD
Call POST request PREVIEW
```

### Steps for buy avatar

```
Call POST request BOOKING
Call POST request PAYMENT
Call GET request PAYMENT
Call PUT request PAYMENT
```

## Call

Without any param settings an api test suite comes up. Here you can check all methods on several ways.

https://api.avarkey.com/api/


## Current available modules

|[x] ADDRESS	|[X] DESIGNER	|[x] PROFILE
|[x] ALIAS		|[X] HASHTAG	|[x] USERROLE
|[x] APIKEY		|[X] LAYER		|[x] SIGNUP
|[x] AVATAR		|[x] LOGIN		|[x] TITLE
|[X] BOOKING	|[x] LOGOUT		|[X] UPLOAD
|[X] CATEGORY	|[x] PASSWORD	|
|[x] CLOSE		|[X] PAYMENT	|
|[X] CREATE		|[x] PREVIEW	|
|[x] CURRENCY	|[X] PRICE		|[X] WHOISONLINE
		

## ADDRESS

If user session is alive, the user can update bonded address to his owned avatar (if available).
GET request list all available owned avatars.
		
POST Input: 
```
{module : "address", userId : userId, avatarId : avatarId, address : address}
```

POST Output: 
```
{"success":true}
```

GET Input: 
```
{module : "address", userId : userId}
```

GET Output: 
```
{"success":true}
```

## ALIAS

If user session is alive, the user can update his profile data 
with new an alias (as designer name, stage name or pseudonym).

POST Input: 
```
{module : "alias", userId : userId, alias : alias}
```

POST Output: 
```
{"success":true}
```

## APIKEY

The input data (given email, confirmation of data protection and terms of service) are stored into database. 
To given eMail address an random link will be send. If eMail has wrong spelling, success will be false. 
The user has to check also spam folder. Inside the email is a link with access code. 
The link is 15 min valid. After 15 min old invalid users will be delete. After confirming the link an apikey is available.

POST Input: 
```
{module : "apikey", email : email, password : password, password2 : password2, dataProtection : dataProtection, termsOfService : termsOfService}
```

POST Output: 
```
{"success":true}
```

GET Input: 
```
{module : "apikey", token : token}
```

GET Output: 
```
{"apikey":"46eb20c6feae6216dd5763e7956dcdf4e98c98883f198d01a4112541cd622670"}
```

## AVATAR

Build cute cat avatar based on default avatar by POST.
Lookup for an existing avatar owned by someone related to given address by GET.

POST Input: 
```
{module : "avatar", address : address}
```

POST Output: 
```
{"address":"1Fvxg6UX11zDLdcaQdWakbbvpv375CFoJq","sha256":"e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855","category":"cat","parts":{"background":1,"body":11,"fur":5,"eyes":8,"mouth":3,"accessorie":6},"imageData":"data:image\/png;base64,iVB..."}
```

GET Input: 
```
{module : "avatar", address : address}
```

GET Output: 
```
�PNG  IHDR�?1	pHYs���+�IDATx����N�0@�e���r�PE�k2 ...
```

## BOOKING

Preparation for building an own avatar based on selected files. 
The user selected from each layer one file with its file id.
For instance 2, 22, 35, 56, 68, 77.
Based on order of layer the avatar will be build after payment.
Over the booking process selected files will be hold. These files
are reserved and not selectable for an furter booking process.

POST Input: 
```
{module : "booking", userId : userId, fileIds : fileIds}
```

POST Output: 
```
{"success":true}
```

## CATEGORY

If user session is alive, user can update current category (called as name of avatar). 
Hashtags (comma separated) are describing the attributes for searching.

POST Input:
```
{module : "category", categoryId : categoryId, name: name, hashtag: hashtag}
```

POST Output: 
```
{"success":true}
```

## CLOSE

If user session is alive, user session will be closed.
All user profile data will be deleted. 
Used avatars created by profile owner are keeped on system.

POST Input: 
```
{module : "close", userId : userId}
```

POST Output: 
```
{"success":true}
```

## CREATE 

If user session is alive, a new category with a basic set of layers will be created.
The category will hold an random artifical name.
Each category has a selection of layers.
Layernames as default are background, body, fur, eye, mouth and accessorie.

POST Input: 
```
{module : "create", userId : userId}
```

POST Output: 
```
{"userId":"2","category":"Yellowed_Gharial","categoryId":13,"layer":[{"layerId":25,"layerName":"background","position":1},{"layerId":26,"layerName":"body","position":2},{"layerId":27,"layerName":"fur","position":3},{"layerId":28,"layerName":"eyes","position":4},{"layerId":29,"layerName":"mouth","position":5},{"layerId":30,"layerName":"accessorie","position":6}]}
```

## CURRENCY

If user session is alive, a list currency will be responded.
All currency properties hold an "supported" attribute.
Intern shortname (3 chars) of crypto currency will be used.

POST Input:
```
{module : "currency"}
```

POST Output:
```
[{"release":2009,"currency":"Bitcoin","symbol":["BTC","XBT","₿"],"supported":false,"notes":"The first and most widely used decentralized ledger currency, with the highest market capitalization."},...]
```

## HASHTAG

If user session is alive, a common set of hashtags, 
favors by other designer, will responded.

POST Input: 
```
{module : "hashtag", categoryId : categoryId}
```

POST Output: 
```
{"hashtags":["cute"]}
```

GET Input: 
```
{module : "hashtag", categoryId : categoryId}
```

GET Output: 
```
{"hashtags":["beauty","active","cute",""]}
```

## Layer

If user session is alive, user can update current layer name.
The position determind the order of each image layer. 
Lower position is similar to the bottom and higher position is related near to top.
PUT insert a new layer and POST update a layer.

POST Input: 
```
{module : "hashtag", categoryId : categoryId, name : name, position : position}
```

POST Output: 
```
{"success":true}
```

PUT Input: 
```
{module : "hashtag", categoryId : categoryId, name : name, position : position}
```

PUT Output: 
```
{"success":true}
```

## LOGIN

Gives access to backend service, open a session. 
If user session is alive, session id will renewed. 
Response id of current user.

POST Input:
```
{module : "login", email : email, password : password}
```

POST Output:
```
{"userId":1}
```

## LOGOUT

User session will be closed.

POST Input:
```
{module : "logout", userId : userId}
```

POST Output: 
```
{"success":true}
```

## PASSWORD

If user session is alive, password data can be managed inside a time slot of 15 minutes.
A POST request will send an email with an token to the email receiver.
A GET request will perform some action on client side (maybe show a password input form).
A PUT request will be handle current token and password.

POST Input:
```
{module : "password", email : email, firstName:firstName }
```

POST Output: 
```
{"success":true}
```

PUT Input:
```
{module : "password", userId : userId, token : token, password : password, password2:password2 }
```

PUT Output: 
```
{"success":true}
```

GET Input:
```
{module : "password", token : token }
```

GET Output: 
```
{"success":true}
```

## PAYMENT 

User goes through the payment process.
For instance he shopping the file ids 2, 22, 35, 56, 68, 77.
Based on order of layer the avatar will be build after paying.
Over the booking process selected files by this user will be marked now as owned by user. 
These files are not selectable for an furter preview or booking process.
GET gives an overview about pending processes.
POST starts payment process.
PUT confirm payment process.
DEL deleted the current booking data in case of abort by user.

POST Input:
```
{module : "payment", userId : userId, fileIds : fileIds }
```

POST Output: 
```
{"success":true}
```

PUT Input:
```
{module : "payment", userId : userId, fileIds : fileIds, address : address }
```

PUT Output: 
```
{"success":true}
```

GET Input:
```
{module : "payment", userId : userId, fileIds : fileIds }
```

GET Output: 
```
[{"fileId":22,"ownerId":1,"pending":1}, {"fileId":77,"ownerId":1,"pending":0}, ...]
```

DEL Input:
```
{module : "payment", userId : userId, fileIds : fileIds }
```

DEL Output: 
```
{"success":true}
```

## PREVIEW

Build preview of avatar based on selected files. 
The user selected from each layer one file with its file id.
For instance 2, 22, 35, 56, 68, 77.
Based on order of layer the avatar will be build.
Avatar in use will be marked.

Input:
```
{module : "preview", fileIds : fileIds }
```

Output: 
```
{"address":"1Fvxg6UX11zDLdcaQdWakbbvpv375CFoJq","sha256":"e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855","category":"cat","parts":{"background":1,"body":11,"fur":5,"eyes":8,"mouth":3,"accessorie":6},"imageData":"data:image\/png;base64,iVB..."}
```

## PRICE

If user session is alive, designer can update price and currency for current layer.

POST Input:
```
{module : "price", fileId : fileId, price : price, currency : currency }
```

POST Output: 
```
{"success":true}
```

## PROFILE

If user session is alive, profile data will be managed.
A POST request will update profile data.
A GET request will respond current profile data.
An existing profile picture will independently from there original 
type always as base64 encoded png file image source delivered.

POST Input:
```
{module : "profile", userId : userId, firstName : firstName, lastName : lastName, alias : alias, email : email, file : file, imageData : imageData}
```

POST Output: 
```
{"success":true}
```

GET Input:
```
{module : "profile", userId : userId}
```

GET Output: 
```
{"firstName":"Marian","lastName":"Kulisch","alias":"Schlaraffenland","email":"s681562@gmail.com","imageData":"data:image\/png;base64,iVBORw0KGgoAAAAN.."}
```

## SIGNUP
To given eMail address an random link will be send. If eMail has wrong spelling, success will be false. 
The user has to check also spam folder. Inside the email is a link with access code. 
The link is 15 min valid. The user can set a new password. After 15 min old invalid users will be delete.

POST Input: 
```
{module : "signup", email : email, password : password, password2 : password2, dataProtection : dataProtection, termsOfService : termsOfService}
```

POST Output: 
```
{"success":true}
```

GET Input: 
```
{module : "signup", token : token}
```

GET Output: 
```
{"success":true}
```

## TITLE 

If user session is alive, 10 random proposals of an titel, 
label or description for category or layer will be generated. 

POST Input: 
```
{module : "title"}
```

POST Output: 
```
["Brave Octopus","Confused Crane","Selfish Antelope","Wide-eyed Peacock","Weary Kookaburra","Confused Bison","Xenophobic Llama","Yellowed Koala","Confused Gentoo","Xenophobic Yak"]
```

## UPLOAD

If user session is alive, files will be uploaded and transformed into png.
User can upload multiple files. File is the browser file object. 
LayerId directed to the layer name for combination of layer name and current number of layers as file name.
DivId is the wrapping divider for the image in html template. Unlink is the file id for deleting unused file.
If file is unlinked, it is not longer assigned.

POST Input:
```
{module : "upload", file : file, layerId : layerId, divId : divId, unlink : unlink }
```

POST Output:
```
[{"divId":1, "fileId":222, "fileName":"93B31277490D8E2C1E85D77EF4B516E7.png", "original":"fur03.png" : "assigned":true}]
```

## USERROLE

For user profile we need a role selection. Possible roles are given by backend service. 
If user session is alive, current roleId is responsed. 
The roleId is specified as index of given array. 
Possible roles are (1 : "USER", 2 : "DESIGNER", 3 : "SUPERVISOR", 4: "DEVELOPER", 5 : "ADMIN").
A user can act in different roles.

POST Input:
```
{module : "userrole"}
```

POST Output: 
```
[{"id":1,"name":"USER"},{"id":2,"name":"DESIGNER"},{"id":3,"name":"SUPERVISOR"},{"id":4,"name":"DEVELOPER"},{"id":5,"name":"ADMIN"}]
```

GET Input:
```
{module : "userrole", userId : userId}
```

GET Output: 
```
[{"id":1,"name":"USER"},{"id":2,"name":"DESIGNER"},{"id":4,"name":"DEVELOPER"}]
```

## WHOISONLINE

Storing active session id in database and updating table data. 
Inform about counter of current online users and used last possible IP address.

POST Input:
```
{module : "whoisonline"}
```

POST Output: 
```
{"counter":15770,"ip":"61.98.24.84"}
```

