<?php
namespace Home\Controller;
use Think\Controller;

header("Content-type:text/html;charset=utf-8");

class IndexController extends Controller {

    private $path_iqiyi = './Public/Image/Iqiyi';

    public function index(){
        // 初始化缓存
        $video = S('video');
        if(!$video){
            $video = $this->__getvideo();
            sort($video);
            S('video',$video,array('type'=>'file','expire'=>600));
        }
        $this->assign('video',$video);
        $this->display();
    }

    private function __geturls(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://list.youku.com/category/show/c_96.html");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        $partern =  "/<ul class=\"yk-pages\".*?>.*?<\/ul>/ism";
        preg_match_all($partern,$output,$result);

        $regex = '/<a href="^"*"^>*>(.*?(\d+).*?)<\/a>/ism';

        preg_match_all($regex,$result00,$re);
        sort($re1);
        $pos=array_search(max($re1),$re1);
        $urls = array();
        for($i=1;$i<=$re1[$pos];$i++){
            $openurl = "http://list.youku.com/category/show/c_96_s_1_d_1_p_".$i.".html?spm=a2h1n.8251845.0.0";
            array_push($urls,$openurl);
        }
        return $urls;
    }

    private function __getcontent($nodes){
        $mh = curl_multi_init();
        $curl_array = array();
        foreach($nodes as $i => $url){
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = NULL;
        do {
            usleep(1000);
            curl_multi_exec($mh,$running);
        } while($running > 0);
        $res = array();

        foreach($nodes as $i => $url){
            $re = curl_multi_getcontent($curl_array[$i]);
            $partern =  "/<li class=\"yk-col4 mr1\".*?>.*?<\/li>/ism";
            preg_match_all($partern,$re,$res[$i]);
        }
        foreach($nodes as $i => $url){
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);
        return $res;
    }

    private function __getvideo(){
        $urls = $this->__geturls();
        $data = array_reduce($this->__getcontent($urls), 'array_merge', array());
        $result = array_reduce($data, 'array_merge', array());
        $re = implode('',$result);
        $regex = '/<a href="(.*?)" title="(.*?)" target=".*?">.*?<\/a>/ism';
        $imgregex = '/<img.*?src="(.*?)".*?\/>/';
        preg_match_all($regex,$re,$res);
        $videos = $res1;
        $videos_title = $res2;
        preg_match_all($imgregex,$re,$resimg);
        $imgsrc = $resimg1;
        $video = array();
        for($i=0;$i<sizeof($videos);$i++){
            $video[$i]['title'] = $videos_title[$i];
            $video[$i]['imgsrc'] = $imgsrc[$i];
            if(strstr($videos[$i],'http:')){
                $video[$i]['videosrc'] = $videos[$i];
            }else{
                $video[$i]['videosrc'] = 'http:'.$videos[$i];
            }
        }
        return $video;
    }

    private function __getiqiyiurls(){
        $urls = array();
        for($i=1;$i<=30;$i++){
            $openurl = "http://list.iqiyi.com/www/1/-------------11-".$i."-1-iqiyi--.html";
            array_push($urls,$openurl);
        }
        return $urls;
    }

    private function __getiqiyicontent($nodes){
        $mh = curl_multi_init();
        $curl_array = array();
        foreach($nodes as $i => $url){
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = NULL;
        do {
            usleep(1000);
            curl_multi_exec($mh,$running);
        } while($running > 0);
        $res = array();

        foreach($nodes as $i => $url){
            $re = curl_multi_getcontent($curl_array[$i]);
            $partern =  "/<ul class=\"site-piclist site-piclist-180236 site-piclist-auto\".*?>.*?<\/ul>/ism";
            preg_match_all($partern,$re,$res[]);
        }
        foreach($nodes as $i => $url){
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);
        return $res;
    }
    private function __getiqiyivideo(){
        $re = S('re');
        if(!$re){
            $urls = $this->__getiqiyiurls();
            $data = $this->__getiqiyicontent($urls);
            $result = array_reduce($data, 'array_merge', array());
            $re = implode('',array_reduce($result,'array_merge', array()));
            S('re',$re,array('type'=>'file','expire'=>600));
        }
        $regex = '/<a.*?title=".*?".*?href="(.*?)".*?>.*?<\/a>/ism';
        $imgregex = '/<img(.*?)\/>/ism';
        preg_match_all($regex,$re,$arr);
        $img = implode('',$arr[0]);
        preg_match_all($imgregex,$img,$imgarr);
        $pregex = '/<a.*?rseat="bigTitle".*?title=".*?".*?href="(.*?)".*?>(.*?)<\/a>/ism';
        preg_match_all($pregex,$re,$title);
        $links = $title[1];
        $vedios = array();
        foreach($imgarr[1] as $k => $v){
            $str = $this->__trimall($v);
            $strarr = array_filter(explode('"',str_replace(array("="),'"',$str)));
            $vedios[$k]['title'] = $strarr[14];
            $vedios[$k]['imgsrc'] = $strarr[17];
            $vedios[$k]['videosrc'] = $links[$k];
            $vedios[$k]['src'] = $this->__getlinklast($strarr[17]);
            //if(!$this->__getImage($vedios[$k]['imgsrc'],$this->path_iqiyi,$vedios[$k]['src'],1)){
            //    continue;
            //}
        }
        return $vedios;
    }
    public function iqiyi(){
        // 初始化缓存
        $videos = S('videos');
        if(!$videos){
            $videos = $this->__getiqiyivideo();
            sort($videos);
            S('videos',$videos,array('type'=>'file','expire'=>600));
        }
        $this->assign('videos',$videos);
        $this->display();
    }
    public function play(){
        $link = base64_decode(I('get.link'));
        $this->assign('link',$link);
        $this->display();
    }

    /*
     * 去除字符串多空格
     * @param $str string
     * @return string
     * */
    private function __trimall($str) {
        $oldchar=array(" ","　","\t","\n","\r");
        $newchar=array("","","","","");
        return str_replace($oldchar,$newchar,$str);
    }
    /*
     * 根据图片的url，获取远程图片，利用curl进行图片的下载，并进行存储，最后返回图片的相关保存信息。
     * */
    private function __getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'&&$ext!='.jpeg'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().rand(0,10000).$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }
    private function __getlinklast($link){
        $arr = explode('/',$link);
        return array_pop($arr);
    }
}