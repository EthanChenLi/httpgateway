<?php
/**
 * server服务器启动文件
 * php server.php 启动
 *  
 * author Ethan <touch_789@163.com>
 */
require_once "index.php";

$serv = new \Swoole\Http\Server("{$options['SERVER_IP']}", $options['SERVER_PORT']);
$serv->set(array(
    'worker_num' => $options['WORKER_NUM'],
    'daemonize' => $options['DAEMONIZE'],
));

$serv->on('Request', function($request, $response) use($options){
    if($request->server['path_info'] != "/favicon.ico"){
        $path = explode("/",$request->server['path_info']);
        if(empty($path[1])){
            require_once APP_PATH."//app//index.php";
            $class = new index($request,$response,$options);
            $result =$class->index();
        }else{
            $pathArr = array_filter($path);
            $className= $pathArr[1];
            $className = strtolower($className);
            require_once APP_PATH."//app//".$className.".php";
            $className = ucfirst($className);
            $class = new $className($request,$response,$options);
            empty($pathArr[2])?$funName="index":$funName =$pathArr[2];
            $result= $class->$funName();
        }
        $response->header("X-Server", "Ethan-swoole");
        if(!empty($result)){
            $response->end($result);
        }
    }
});
$serv->start();
