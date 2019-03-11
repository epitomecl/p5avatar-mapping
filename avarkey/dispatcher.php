<?php
echo "<h1>Served Index file</h1>";
/** Get Server Path
* For http://localhost:5000/user/login, We get
* /user/login
**/
echo "Server Path </br>";
$path= $_SERVER['PATH_INFO'];
print_r($path);
// Then we split the path to get the corresponding controller and method to work with
echo "<br/><br/>Path Split<br/>";
print_r(explode('/', ltrim($path)));
/** Then we have our controller name has [1]
* method name as [2]
**/
// We now need to match controllers and methods
?>