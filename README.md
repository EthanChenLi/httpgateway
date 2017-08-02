# 微信网关系统
### 基于PHP的微信网关系统，运行于nuix内核服务器（包括树莓派），使用 PHP7+SWOOLE强力驱动。<br/>
微信语音指令，支持http协议

---
###### 软件支持
+ PHP7.0以上版本
+ 树莓派debian、Ubuntu、其他nuix内核系统
+ swoole 1.9X
+ 花生壳

###### 使用方法
<pre><code>php server.php</code></pre>

程序默认端口为 8090   <br/>
配置更改：server.php
- - -

###### 安装说明

###### 安装PHP7（至少包含mb_string拓展）
<pre><code>
~$ sudo apt-get install php
</code></pre>

###### 安装swoole拓展
<pre>
<code>
~$ git clone https://github.com/swoole/swoole-src.git
~$ cd swoole
~$ phpize
~$ ./configure
~$ make 
~$ sudo make install
</code>
</pre>

###### 安装花生壳 
phddns start（启动）| stop（停止）| restart（重启） <br/>
需要自行注册： http://hsk.oray.com/  <br/>
开通内网穿透，设置为本机ip 端口
<pre>
	<code>
~$ wget http://hsk.oray.com/download/download?id=25
~$ dpkg -i phddns_rapi_3.0.1.armhf.deb
~$ phddns start
	</code>
</pre>

###### 安装程序
<pre>
	<code>
~$ git clone https://github.com/EthanChenLi/httpgateway.git
~$ cd httpgateway
~$ php server.php
	</code>
</pre>
