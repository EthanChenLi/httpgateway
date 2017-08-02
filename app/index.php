<?php
/**
 * app处理
 * User: ethanchan
 * Date: 2017/7/27
 * Time: 18:04
 * author Ethan <touch_789@163.com>
 */

class Index{

    private $dataPath = APP_PATH."/data/data.json"; //数据存储保存路径
    private $confPath = APP_PATH."/data/conf.json";//配置文件
    private $options ;

    public function __construct($option)
    {
        $this->req = $option;
        $this->optionsjson = json_decode(file_get_contents($this->confPath),true);
    }

    //入口方法
    public function index(){
        if($this->optionsjson['verify'] == 1)
            return $this->valid(); //认证
        $post =$this->req->rawContent();//请求的XML内容
        $content = $this->_xmlToArray($post);
        if($content['MsgType'] == 'voice')
            return $this->_check($content);
    }


     // 将响应输出
    private function valid(){
        $get =$this->req->get;
        return $get['echostr'];
    }
    //处理方法
    private function _check($content){
        $msg =$content['Recognition'];
        $str = mb_substr($msg,0,-1,"utf-8");
        $msg= $this->_sendcommon($str,$content);
        return  $this->_replayText($content['FromUserName'],$content['ToUserName'],$msg);
    }

    //发送指令
    private function _sendcommon($str,$content){
        $file = file_get_contents($this->dataPath);
        if(!empty($file)){
            $data = json_decode($file,true);
            foreach ($data as $item) {
                $pos =mb_strpos($item['keyword'],"{$str}");
                if($pos === 0 || !empty($pos)){
                    $api = $item['path'];
                    break;
                }
            }
            if(!empty($api)){
                $msg ="正在执行指令: ".$str;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                //执行并获取HTML文档内容
                $output = curl_exec($ch);
                curl_close($ch);
                
                if(!is_null(json_decode($output))){
                        $infoJson = json_decode($output,true);
                        print_r($infoJson);
                        if($infoJson['status'] == "success"){
                            $msg = $infoJson['content'];
                        }
                }
                print PHP_EOL."请求地址: ".$api.PHP_EOL;
            }else{
                $msg ="无效的指令: ".$str;
            }
            print $msg.PHP_EOL;
            return $msg;
        }
    }
    //回复文本
    public function _replayText($to,$from,$msg){
        $html ="<xml>";
        $html.="<ToUserName><![CDATA[{$to}]]></ToUserName>";
        $html.="<FromUserName><![CDATA[{$from}]]></FromUserName>";
        $html.="<CreateTime>".time()."</CreateTime>";
        $html.="<MsgType><![CDATA[text]]></MsgType>";
        $html.="<Content><![CDATA[{$msg}]]></Content>";
        $html.="</xml>";
        return $html;
    }

    private  function _xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

}