<?php
/*论坛系统函数
 * 用于处理配置和状态
 */
function get_forum_config($type){
    //获取配置
    switch($type){
        case 'ALL':
            return array('POSTERS_PER_FILE'=>10,'USERS_PER_FILE'=>100);
        case 'DATA_HOME':
            return 'C:\Program Files (x86)\Apache Software Foundation\Apache2.2\htdocs\forums\data';
    }
}
function get_forum_posters_num($area){
    //获取帖子数目
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r');
    fseek($fp,12*($area)+25);
    $num=fread($fp,12);
    fclose($fp);
    return $area.ltrim($num,'0');
}
function add_forum_poster_num($area){
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r+');
    fseek($fp,12*($area)+25);
    $num=fread($fp,12);
    //echo $num.'/';
    $num=ltrim($num,'0');
    //echo $num.'/';
    $num++;
    //echo $num.'/';
    $num=str_pad($num,12,'0',STR_PAD_LEFT);
    fseek($fp,12*($area)+25);
    //echo $num.'/';
    fwrite($fp,$num);
    fclose($fp);
}
function get_forum_files_num(){
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r');
    fseek($fp,10);
    $num=fread($fp,15);
    fclose($fp);
    return ltrim($num,'0');
}
function add_forum_files_num(){
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r+');
    fseek($fp,10);
    $num=fread($fp,15);
    //echo $num.'/';
    $num=ltrim($num,'0');
    //echo $num.'/';
    $num++;
    //echo $num.'/';
    $num=str_pad($num,15,'0',STR_PAD_LEFT);
    //echo $num.'/';
    fseek($fp,10);
    fwrite($fp,$num);
    fclose($fp);
}
function get_forum_users_num(){
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r');
    $num=fread($fp,10);
    fclose($fp);
    return ltrim($num,'0');
}
function add_forum_users_num(){
    $fp=fopen(get_forum_config('DATA_HOME').'/stat.txt','r+');
    $num=fread($fp,10);
    //echo $num.'/';
    $num=ltrim($num,'0');
    //echo $num.'/';
    $num++;
    //echo $num.'/';
    $num=str_pad($num,10,'0',STR_PAD_LEFT);
    //echo $num.'/';
    fseek($fp,0);
    fwrite($fp,$num);
    fclose($fp);
}
?>