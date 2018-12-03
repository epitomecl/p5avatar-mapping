# Avatar Layer Generation Tool

Web interface for user interaction between homepage and backend.

call http://13.209.194.1:5000/api/

Current module available:

- [ ] APIKEY
- [x] AVATAR
- [ ] CATEGORY
- [x] CLOSE
- [ ] CREATE
- [x] CURRENCY
- [ ] DESIGNER
- [ ] HASHTAG
- [ ] LAYER
- [x] LOGIN
- [x] LOGOUT
- [ ] PRICE
- [ ] PROFILE
- [x] ROLE
- [ ] SIGNUP
- [x] TITLE
- [ ] UPLOAD


## APIKEY

The input data (First Name, Last Name, Email, How will you use the APIs (optional)) are stored into database. 
To given eMail address an random link will be send. If eMail has wrong spelling, success will be false. 
The user has to check also spam folder. Inside the email is a link with access code. 
The link is 30 min valid. After 30 min old invalid users will be delete. 
After confirming the link an apikey is respond.

Input: 
```
{module : "apikey", firstName : firstName, lastName : lastName, eMail : eMail, message : message}
```

Output: 
```
{"success":true}
```

## CLOSE

Delete user account, but keep created avatars.

Input: 
```
{module : "close", ssID : ssId, userId : userId}
```

Output: 
```
{"success":true}
```

## CURRENCY

Give a overview of current currencies and by avarkey supported currencies.

Input:
```
{module : "currency"}
```

Output:
```
[{"release":2009,"currency":"Bitcoin","symbol":["BTC","XBT","₿"],"supported":false,"notes":"The first and most widely used decentralized ledger currency, with the highest market capitalization."},...]
```

## HASHTAG

If ssId is given and user session is alive, a set of hashtags, used by other designer, is responded.

Input:
```
{module : "hashtag", ssID : ssId}
```

Output: 
```
array of favorite hashtags
```

## LOGIN

User can login with their identify and password. A user role can be choosen. If user exist, choosen roleId returned, too.

Input:
```
{module : "login", identify : identify, password : password}
```

Output:
```
{"ssId":"smekcau1sopsc38i4aot0l9iil","userId":1,"roleId:1}
```

## LOGOUT

User session will be closed.

Input:
```
{module : "logout", ssID : ssId, userId : userId}
```

Output: 
```
{"success":true}
```

## PROFILE

If ssId is given and user session is alive, update profile data.

Input:
```
{module : "profile", ssID : ssId, firstName : firstName,
lastName : lastName, stageName : stageName, eMail : eMail,
imageData : imageData}
```

Output: 
```
{"success":true}
```

## TITLE

Give more free styled names for avatar or stage name. Each call response with 10 random names.

Input:
```
{module : "title"}
```

Output:
```
["Lucky Magpie","Distinct Lemur","Clear Boar","Nervous Corncrake","Stupid Mandrill","Obnoxious Peccary","Lovely Tapir","Magnificent Pygmy","Eager Oystercatcher","Helpless Tamarin"]
```

## SIGNUP

To given eMail address an random link will be send. 
If eMail has wrong spelling, success will be false. 
The user has to check also spam folder. 
Inside the email is a link with access code. The link is 30 min valid. 
The user can set a new password. After 30 min old invalid users will be ready for deleting.

Input:
```
{module : "signup", identity : identity, eMail : eMail, roleId : roleId}
```

Output: 
```
{"success":true}
```

## UPLOAD

User can upload multiple files. File is the browser file object. 
LayerId directed to the layer name for combination of layer name and current number of layers as file name.
DivId is the wrapping divider for the image in html template. Unlink is the file id for deleting unused file.
If file is unlinked, it is not longer assigned.

Input:
```
{module : "upload", file : file, layerId : layerId, divId : divId, unlink : unlinkId }
```

Output:
```
[{pkId:1, divId:2, filename:"fur03.png" : assigned:true}]
```




