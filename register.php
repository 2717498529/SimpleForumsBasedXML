<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        if(!isset($_POST['u'])){
            die('Bad Requiring');
        }
        require_once 'core/ForumUserXML.Class.php';
        $u=new ForumUserXML;
        echo '注册成功，ID为'.$u->addUser($_POST['u'],$_POST['p'],$_POST['m']);
        ?>
    </body>
</html>
