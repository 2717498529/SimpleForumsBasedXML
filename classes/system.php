<?php
class ForumSystem{
    /*论坛系统函数
     * 用于处理配置和状态
     */
    const POSTER_NUM_LEN=12;
    const USER_NUM_LEN=10;
    const POSTER_FILE_NUM_LEN=15;
    const POSTER_NUM_OFFSET=25;         //
    const USER_NUM_OFFSET=0;            //
    const POSTER_FILE_NUM_OFFSET=10;    //
    public static function get_forum_config($type){
        //获取配置
        switch($type){
            case 'ALL':
                return array('POSTERS_PER_FILE'=>10,'USERS_PER_FILE'=>100);
            case 'DATA_HOME':
                return 'C:\Program Files (x86)\Apache Software Foundation\Apache2.2\htdocs\forums\data';
        }
    }
    public static function get_forum_posters_num($area){
        //获取帖子数目
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r');
        fseek($fp,self::POSTER_NUM_LEN*($area)+self::POSTER_NUM_OFFSET);
        $num=fread($fp,self::POSTER_NUM_LEN);
        fclose($fp);
        return $area.ltrim($num,'0');
    }
    public static function add_forum_poster_num($area){
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r+');
        fseek($fp,self::POSTER_NUM_LEN*($area)+self::POSTER_NUM_OFFSET);
        $num=fread($fp,self::POSTER_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($num,self::POSTER_NUM_LEN,'0',STR_PAD_LEFT);
        fseek($fp,self::POSTER_NUM_LEN*($area)+self::POSTER_NUM_OFFSET);
        //echo $num.'/';
        fwrite($fp,$num);
        fclose($fp);
    }
    public static function get_forum_files_num(){
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r');
        fseek($fp,self::POSTER_FILE_NUM_OFFSET);
        $num=fread($fp,self::POSTER_FILE_NUM_LEN);
        fclose($fp);
        return ltrim($num,'0');
    }
    public static function add_forum_files_num(){
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r+');
        fseek($fp,self::POSTER_FILE_NUM_OFFSET);
        $num=fread($fp,self::POSTER_FILE_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($num,self::POSTER_FILE_NUM_LEN,'0',STR_PAD_LEFT);
        //echo $num.'/';
        fseek($fp,self::POSTER_FILE_NUM_OFFSET);
        fwrite($fp,$num);
        fclose($fp  );
    }
    public static function get_forum_users_num(){
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r');
        $num=fread($fp,self::USER_NUM_LEN);
        fclose($fp);
        return ltrim($num,'0');
        }
    public static function add_forum_users_num(){
        $fp=fopen(self::get_forum_config('DATA_HOME').'/stat.txt','r+');
        $num=fread($fp,self::USER_NUM_LEN);
        //echo $num.'/';
        $num=ltrim($num,'0');
        //echo $num.'/';
        $num++;
        //echo $num.'/';
        $num=str_pad($numself::USER_NUM_LEN,'0',STR_PAD_LEFT);
        //echo $num.'/';
        fseek($fp,0);
        fwrite($fp,$num);
        fclose($fp          );
    }
}
?>