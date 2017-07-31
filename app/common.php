<?php
/**
 * 公用方法.
 * User: ethanchan
 * Date: 2017/7/28
 * Time: 15:26
 */
class Common {

    public function __construct($req,$res)
    {
        $this->res=$res;
    }

    private $path = ".//view//";
    public function tpl($name="index"){
         $file =$this->path.$name.".html";
        \swoole_async_readfile($file, function($filename, $content) {
           $this->res->end($content);
        });
    }


}