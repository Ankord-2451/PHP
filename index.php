<?php
$dir = "image/";
$files = scandir($dir);
?>

<!DOCTYPE html>
<html lang="ru">
<head>

<meta charset="UTF-8">
<title>Cats Gallery</title>

<style>

body{
font-family: Arial;
background:#e9e9e9;
}

.container{
width:800px;
margin:auto;
background:white;
padding:15px;
border:1px solid #ccc;
}

.menu{
border:1px solid #aaa;
padding:8px;
margin-bottom:15px;
}

.menu a{
text-decoration:none;
color:black;
margin-right:10px;
}

.title{
font-weight:bold;
margin-top:10px;
}

.subtitle{
color:gray;
font-size:12px;
margin-bottom:15px;
}

.gallery{
display:grid;
grid-template-columns: repeat(3, 1fr);
gap:15px;
}

.gallery img{
width:100%;
border-radius:6px;
border:1px solid #ccc;
}

footer{
text-align:center;
margin-top:20px;
font-size:14px;
}

</style>

</head>

<body>

<div class="container">

<div class="menu">
<a href="#">About</a> |
<a href="#">News</a> |
<a href="#">Contacts</a>
</div>

<div class="title">#screen</div>
<div class="subtitle">Explore a world of cats</div>

<div class="gallery">

<?php

if ($files !== false){

for($i=0;$i<count($files);$i++){

if($files[$i]!="." && $files[$i]!=".."){

$path=$dir.$files[$i];

echo "<img src='$path'>";
}

}

}

?>

</div>

<footer>
USM © 2024
</footer>

</div>

</body>
</html>