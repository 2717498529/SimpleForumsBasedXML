<?php
session_start();
header('Content-Type:text/html; charset=utf-8');
if(!isset($_POST['p'])){
    die('Bad Requiring');
}
if(!isset($_POST['reply'])){
    die('Bad Requiring');
}
if(!isset($_SESSION['uid'])){
    die('未登录');
}
require_once 'core/ForumPosterXML.Class.php';
$post=new ForumPosterXML;
$reply=htmlspecialchars($_POST['reply'],ENT_HTML401|ENT_QUOTES);
$reply=str_replace(chr(0xd).chr(0xa),'</br>',$reply);
$reply=str_replace(chr(0xa),'</br>',$reply);
$reply=str_replace(chr(0xd),'</br>',$reply);
$post->load($_POST['p']);
if($post->addReply($reply,$_FILES)){
    echo "回帖成功";
}else{
    echo '失败';
}
?>