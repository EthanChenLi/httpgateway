<?php

$serv = new Swoole\Http\Server("0.0.0.0", 8090);


$serv->set(array(
    'worker_num' => 4,
    'daemonize' => false,

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
        $response->header("X-Server", "Swoole");
        if(!empty($result)){
            $response->end($result);
        }


    }

});

$serv->start();
