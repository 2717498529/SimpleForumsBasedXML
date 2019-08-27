<?php
session_start();
date_default_timezone_set('Asia/Shanghai');
require_once 'core/ForumPosterXML.Class.php';
require_once 'core/ForumList.Class.php';
$post=new ForumPosterXML;
$list=new ForumList;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<table width="414" height="405" border="3">
  <tr>
    <td width="94" height="46">测试论坛Forums</td>
    <?php 
    if(!isset($_SESSION['uid'])){
        echo "<td width='307'><a href='login.html'>登录</a></td><td width='156'><a href='register.html'>注册</a></td>";
    }else{
        echo "<td width='307'><a href='post.html'>发帖</a></td>";
    }
    ?>
  </tr>
  <tr>
    <td height="292" colspan="3"><p><img src="190128125940.png" width="569" height="314" /></p>
    <p>该论坛项目开始于2018年9月，目的是让广大小型企业和个人一最低的成本搭建简洁实用的网上交流平台。相比于Discuz!等论坛，该系统有节省服务器资源和便于二次开发等特点。</p></td>
  </tr>
  <tr>
    <td>最新动态</td>
    <td>时间</td>
  </tr>
  <?php 
  $posters=$list->getNewestPosters(0);
  //print_r($posters);
  if($list!==false){
    foreach($posters as $posterid){
        $post->load($posterid);
        $thread=$post->getThread();
        echo "<tr><td><a href='getPoster.php?i=".$posterid."'>".$thread['title'].'</a></td><td>'.date('Y.m.d H:i:s',$thread['time']).'</td></tr>';
    }
  }
   ?>
</table>
</body>
</html>
