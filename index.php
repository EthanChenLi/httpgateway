<?php
/**
 * 定义入口文件
 * @author  Ethan chan <touch_789@163.com>
 */
define("APP_PATH", __DIR__);

//Http 服务配置
$options =[
    'SERVER_IP'=>'0.0.0.0', //服务器ip，允许所有ip访问
    'SERVER_PORT'=>8090,//服务器端口
    'DAEMONIZE'=>true, //守护进程，调试模式下请设为false
    'WORKER_NUM'=>2,//进程数，与CPU核心数相同即可
    "TICK_SERVER"=>[
    	'IP'=>'127.0.0.1',
    	'PORT'=>'8088',
    	'JSONPATH' =>APP_PATH."/data/Tickdata.json",
    ]

];