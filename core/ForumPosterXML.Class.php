<?php
/*ForumPosterXML类
 * 用于处理帖子XML文件
 * Date：18-12-30
 */
require_once 'ForumSystem.Class.php';
require_once 'ForumMisc.Class.php';
require_once 'constants.php';
class ForumPosterXML{
    public $domdoc;
    public $posterid;
    public $xmlfile;
    public function __construct($posterid=null){
        $this->domdoc=new DOMDocument;
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
        $this->posterid=$posterid;
        $this->xmlfile=$this->getXMLPath($posterid);
        if(file_exists($this->xmlfile)){
            $this->domdoc->load($this->xmlfile);
        }else{
            return false;
        }
    }
    public function getXMLPath($posterid,$format=FORUM_GET_XML_PATH_FULL){
        switch ($format){
            case 0:
                return FORUM_DATA_HOME.'/posters/'.substr($posterid,POSTER_AREA_OFFSET,POSTER_AREA_LEN);
            case 1:
                return FORUM_DATA_HOME.'/posters/'.substr($posterid,POSTER_AREA_OFFSET,POSTER_AREA_LEN).'/'.substr($posterid,POSTER_DIR_OFFSET,POSTER_DIR_LEN);
            case 2:
                return dechex(floor(hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))/POSTERS_PER_FILE));
            case 3:
                //echo '-'.hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN)).'-';
                return FORUM_DATA_HOME.'/posters/'.substr($posterid,POSTER_AREA_OFFSET,POSTER_AREA_LEN).'/'.substr($posterid,POSTER_DIR_OFFSET,POSTER_DIR_LEN).'/'.dechex(floor(hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))/POSTERS_PER_FILE)).'.xml';
                //echo substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN);
            default:
                return false ;
        }
    }
    public function getNumberInFile($posterid){
        //返回帖子在文件中的序号
        return hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))-hexdec(($this->getXMLPath($posterid,FORUM_GET_XML_PATH_BASENAME)))*POSTERS_PER_FILE;
    }
    public function addPoster($poster,$title,$area,$files=null){
        //创建帖子
        //设置引用
        $dom=&$this->domdoc;
        $posterid=&$this->posterid;
        $xmlfile=&$this->xmlfile;
        //得到ID并增加计数
        $posterid=$area.str_pad(ForumSystem::get_forum_posters_num($area),POSTER_NUM_LEN,'0',STR_PAD_LEFT);
        echo $posterid.'/';
        //$posterid=$area.printf("%0".ceil(log(ForumSystem::get_forum_poster_num($area)."x",ForumSystem::get_forum_poster_num)));
        ForumSystem::add_forum_poster_num($area);
        //得到文件名
        $xmlfile=$this->getXMLPath($posterid);
        //如果最近的文件容量已满，则创建新文件
        if(hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))%POSTERS_PER_FILE==0){
            $root=$dom->createElement('root');
            $dom->appendChild($root);
            //如果文件夹不存在，则创建
            //如果文件夹不存在，则创建
            if(!file_exists($this->getXMLPath($posterid,FORUM_GET_XML_PATH_DIR))){
                if(!mkdir($this->getXMLPath($posterid,FORUM_GET_XML_PATH_DIR))){
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
        $post->setAttribute('i',$this->getNumberInFile($posterid));
        //创建主题帖节点
        $thread=$dom->createElement('t');
        //设置帖子属性
        $thread->setAttribute('t',time());//时间
        $thread->setAttribute('a',$_SESSION['uid']);//作者
        $thread->setAttribute('h',$title);//标题
        //设置帖子内容
        $thread->nodeValue=$poster;
        //创建和管理文件并上传文件
        if($files!==null){
            $post=$this->addFiles($files,1,$post);
        }
        //关联各个节点
        $post->appendChild($thread);
        $root->appendChild($post);
        //保存
        $dom->save($xmlfile);
        echo $xmlfile;
        return true;
    }
    public function getThread(){
        //获取主题帖
        //初始化引用
        $dom=&$this->domdoc;
        //得到帖子序号
        $i=$this->getNumberInFile($this->posterid);
        //使用xpath搜索
        $xpath=new DOMXpath($this->domdoc);
        $threadElement=$xpath->query("/root/p[@i='".$i."']/t")->item(0);
        if($threadElement===null){
            return false;
        }
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
        $i=$this->getNumberInFile($this->posterid);
        //使用xpath搜索帖子
        $xpath=new DOMXpath($this->domdoc);
        $post=$xpath->query("/root/p[@i='".$i."']")->item(0);
        //获取回帖序号
        $repid=$xpath->query("/root/p[@i='".$i."']/r")->length;
        unset($xpath);
        //建立节点设置属性
        $reply_element=$dom->createElement('r');
        $reply_element->setAttribute('t',time());//时间
        $reply_element->setAttribute('a',$_SESSION['uid']);//作者
        $reply_element->setAttribute('i',$repid);//序号
        //设置内容
        $reply_element->nodeValue=$reply;
        //关联节点并保存
        $post->appendChild($reply_element);
        if($files!==null){
            $post=$this->addFiles($files,$repid+2,$post);
        }
        $dom->save($this->xmlfile);
        return true;
        
    }
    public function getReply($replyi='all'){
        //获取回帖
        //初始化变量和引用
        $dom=&$this->domdoc;
        $replies_to_ret=array();
        //得到帖子序号
        $i=$this->getNumberInFile($this->posterid);
        //创建Xpath对象
        $xpath=new DOMXpath($this->domdoc);
        if($replyi=='all'){
            //获取回帖NodeList
            $replies=$xpath->query("/root/p[@i='".$i."']/r");
            //遍历并输出
            for($replyi=0;$replyi<$replies->length;$replyi++){
                $reply=$replies->item($replyi);
                $replies_to_ret[$replyi]['author']=$reply->getAttribute('a');
                $replies_to_ret[$replyi]['time']=$reply->getAttribute('t');
                $replies_to_ret[$replyi]['body']=$reply->nodeValue;
                //$replies_to_ret[$reply->getAttribute('i')]=$reply_to_ret;
            }
            return $replies_to_ret;
        }else{
            //获取回帖
            $reply=$xpath->query("/root/p[@i='".$i."']/r")->item($replyi);
            //写入数组并输出
            $reply_to_ret['author']=$reply->getAttribute('a');
            $reply_to_ret['time']=$reply->getAttribute('t');
            $reply_to_ret['body']=$reply->nodeValue;
            return $reply_to_ret;
        }
    }
    public function getFiles($floor){
        $xpath=new DOMXpath($this->domdoc);
        $i=$this->getNumberInFile($this->posterid);
        $files=$xpath->query("/root/p[@i='".$i."']/f[@f='".$floor."']");
        if($files->length==0){
            return false;
        }
        for($i=1;$i<=$files->length;$i++){
            $file=$files->item($i-1);
            $flies_to_array[$i]['id']=str_pad($file->getAttribute('i'),POSTER_FILE_ID_LEN,'0',STR_PAD_LEFT);
            $flies_to_array[$i]['name']=$file->getAttribute('n');
        }
        return $flies_to_array;
    }
    public function addFiles($files,$floor,$post){
        $i=$this->getNumberInFile($this->posterid);
        foreach($files as $file){
            //上传
            $fileid=ForumMisc::upFile($file['tmp_name']);
            //成功则创建并关联节点
            $fileElement=$this->domdoc->createElement('f');
            $fileElement->setAttribute('n',$file['name']);//文件名
            $fileElement->setAttribute('f',$floor);//楼层
            $fileElement->setAttribute('i',$i);//序号
            $post->appendChild($fileElement);
            $i++;
        }
    return $post;
    }
    public static function posterExists($posterid){
        $path=FORUM_DATA_HOME.'/posters/'.substr($posterid,POSTER_AREA_OFFSET,POSTER_AREA_LEN).'/'.substr($posterid,POSTER_DIR_OFFSET,POSTER_DIR_LEN).'/'.dechex(floor(hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))/POSTERS_PER_FILE)).'.xml';
        if(file_exists($path)){
            $dom=new DOMDocument;
            $dom->load($path);
            $xpath=new DOMXpath($dom);
            $i=hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN))-(floor(hexdec(substr($posterid,POSTER_BASENAME_OFFSET,POSTER_BASENAME_LEN)/POSTERS_PER_FILE)))*POSTERS_PER_FILE;
            if($xpath->query("/root/p[@i='".$i."']")->length!=0){
                return true;
            }
        }
        return true; //false
    }
}


?>