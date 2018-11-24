# Avatar Layer Generation Tool

Web interface for user interaction between homepage and backend.

call http://13.209.194.1:5000/api/

Current module available: 

[x]AVATAR
[ ]CATEGORY
[x]CLOSE
[ ]CREATE
[x]CURRENCY
[ ]DESIGNER
[ ]LAYER
[x]LOGIN
[x]LOGOUT
[ ]PRICE
[x]ROLE
[x]TITLE
[ ]UPLOAD

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

## LOGIN

User can login with their identify and password. A user role can be choosen. If user exist, choosen roleId returned, too.

Input:
```
{module : "login", roleId : roleId}
```

Output:
```
{"ssId":"smekcau1sopsc38i4aot0l9iil","userId":1}
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





