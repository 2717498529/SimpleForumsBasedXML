<?php
/*ForumPosterXML类
 * 用于处理帖子XML文件
 * Date：18-12-30
 */
require_once 'system.php';
require_once 'ForumMisc.Class.php';
class ForumPosterXML{
    const POSTER_ID_LEN=13;
    const POSTER_AREA_LEN=1;
    const POSTER_DIR_LEN=6;
    const POSTER_BASENAME_LEN=6;
    const POSTER__LEN=6;
    public $domdoc;
    public $posterid;
    public $xmlfile;
    public $systeminfo;
    public function __construct($posterid=null){
        $this->domdoc=new DOMDocument;
        $this->systeminfo=ForumSystem::get_forum_config('ALL');
        if(isset($posterid)){
            /* POSTERID的第1位是所属分区编号，2~13位为帖子在分区中的编号。
             * 其中，为避免在单个文件夹中存储太多的文件，2~7位作为所属的文
             * 件夹名，8~13位除以每个文件中的帖子数的舍去法取整值每个作为
             * 所属文件的基本名。
             */
            $this->load($posterid);
        }
        
    }
    public function load($posterid){
        $this->xmlfile=$this->getXMLPath($posterid);
        $this->domdoc->load($this->xmlfile);
    }
    public function getXMLPath($posterid,$format='full'){
        switch ($format){
            case 'home':
                return ForumSystem::get_forum_config('DATA_HOME').'/posters/'.substr($posterid,0,1);
            case 'dir':
                return ForumSystem::get_forum_config('DATA_HOME').'/posters/'.substr($posterid,0,1).'/'.substr($posterid,1,6);
            case 'basename':
                return floor(substr($posterid,7,6)/$this->systeminfo['POSTERS_PER_FILE']);
            case 'full':
                return ForumSystem::get_forum_config('DATA_HOME').'/posters/'.substr($posterid,0,1).'/'.substr($posterid,1,6).'/'.floor(substr($posterid,7,6)/$this->systeminfo['POSTERS_PER_FILE']).'.xml';
            default:
                return false ;
        }
    }
    public function getOrderNumberInFile($posterid){
        //返回帖子在文件中的序号
        return substr($posterid,7,6)-($this->getXMLPath($posterid,'basename'))*$this->systeminfo['POSTERS_PER_FILE'];
    }
    public function addPoster($poster,$title,$area,$files=null){
        //创建帖子
        //设置引用
        $dom=&$this->domdoc;
        $posterid=&$this->posterid;
        $xmlfile=&$this->xmlfile;
        //得到ID并增加计数
        $posterid=$area.str_pad(ForumSystem::get_forum_posters_num($area),12,'0',STR_PAD_LEFT);
        ForumSystem::add_forum_poster_num($area);
        //得到文件名
        $xmlfile=$this->getXMLPath($posterid);
        //如果最近的文件容量已满，则创建新文件
        if(is_int(substr($posterid,7,6)/$this->systeminfo['POSTERS_PER_FILE'])){
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
        //创建帖子节点
        $post=$dom->createElement('p');
        //设置帖子在文件中的编号
        $post->setAttribute('i',$this->getOrderNumberInFile($posterid));
        //创建主题帖节点
        $thread=$dom->createElement('t');
        //设置帖子属性
        $thread->setAttribute('t',time());//时间
        $thread->setAttribute('a',$_SESSION['UID']);//作者
        $thread->setAttribute('h',$title);//标题
        //设置帖子内容
        $thread->nodeValue=$poster;
        //echo 0;
        //创建和管理文件并上传文件
        if($files!==null){
            $post=$this->addFiles($files,1,$post);
        }
        //关联各个节点
        $post->appendChild($thread);
        $root->appendChild($post);
        //echo 0;
        //保存
        $dom->save($xmlfile);
        return true;
    }
    public function getThread(){
        //获取主题帖
        //初始化引用
        $dom=&$this->domdoc;
        //得到帖子序号
        $i=getOrderNumberInFile($posterid);
        //使用xpath搜索
        $xpath=new DOMXpath($this->domdoc);
        $threadElement=$xpath->query("root/p[@i='".$i."']/t")->item(0);
        //保存并返回结果
        $thread['body']=$threadElement->nodeValue;
        $thread['title']=$threadElement->getAttribute('h');
        $thread['author']=$threadElement->getAttribute('a');
        $thread['time']=$threadElement->getAttribute('t');
        return $thread;
    }
    public function addReply($reply,$files=null){
        //回帖
        //初始化引用
        $dom=&$this->domdoc;
        //得到帖子序号
        $i=$this->getOrderNumberInFile($this->posterid);
        //使用xpath搜索帖子
        $xpath=new DOMXpath($this->domdoc);
        $post=$xpath->query("root/p[@i='".$i."']")->item(0);
        //获取回帖序号
        $repid=$xpath->query("root/p[@i='".$i."']/r")->length;
        unset($xpath);
        //建立节点设置属性
        $reply=$dom->createElement('r');
        $reply->setAttribute('t',time());//时间
        $reply->setAttribute('a',$_SESSION['UID']);//作者
        $reply->setAttribute('i',$repid);//序号
        //设置内容
        $reply->nodeValue=$reply;
        //关联节点并保存
        $post->appendChild($reply);
        return;
        
    }
    public function getReply($replyi='all'){
        //获取回帖
        //初始化引用
        $dom=&$this->domdoc;
        //得到帖子序号
        $i=$this->getOrderNumberInFile($this->posterid);
        //创建Xpath对象
        $xpath=new DOMXpath($this->domdoc);
        if($i==all){
            //获取回帖NodeList
            $replies=$xpath->query("root/p[@i='".$i."']/r");
            //遍历并输出
            for($replyi=0;$replyi<=$replies->lenght;$replyi++){
                $reply=$replies->item($replyi);
                $reply_to_ret['author']=$reply->getAttribute('a');
                $reply_to_ret['time']=$reply->getAttribute('a');
                $reply_to_ret['body']=$reply->nodeValue;
                $replies_to_ret[$reply->getAttribute('i')]=$reply_to_ret;
            }
            return $replies_to_ret;
        }else{
            //获取回帖
            $reply=$xpath->query("root/p[@i='".$i."']/r")->item($replyi);
            //写入数组并输出
            $reply_to_ret['author']=$reply->getAttribute('a');
            $reply_to_ret['time']=$reply->getAttribute('a');
            $reply_to_ret['body']=$reply->nodeValue;
            return $reply_to_ret;
        }
        unset($xpath);
    }
    public function addFiles($files,$floor,$post){

        $xpath=new DOMXpath($this->domdoc);
        $i=$this->getOrderNumberInFile($this->posterid);
        $i=$xpath->query("root/p[@i='".$i."']/r")->length;
        unset($xpath);
        foreach($files as $file){
            //print_r($file);
            //创建对象
            $misc=new ForumMisc;
            //上传
            $fileid=$misc->upFile($file['tmp_name']);
            //成功则创建并关联节点
            //echo $fileid;
            //echo 0;
            $fileElement=$this->domdoc->createElement('f');
            $fileElement->setAttribute('n',$file['name']);//文件名
            $fileElement->setAttribute('f',$floor);//楼层
            $fileElement->setAttribute('i',$i);//序号
            $fileElement->nodeValue=$fileid;
            $post->appendChild($fileElement);
            $i++;
        }
    return $post;
    }
}


?>