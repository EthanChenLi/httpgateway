<?php
/**
 * 定时任务
 * User: ethanchan
 * Date: 2017/8/7
 * Time: 16:48
 */

require_once "index.php";

$serv = new \Swoole\Server("{$options['TICK_SERVER']['IP']}", $options['TICK_SERVER']['PORT']);

$serv->set(array(
    'worker_num' => 1,
    'daemonize' => $options['DAEMONIZE'],
));

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) {

    $data = json_decode($data,true);
    $atomic = new \swoole_atomic(1);
    
    if($data['type'] == "START"){
       

        $timerId =$serv->tick($data['timer'], function($timer_id) use ($serv, $fd, $atomic,$data) {
            $count = $atomic->add(1);

            if($count > $data['loop']){
                //clear
                swoole_timer_clear($timer_id);
                updateJson($serv,'null',$data['id']);
                print "--{$fd}-timeer_id:{$timer_id} clear--".PHP_EOL;
                
               
            }else{
                //exec
                file_get_contents($data['path']);
                print "--{$fd}-timer_id: {$timer_id} -".PHP_EOL;
            }
        });


    }else if($data['type'] == "STOP"){
        $timerId=$data['timer_id'];
        swoole_timer_clear($data['timer_id']);
        updateJson($serv,$fd,$data['id']);
        print "--{$fd}-timeer_id:{$timer_id} clear--".PHP_EOL;
    }

    if($timerId != false){
        //success
         updateJson($serv,$fd,$data['id'],$timerId,'1');
    }else{
        //clear
        updateJson($serv,'null',$data['id']);
    }


});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();


//更新json地址
function updateJson($serv,$fd,$id,$timer_id=1,$status= '0'){
    $path = APP_PATH.'/data/Tickdata.json';
    $file = file_get_contents($path);

    if(!empty($file)){
        $file = json_decode($file,true);
        foreach ($file as $key => $value) {
            if($id == $value['id']){
                if($status == 1){
                    $file[$key]['status']='1';
                    $file[$key]['timer_id']=$timer_id;
                }else{
                    
                    $file[$key]['status']='0';
                }
                
            }
        }
        print_r($file);
        file_put_contents($path, json_encode($file));
    }
    if($fd != 'null'){
         $serv->send($fd,json_encode([
                        "fd"        =>$fd,
                        'timer_id'  =>$timer_id,
                        'status'    =>$status
                    ]));        
    }

  
}

