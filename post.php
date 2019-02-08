<?php
session_start();
header('Content-Type:text/html; charset=utf-8');
if(!isset($_POST['p'])){
    die('Bad Requiring');
}
if(!isset($_POST['t'])){
    die('Bad Requiring');
}
if(!isset($_SESSION['uid'])){
    die('未登录');
}
require_once 'core/ForumPosterXML.Class.php';
$poster=new ForumPosterXML;
$post=htmlspecialchars($_POST['p'],ENT_HTML401|ENT_QUOTES);
$post=str_replace(chr(0xd).chr(0xa),'</br>',$post);
$post=str_replace(chr(0xa),'</br>',$post);
$post=str_replace(chr(0xd),'</br>',$post);
$title=htmlspecialchars($_POST['t'],ENT_HTML401|ENT_QUOTES);
$title=str_replace(chr(0xd).chr(0xa),'</br>',$title);
$title=str_replace(chr(0xa),'</br>',$title);
$title=str_replace(chr(0xd),'</br>',$title);
if($poster->addPoster($post,$title,0,$_FILES)){
    echo "回帖成功";
}else{
    echo '失败';
}
?>

