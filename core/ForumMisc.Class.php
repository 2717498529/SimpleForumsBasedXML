<?php
/*论坛杂项类
 * date：18-12-31
 */
require_once 'ForumSystem.Class.php';
require_once 'constants.php';
class ForumMisc{
    public static function upFile($tmp_name){
        //上传文件
        //得到FILEID增加计数
        $fileid=str_pad(ForumSystem::get_forum_files_num(),POSTER_FILE_ID_LEN,'0',STR_PAD_LEFT);
        ForumSystem::add_forum_files_num();
        //合法则上传
        if(is_uploaded_file($tmp_name)){
            //echo 0;
            //得到移动目标位置
            $moveto=FORUM_DATA_HOME.'/files/'.substr($fileid,POSTER_FILE_FIRST_DIR_OFFSET,POSTER_FILE_FIRST_DIR_LEN).'/'.substr($fileid,POSTER_FILE_SECOND_DIR_OFFSET,POSTER_FILE_SECOND_DIR_LEN).'/'.substr($fileid,POSTER_FILE_BASENAME_OFFSET,POSTER_FILE_BASENAME_LEN).'.bin';
            //没有文件夹则创建
            if(!file_exists(FORUM_DATA_HOME.'/files/'.substr($fileid,POSTER_FILE_FIRST_DIR_OFFSET,POSTER_FILE_FIRST_DIR_LEN))){
                mkdir(FORUM_DATA_HOME.'/files/'.substr($fileid,POSTER_FILE_FIRST_DIR_OFFSET,POSTER_FILE_FIRST_DIR_LEN));
                if(!file_exists(FORUM_DATA_HOME.'/files/'.substr($fileid,POSTER_FILE_FIRST_DIR_OFFSET,POSTER_FILE_FIRST_DIR_LEN).'/'.substr($fileid,POSTER_FILE_SECOND_DIR_OFFSET,POSTER_FILE_SECOND_DIR_LEN))){
                    mkdir(FORUM_DATA_HOME.'/files/'.substr($fileid,POSTER_FILE_FIRST_DIR_OFFSET,POSTER_FILE_FIRST_DIR_LEN).'/'.substr($fileid,POSTER_FILE_SECOND_DIR_OFFSET,POSTER_FILE_SECOND_DIR_LEN));
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