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
        //echo 0;
        $posternum=hexdec(ForumSystem::get_forum_posters_num($area))-1;
        //echo $posternum.'/';
        $i=0;
        $checked=0;
        if($posternum==0){
            return array();
        }
        while($i<=$num){
            $posterid=$area.sprintf("%0".POSTER_NUM_LEN."x",$posternum-$checked);
            //echo '['.$posternum.'-'.$checked.']';
            //echo $posterid.'-';
            if($i>$posternum){
                break;
            }
            if(ForumPosterXML::posterExists($posterid)){
                $this->list[]=$posterid;
                $i++;
            }
            $checked++;
        }
        return $this->list;
    }
    public function compressUtf8($string){
        $compressed_tag='';
        $is_header=true;
        for($i=0;$i<strlen($string);$i++){
            //压缩utf-8字符串
            if($string[$i]&0x80==0&&$is_header){
                //7-bit ASCII
                $compressed_tag.=decbin(ord($string));
            }else if($string[$i]&0x80==0x80&&$is_header){
                //多字节字符
                $bytes_num=8-strlen(strstr(decbin(ord($string[i])),'0'));
                $compressed_tag.=decbin($string[$i]&(~((1<<$bytes_num)-1)<<8-$bytes_num));
                $is_header=false;
                $bytes_num--;
            }else if($string[$i]&0xc0==0xc0&&is_header==false){
                //多字节字符第二个及以后字节
                $compressed_tag.=substr(decbin(ord($string[i])),2,6);
                $bytes_num--;
                if($bytes_num==0){
                    $is_header=true;
                }
            }
        }
        for($i=0;$i<12;$i++){
            if(strlen($compressed_tag)-$i*2-1<0){
                break;
            }
            $result_num+=0x800*$compressed_tag[strlen($compressed_tag)-$i*2-1];
        }
        return chr(($result_num&0xf00)>>8).chr(($result_num&0xff));
    }
    public function createPosterIndexItem($posterid,$tags,$flag_update_tags=1){
        $fp_tags=fopen(FORUM_DATA_HOME.'/tags.txt','r+');
        $fp_index=fopen(FORUM_DATA_HOME.'/postidx','rb+');
        $fp_tags_num=ord(fread($fp_tags,2)[0])*256+ord(fread($fp_tags,2)[1]);
        if($flag_update_tags){
            $tags_exists=array();
            for($i=0;$i<$fp_tags_num;$i++){
                $tags_exists[]=str_replace('\n','',fgets($fp_tags));
            }
            $new=array_diff($tags,$tags_exists);
            foreach($new as $tag){
                fputs($fp,$tag);
            }
        }
        $nums=array();
        for($i=0;$i<count($tags);$i++){
            $nums[$i]=$this->compressUtf8($tags[$i]);
        }
        foreach($nums as $key=>$num){
            fseek($fp_index,$num*3);
            $offset_raw=fread($fp_index,3);
            $next_offset=ord($offset_raw[0])<<16+ord($offset_raw[1])<<8+ord($offset_raw[2]);   //实际上该是offset，为了一些需要实际使用next_offset
            fseek($fp_index,$num*3+3);
            $next_chunk_raw=fread($fp_index,3);
            $next_chunk=ord($next_chunk_raw[0])<<16+ord($next_chunk_raw[1])<<8+ord($next_chunk_raw[2]);
            while($tag_in_file!=$tags[$key]&&$next_offset!=$next_chunk){
                fseek($fp_index,$next_offset);
                $raw_data_header=fread($fp_index,4);
                $curr_offset=$next_offset;
                $next_offset=ord($raw_data_header[0])<<16+ord($raw_data_header[1])<<8+ord($raw_data_header[2]);
                $tag_in_file=fread($fp_index,ord($raw_data_header[3]));
            }
            if($next_offset=0){
                if($flag_update_tags){
                    fwrite($fp_index,$next_chunk);
                    fseek($fp_index,$next_chunk);
                    $file=fread($fp_index,filesize(FORUM_DATA_HOME.'/postidx')-$next_chunk);
                    $next_tag_item=POSTER_ID_LEN/2+4+$next_chunk;
                    $raw_data=chr(($next_tag_item&0xff0000)>>16)+chr(($next_tag_item&0xff00)>>8)+chr($next_tag_item&0xff);
                    $raw_data.=chr(strlen($tag));
                    $raw_data.=chr(($posterid&0xff0000)>>16)+chr(($posterid&0xff00)>>8)+chr($posterid&0xff);
                    //
                    fwrite($fp_index,$rawdata);
                    fseek($fp_index,$next_tag_item);
                    fwrite($fp_index,$file);
                    unset($file);
                    fseek($fp_index,$num*3+3);
                    fwrite($fp_index,chr(($next_tag_item&0xff0000)>>16)+chr(($next_tag_item&0xff00)>>8)+chr($next_tag_item&0xff));
                }else{
                    continue;
                }
            }
        }
        fclose($fp_tags);
        fclose($fp_index);
    }
    public function searchPosterByTags($key,$num){
        
    }
    public function getRelatedPoster($posterid){
        //nothing
    }
}
?>
