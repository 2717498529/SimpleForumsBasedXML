<?php
require_once 'constants.php';
class ForumSystem{
    /*论坛系统函数
     * 用于处理配置和状态
     */
    public static function get_forum_posters_num($area){
        //获取帖子数目
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r');
        fseek($fp,POSTER_NUM_LEN*($area)+POSTER_NUM_OFFSET);
        $num=fread($fp,POSTER_NUM_LEN);
        fclose($fp);
        return $area.ltrim($num,'0');
    }
    public static function add_forum_poster_num($area){
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r+');
        fseek($fp,POSTER_NUM_LEN*($area)+POSTER_NUM_OFFSET);
        $num=fread($fp,POSTER_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($num,POSTER_NUM_LEN,'0',STR_PAD_LEFT);
        fseek($fp,POSTER_NUM_LEN*($area)+POSTER_NUM_OFFSET);
        //echo $num.'/';
        fwrite($fp,$num);
        fclose($fp);
    }
    public static function get_forum_files_num(){
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r');
        fseek($fp,POSTER_FILE_NUM_OFFSET);
        $num=fread($fp,POSTER_FILE_NUM_LEN);
        fclose($fp);
        return ltrim($num,'0');
    }
    public static function add_forum_files_num(){
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r+');
        fseek($fp,POSTER_FILE_NUM_OFFSET);
        $num=fread($fp,POSTER_FILE_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($num,POSTER_FILE_NUM_LEN,'0',STR_PAD_LEFT);
        //echo $num.'/';
        fseek($fp,POSTER_FILE_NUM_OFFSET);
        fwrite($fp,$num);
        fclose($fp  );
    }
    public static function get_forum_users_num(){
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r');
        $num=fread($fp,USER_NUM_LEN);
        fclose($fp);
        return ltrim($num,'0');
        }
    public static function add_forum_users_num(){
        $fp=fopen(FORUM_DATA_HOME.'/stat.txt','r+');
        $num=fread($fp,USER_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($num,USER_NUM_LEN,'0',STR_PAD_LEFT);
        //echo $num.'/';
        fseek($fp,0);
        fwrite($fp,$num);
        fclose($fp          );
    }
}
?>