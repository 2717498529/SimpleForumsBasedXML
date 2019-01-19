<?php
/*论坛杂项类
 * date：18-12-31
 */
require_once 'system.php';
class ForumMisc{
    public function upFile($tmp_name){
        //上传文件
        //得到FILEID增加计数
        $fileid=str_pad(ForumSystem::get_forum_files_num(),15,'0',STR_PAD_LEFT);
        ForumSystem::add_forum_files_num();
        //合法则上传
        if(is_uploaded_file($tmp_name)){
            //echo 0;
            //得到移动目标位置
            $moveto=ForumSystem::get_forum_config('DATA_HOME').'/files/'.substr($fileid,0,5).'/'.substr($fileid,5,5).'/'.substr($fileid,10,5).'.bin';
            //没有文件夹则创建
            if(!file_exists(ForumSystem::get_forum_config('DATA_HOME').'/files/'.substr($fileid,0,5))){
                mkdir(ForumSystem::get_forum_config('DATA_HOME').'/files/'.substr($fileid,0,5));
                if(!file_exists(ForumSystem::get_forum_config('DATA_HOME').'/files/'.substr($fileid,0,5).'/'.substr($fileid,5,5))){
                    mkdir(ForumSystem::get_forum_config('DATA_HOME').'/files/'.substr($fileid,0,5).'/'.substr($fileid,5,5));
                }
            }
            //移动文件
            if(move_uploaded_file($tmp_name,$moveto)){
                return $fileid;
            }
        }
        return false;
    }
}
?>