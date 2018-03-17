#!/usr/bin/python
import sys
import _mysql
import _mysql_exceptions
import cgi
import cgitb; cgitb.enable()
import json
print ("Content-type: text/html\n\n")

# Create instance of FieldStorage 
form = cgi.FieldStorage() 

# Get data from fields
Type = form.getvalue('Type')
ID = form.getvalue('ID')
User = form.getvalue('User')
Text = form.getvalue('Text')
AT = form.getvalue('AT')

#Attempt at MySQL DataBase connection
try:
    db=_mysql.connect(user="sudocom",host="localhost", db="sudocom" ,passwd="gate+Grace*civic")
    if Type == 'Boards' or Type == None:
        db.query("SELECT * FROM Boards")
        r = db.store_result()
    elif Type == 'search':
        db.query("SELECT * FROM Posts")
        r = db.store_result()
        db.query("SELECT * FROM Threads")
        s = db.store_result()
    elif Type == 'User_Check':
        db.query("SELECT * FROM User_Base WHERE Username = '%s'" % (User))
        r = db.store_result()
    elif Type == 'Img':
        db.query("SELECT * FROM User_Base WHERE Username = '%s'" % (User))
        r = db.store_result()
    else:
        db.query("SELECT * FROM %s WHERE Parent = '%s'" % (Type, ID))
        r = db.store_result()
    response = r.fetch_row(0,1)
    out = []
    for i in range(len(response)):
        if Type == 'Boards':
            curr = 'Board_ID'+'`'+response[i]['Board_ID']
            curr += ';'
            curr += 'Name'+'`'+response[i]['Name']
            curr += '%'
            out.append(curr)
        elif Type == 'Threads':
            curr = 'Thread_ID'+'`'+response[i]['Thread_ID']
            curr += ';'
            curr += 'Name'+'`'+response[i]['Name']
            curr += ';'
            curr += 'Parent'+'`'+response[i]['Parent']
            curr += ';'
            curr += 'Author'+'`'+response[i]['Author']
            curr += '%'
            out.append(curr)
        elif Type == 'Posts':
            curr = 'Post_ID'+'`'+response[i]['Post_ID']
            curr += ';'
            curr += 'Parent'+'`'+response[i]['Parent']
            curr += ';'
            curr += 'Author'+'`'+response[i]['Author']
            curr += ';'
            curr += 'Content'+'`'+response[i]['Content']
            curr += ';'
            curr += 'Timestamp'+'`'+response[i]['Timestamp']
            curr += '%'
            out.append(curr)
        elif Type == 'search':
            if  Text!= None and Text!='' and Text in response[i]['Content']:
                curr = ''
                curr += response[i]['Content']
                curr += chr(31)
                curr += response[i]['Timestamp']
                curr += chr(31)
                curr += response[i]['Author']
                curr += chr(31)
                bread = s.fetch_row(0,1)
                for j in range(len(bread)):
                    if bread[j]["Thread_ID"] == response[j]["Parent"]:
                        curr += '?Name='+bread[j]["Name"]+'&ID='+bread[j]["Thread_ID"]
                        break
                curr += chr(30)
                out.append(curr)
        elif Type == 'User_Check':
            if response[0]['Access_Token'] == AT:
                curr = 'True'
            else:
                curr = 'False'
            out.append(curr)
    if Type == 'Img':
        out = response[0]['DP']
    for i in range(len(out)):
        sys.stdout.write(out[i])
except _mysql_exceptions.Error as err:
    print("Something went wrong: {0}".format(err))
