# 微信网关系统
### 基于PHP的微信网关系统，运行于nuix内核服务器（包括树莓派），使用 PHP7+SWOOLE强力驱动。

---
###### 软件支持
+ PHP7.0以上版本
+ 树莓派debian、Ubuntu、其他nuix内核系统
+ swoole 1.9X
+ 花生壳

###### 使用方法
<pre><code>php server.php</code></pre>

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
phddns start（启动）| stop（停止）| restart（重启）
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
