<?php
/*论坛列表类
 * Date:19/01/25
 */
require_once 'constants.php';
require_once 'ForumPosterXML.Class.php';
require_once 'ForumUserXML.Class.php';
require_once 'ForumSystem.Class.php';
class ForumList{
    public $post;
    public $user;
    public $list=array();
    public function getNewestPosters($area,$num=FORUM_NEWEST_POSTERS_NUM){
        $posternum=ForumSystem::get_forum_posters_num($area);
        $i=0;
        $checked=0;
        while($i<=$num){
            $posterid=$area.str_pad($posternum-$checked,12,'0',STR_PAD_LEFT);
            if(ForumPosterXML::posterExists($posterid)){
                $this->list[]=$posterid;
                $i++;
            }
            $checked++;
        }
        return $this->list;
    }
    public function searchPosterByTitle($key,$from){
        
    }
}
?>
