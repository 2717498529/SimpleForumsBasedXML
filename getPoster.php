<?php
date_default_timezone_set('Asia/Shanghai');
require_once 'core/ForumPosterXML.Class.php';
require_once 'core/ForumUserXML.Class.php';
$post=new ForumPosterXML;
$user=new ForumUserXML;
if(!$post->posterExists($_GET['i'])){
    echo '找不到帖子';
    die();
}
$post->load($_GET['i']);
$thread=$post->getThread();
$reply=$post->getReply();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $thread['title'];?></title>
    </head>
    <body>
        <table width="60%" border="3">
            <tr><th colspan="2" scope="col"><span class="STYLE2"><?php echo $thread['title'];?></span></th></tr>
            <?php
            $author=$user->getUser($thread['author']);
            echo "<tr><td width='15%'>1楼</br>".$author['name']."</br>".date('Y.m.d H:i:s',$thread['time'])."</br></td><td width='85%'>".$thread['body']."</br>";
            //echo "<a href='down.pgp?i=$i'>$filename</a> $sz</br></td>";
            echo "</tr>";
            foreach($reply as $i=>$reply){
                $floor=$i+2;
                $author=$user->getUser($reply['author']);
                echo "<tr><td width='15%'>".$floor."楼</br>".$author['name']."</br>".date('Y.m.d H:i:s',$reply['time'])."</br></td><td width='85%'>".$reply['body']."</br>";
                //echo "<a href='down.pgp?i=$i'>$filename</a> $sz</br></td>";
                echo "</tr>";
            }
            ?>
        </table>
        回复：</br>
        <form action="reply.php" method="post" enctype="multipart/form-data">
            <textarea name="reply" cols="80" rows="10" width="90%"></textarea></br>
            附件<input type="file" name="file"/>
            <input type="hidden" name="p" value="<?php echo $_GET['i'];?>" />
            </br><input type="submit" name="Submit" value="提交" /></br>
        </form>
    </body>
</html>
