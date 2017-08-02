<?php
/**
 * 公用方法.
 * User: ethanchan
 * Date: 2017/7/28
 * Time: 15:26
 * author Ethan <touch_789@163.com>
 */
class Common {

    public function __construct($req,$res)
    {
        $this->res=$res;
    }

    private $path = APP_PATH."/view/";
    public function tpl($name="index"){
          $file =$this->path.$name.".html";
         \swoole_async_readfile($file, function($filename, $content) {
           $this->res->end($content);
         });
    }


}