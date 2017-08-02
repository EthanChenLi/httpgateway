<?php
/**
 * server服务器启动文件
 * php server.php 启动
 *  
 * author Ethan <touch_789@163.com>
 */

//全局配置
$options =[
    'SERVER_IP'=>'0.0.0.0', //服务器ip，允许所有ip访问
    'SERVER_PORT'=>8090,//服务器端口
    'DAEMONIZE'=>true, //守护进程，调试模式下请设为false
    'WORKER_NUM'=>4,//进程数，与CPU核心数相同即可
];
define("APP_PATH", __DIR__);
$serv = new \Swoole\Http\Server("{$options['SERVER_IP']}", $options['SERVER_PORT']);
$serv->set(array(
    'worker_num' => $options['WORKER_NUM'],
    'daemonize' => $options['DAEMONIZE'],
));
$serv->on('Request', function($request, $response) {
    if($request->server['path_info'] != "/favicon.ico"){
        $path = explode("/",$request->server['path_info']);
        if(empty($path[1])){
            require_once "app//index.php";
            $class = new index($request,$response);
            $result =$class->index();
        }else{
            $pathArr = array_filter($path);
            $className= $pathArr[1];
            $className = strtolower($className);
            require_once "app//".$className.".php";
            $className = ucfirst($className);
            $class = new $className($request,$response);
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
