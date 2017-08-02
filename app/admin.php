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
    private $confPath =APP_PATH."/data/conf.json";

    public function __construct($req,$res)
    {
        $this->req=$req;
        require_once "common.php";
        $this->public= new common($req,$res);

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
                            'keyword'=>$this->req->post['keyword']
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
        $data = file_get_contents($this->dataPath);
        if(!empty($data)) {
            $data = json_decode($data,true);
            foreach ($data as $key=>$item) {
                if($id == $item['id']){
                    unset($data[$key]);
                }
            }
        }
        file_put_contents($this->dataPath,json_encode($data));
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


}