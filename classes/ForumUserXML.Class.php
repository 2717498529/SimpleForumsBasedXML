<?php
/*论坛用户XML类
 * 用于处理用户XML文件
 * date：18-12-31
 */
require_once 'constants.php';
require_once 'ForumSystem.Class.php';
class ForumUserXML{
    public $domdoc;
    public $userid;
    public $xmlfile;
    public $systeminfo;
    public function getXMLPath($userid,$format=FORUM_GET_XML_PATH_FULL){
        switch ($format){
            case 0:
                return ForumSystem::get_forum_config('DATA_HOME').'/users/';
            case 1:
                return ForumSystem::get_forum_config('DATA_HOME').'/users/'.substr($userid,USER_DIR_OFFSET,USER_DIR_LEN);
            case 2:
                return floor(substr($userid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN)/$this->systeminfo['USERS_PER_FILE']);
            case 3:
                return ForumSystem::get_forum_config('DATA_HOME').'/users/'.substr($userid,USER_DIR_OFFSET,USER_DIR_LEN).'/'.floor(substr($userid,USER_BASENAME_OFFSET,USER_BASENAME_LEN)/$this->systeminfo['USERS_PER_FILE']).'.xml';
            default:
                return false ;
        }
    }
    public function getNumberInFile($userid){
        //返回在文件中的序号
        return substr($userid,USER_BASENAME_OFFSET,USER_BASENAME_LEN)-floor(substr($userid,USER_BASENAME_OFFSET,USER_BASENAME_LEN)/$this->systeminfo['USERS_PER_FILE'])*$this->systeminfo['USERS_PER_FILE'];
        
    }
    public function __construct(){
        $this->domdoc=new DOMDocument;
        $this->systeminfo=ForumSystem::get_forum_config('ALL');
    }
    public function addUser($name,$pwd,$mail){
        $dom=&$this->domdoc;
        $posterid=&$this->userid;
        $xmlfile=&$this->xmlfile;
        $userid=str_pad(ForumSystem::get_forum_users_num(),USER_ID_LEN,'0',STR_PAD_LEFT);
        ForumSystem::add_forum_users_num();
        $xmlfile=$this->getXMLPath($userid);
        if(substr($userid,USER_BASENAME_OFFSET,USER_BASENAME_LEN)%$this->systeminfo['USERS_PER_FILE']==0){
            $root=$dom->createElement('root');
            $dom->appendChild($root);
            //如果文件夹不存在，则创建
            if(!file_exists($this->getXMLPath($userid,FORUM_GET_XML_PATH_DIR))){
                if(!mkdir($this->getXMLPath($userid,FORUM_GET_XML_PATH_DIR))){
                    return false;
                }
            }
            $dom->save($xmlfile);
            $dom->load($xmlfile);
            $root=$dom->getElementsByTagName('root')->item(0);
        }else{
            $dom->load($xmlfile);
            $root=$dom->getElementsByTagName('root')->item(0);
        }
        $user=$dom->createElement('u');
        $user->setAttribute('i',$this->getNumberInFile($userid));
        $user->setAttribute('n',$name);
        $user->setAttribute('p',md5($pwd));
        $user->setAttribute('m',$mail);
        $root->appendChild($user);
        $dom->save($xmlfile);
    }
    public function login($uid,$pwd){
        $dom=&$this->domdoc;
        $xmlfile=&$this->xmlfile;
        $uid=str_pad($uid,USER_ID_LEN,'0',STR_PAD_LEFT);
        $xmlfile=$this->getXMLPath($uid);
        $i=$this->getNumberInFile($uid);
        $dom->load($xmlfile);
        $xpath=new DOMXpath($dom);
        $user=$xpath->query("root/p[@i='".$i."']/r")->item(0);
        //var_dump($user);
        //写入数组并输出
        $pwd_in_file=$user->getAttribute('p');
        if(md5($pwd)==$pwd_in_file){
            session_start();
            $_SESSION['uid']=$uid;
            $this->userid=$uid;
            return true;
        }else{
            return false;
        }
        
    }
}
?>