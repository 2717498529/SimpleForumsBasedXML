<html>
    <head>
        <meta charset="UTF-8">
        <title>登录论坛</title>
    </head>
    <body>
        <?php
        if(!isset($_POST['u'])){
            die('Bad Requiring');
        }
        require_once 'core/ForumUserXML.Class.php';
        $u=new ForumUserXML;
        if($u->login($_POST['u'],$_POST['p'])){
            echo '登陆'.$_SESSION['uid'].'成功';
        }else{
            echo '用户名密码错误';
        }
        //echo 0;
        ?>
    </body>
</html>
