<?php
/**
 * Created by PhpStorm.
 * User: ethanchan
 * Date: 2017/7/28
 * Time: 15:14
 * author Ethan <touch_789@163.com>
 */
class Admin{

    private $dataPath = APP_PATH."/data/data.json"; //数据存储保存路径
    private $tickPath = APP_PATH."/data/Tickdata.json";
    private $confPath =APP_PATH."/data/conf.json";

    public function __construct($req,$res,$conf)
    {
        $this->req=$req;
        require_once "common.php";
        $this->public= new common($req,$res);
        $this->conf = $conf;
    }


    public function index(){
       
        return $this->public->tpl();
    }


    //详情
    public function info(){
        $this->public->tpl("info");
    }

    //提交数据
    public function check(){

        $data = file_get_contents($this->dataPath);
        !empty($data)?$data = json_decode($data,true):$data=[];

            $id = $this->req->post['id'];
            if(!empty($id)){
                //edit
                foreach ($data as $key=>$item) {
                    if($id == $item['id']){
                       unset($data[$key]);
                        $new_data[$id] =[
                            'id'=>$id,
                            'path'=>$this->req->post['path'],
                            'keyword'=>$this->req->post['keyword'],                       
                        ];
                    }
                }
            }else{
                //add
                $id = time();
                $new_data[$id] =[
                    'id'=>$id,
                    'path'=>$this->req->post['path'],
                    'keyword'=>$this->req->post['keyword']
                ];
            }


            $dataJson = json_encode(array_merge($data,$new_data));

            $result = file_put_contents($this->dataPath,$dataJson);
            if($result){
                return json_encode([
                    'status'=>1,
                    'info'=>'设置成功'
                ]);
            }else{
                return json_encode([
                    'status'=>0,
                    'info'=>'设置失败'
                ]);
            }

    }

    //拉取数据
    public function getdata(){
        $data = file_get_contents($this->dataPath);
        if(!empty($data)) {
            $data = json_decode($data,true); 
            if(empty($this->req->get['id'])){

                $html="";
                foreach ($data as $item) {
                    $html .="<tr>";
                    $html .="<td>{$item['id']}</td>";
                    $html .="<td>{$item['path']}</td>";
                    $html .="<td>".mb_substr($item['keyword'],0,20)."</td>";
                    $html .="<td><a href='/admin/info/?id={$item['id']}'>[修改]</a>|<a href='javascript:;' onclick='del({$item['id']})' >[删除]</a></td>";
                    $html .="</tr>";
                }
                return $html;
            }else{
                $id = $this->req->get['id'];
                foreach ($data as $key=>$item) {
                    if($id == $item['id']){
                        $result= [
                            'status'=>1,
                            'data'=>$data[$key]
                        ];
                        break;
                    }else{
                        $result= [
                            'status'=>0,
                            'info'=>"拉取数据失败"
                        ];
                    }
                }
                    return json_encode($result);
            }
        }
    }

    //删除请求
    public function del(){
        $id = $this->req->post['id'];

        if(empty($this->req->post['type'])){
            $path = $this->dataPath;
        }else{
            $path =APP_PATH."/data/".ucfirst(strtolower($this->req->post['type']))."data.json";
        }

        $data = file_get_contents($path);
        if(!empty($data)) {
            $data = json_decode($data,true);
            foreach ($data as $key=>$item) {
                if($id == $item['id']){
                    unset($data[$key]);
                }
            }
        }
        file_put_contents($path,json_encode($data));
    }

    //全局设置
    public function setting(){
        if(!empty($this->req->post['appid'])){
            $data['appid'] = $this->req->post['appid'];
            $data['secret'] = $this->req->post['secret'];
            $data['token'] = $this->req->post['token'];
            if(!empty($this->req->post['verify'])){
                $data['verify']=1;
            }else{
                 $data['verify']=0;
            }
            $result =file_put_contents($this->confPath,json_encode($data));
            if($result){
                return json_encode([
                    'status'=>1,
                    'info'=>'设置成功'
                ]);
            }else{
                return json_encode([
                    'status'=>0,
                    'info'=>'设置失败'
                ]);
            }
        }else{
           $this->public->tpl("setting");  
        } 
    }

    //拉取配送信息
    public function getsetting(){
        $file=  file_get_contents($this->confPath);
        return !empty($file)?$file:json_encode([]); 
    }


    /*-------------------TICK---------------------*/

    public function tick(){
        $this->public->tpl('tick');
    }

    public function info_tick(){


         $this->public->tpl('info_tick');
    }

    //提交
    public function check_tick(){
        $data = $this->_getData();
        $info=  $this->req->post;
        if(empty($info['id'])){
            //add
            $id =time();
            $param[$id] = [
                'id'=>$id,
                'path'=>$info['path'],
                'timer'=>$info['timer'],
                'loop'=>$info['loop'],
                'title'=>$info['title'],
                'status'=>'0' ,
            ];


        }else{
            //EDIT
            $id = $info['id'];
            foreach ($data as $key=>$item) {
                if($id == $item['id']){
                   unset($data[$key]);
                    $param[$id] =[
                        'id'=>$id,
                        'path'=>$info['path'],
                        'timer'=>$info['timer'],
                        'loop'=>$info['loop'],
                        'title'=>$info['title'],
                        'status'=>'0' ,                   
                    ];
                }
            }
        }
        $dataJson = json_encode(array_merge($data,$param));
        $result= file_put_contents($this->tickPath, $dataJson);
        if($result){
                return json_encode([
                    'status'=>1,
                    'info'=>'设置成功'
                ]);
            }else{
                return json_encode([
                    'status'=>0,
                    'info'=>'设置失败'
                ]);
            }
    }

    //拉取列表信息
    public function getdata_tick(){
        $data = file_get_contents($this->tickPath);
        if(!empty($data)) {
            $data = json_decode($data,true);  
            if(empty($this->req->get['id'])){ 
                $html="";
                foreach ($data as $item) {
                    $html .="<tr>";
                    $html .="<td>{$item['id']}</td>";
                    $html .="<td>{$item['title']}</td>";
                    $html .="<td>{$item['path']}</td>";
                    $html .="<td>{$item['timer']}</td>";
                    $html .="<td>{$item['loop']}</td>";
                    if($item['status'] == '0'){
                        $html .="<td><a href='#' onclick='getmission({$item['id']})'>[请求执行]</a></td>";
                        $html .="<td><a href='/admin/info_tick/?id={$item['id']}'>[修改]</a>|<a href='javascript:;' onclick='del({$item['id']})' >[删除]</a></td>";    
                    }else{
                        $html .="<td><a href='#' onclick='getmission({$item['id']})'>[请求停止]</a></td>";
                        $html .="<td><a href='#'> -- </a>|<a href='javascript:;'  > -- </a></td>";

                    }
                    $html .="</tr>";
                }
             
                return $html;
            }else{
                $id = $this->req->get['id'];
                foreach ($data as $key=>$item) {
                    if($id == $item['id']){
                        $result= [
                            'status'=>1,
                            'data'=>$data[$key]
                        ];
                        break;
                    }else{
                        $result= [
                            'status'=>0,
                            'info'=>"拉取数据失败"
                        ];
                    }
                }
                    return json_encode($result);
            }
        }
    }



    public function tick_mission(){
        $id = $this->req->post['id'];
        $data =$this->_getData();
        foreach ($data as $key => $value) {
            
            if($id == $value['id']){
                if($value['status'] == '1'){
                    //stop
                    $this->_getServer([
                        "type"      =>"STOP",
                        'timer_id'  =>$value['timer_id'],
                        'id'        =>$value['id'],
                    ]);
                }else{
                    //start
                    $this->_getServer([
                        'type'      =>'START',
                        'id'        =>$value['id'],
                        'timer'       =>$value['timer'],
                        'loop'      =>$value['loop'],
                        'path'      =>$value['path'],
                    ]);
                }

            }
        }
    }


    //执行请求
    public function _getServer(array $options=[]){
        
    
        $client = new \swoole_client(SWOOLE_SOCK_TCP);

      
        if (!$client->connect($this->conf['TICK_SERVER']['IP'], $this->conf['TICK_SERVER']['PORT'], -1))
        {
            print ("connect failed. Error: {$client->errCode}\n");
        }  

        $client->send(json_encode($options));
        print $client->recv();
        $client->close();
    }


    //拉取数据 -- tick
    private function _getData($path = null){
        if($path == null) $path = $this->tickPath;
        $data = file_get_contents($path);
        !empty($data)?$data = json_decode($data,true):$data=[];
        return $data;
    }

}