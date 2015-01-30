<?php

$file	= $_GET['file'].'.jpg';
$f	= 'images/meme/'.$file;

header('Content-disposition: attachment; filename='.$file.'');
header('Content-type: image/jpeg');
readfile($f);
?> 