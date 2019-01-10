<?php
/*论坛用户XML类
 * 用于处理用户XML文件
 * date：18-12-31
 */
require_once 'system.php';
class ForumUserXML{
    public $domdoc;
    public $userid;
    public $xmlfile;
    public $systeminfo;
    public function getXMLPath($userid,$format='full'){
        switch ($format){
            case 'home':
                return get_forum_config('DATA_HOME').'/users/';
            case 'dir':
                return get_forum_config('DATA_HOME').'/users/'.substr($posterid,0,5);
            case 'basename':
                return floor(substr($posterid,5,5)/$this->systeminfo['USERS_PER_FILE']);
            case 'full':
                return get_forum_config('DATA_HOME').'/users/'.substr($posterid,0,5).'/'.floor(substr($posterid,5,5)/$this->systeminfo['USERS_PER_FILE']).'.xml';
            default:
                return false ;
        }
    }
    public function __construct(){
        $this->domdoc=new DOMDocument;
        $this->systeminfo=get_forum_config('ALL');
    }
    public function addUser($name,$pwd,$mail){
        $dom=&$this->domdoc;
        $posterid=&$this->posterid;
        $xmlfile=&$this->xmlfile;
        $posterid=str_pad(get_forum_posters_num(),12,'0',STR_PAD_LEFT);
        add_forum_poster_num($area);
        $xmlfile=$this->getXMLPath($userid);
        if(is_int(substr($userid,5,5)/$this->systeminfo['USERS_PER_FILE'])){
            $root=$dom->createElement('root');
            $dom->appendChild($root);
            //如果文件夹不存在，则创建
            if(!file_exists($this->getXMLPath($posterid,'dir'))){
                if(!mkdir($this->getXMLPath($posterid,'dir'))){
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
        $user->setAttribute('i',substr($userid,6,4)-floor(substr($userid,6,4)/$this->systeminfo['USERS_PER_FILE'])*$this->systeminfo['USERS_PER_FILE']);
        $user->setAttribute('n',$name);
        $user->setAttribute('p',$pwd);
        $user->setAttribute('m',$mail);
        $root->appendChild($user);
        $dom->save($xmlfile);
    }
    public function login($uid,$pwd){
        $dom=&$this->domdoc;
        $posterid=&$this->posterid;
        $xmlfile=&$this->xmlfile;
        $xmlfile=$this->getXMLPath($uid);
        $i=substr($userid,6,4)-floor(substr($userid,6,4)/$this->systeminfo['USERS_PER_FILE'])*$this->systeminfo['USERS_PER_FILE'];
        $dom->load($xmlfile);
        $reply=$xpath->query("root/p[@i='".$i."']/r")->item($replyi);
        //写入数组并输出
        $uid=$reply->getAttribute('');
        $reply_to_ret['time']=$reply->getAttribute('a');
        $reply_to_ret['body']=$reply->nodeValue;
        
    }
}
?>