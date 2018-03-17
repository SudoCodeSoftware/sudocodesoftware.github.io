#!/usr/bin/python
import _mysql
import _mysql_exceptions
import cgi
import cgitb; cgitb.enable()
import sys
import json
print ("Content-type: text/html\n\n")

# Create instance of FieldStorage 
form = cgi.FieldStorage() 

# Get data from fields
Type = form.getvalue('Type')
Name = form.getvalue('Name')
User = form.getvalue('User')
Parent = form.getvalue('Parent')
Content = form.getvalue('Content')
Img = form.getvalue('Img')
user = form.getvalue('Username')
Timestamp = form.getvalue('Time')
Access_Token = form.getvalue('AT')


#Attempt at MySQL DataBase connection
try:
    db=_mysql.connect(user="sudocom",host="localhost", db="sudocom" ,passwd="gate+Grace*civic")
    if Type == 'Boards' or Type == None and Name != None:
        db.query("INSERT INTO Boards (Board_ID, Name) VALUES (NULL, '@%s')" % ((cgi.escape(Name))))
    elif Type == 'Threads' and Name != None:
        db.query("INSERT INTO Threads (Thread_ID, Name, Parent, Author) VALUES (NULL, '@%s', '@%s', '@%s')" % ((cgi.escape(Name)), (cgi.escape(Parent)), (cgi.escape(User))))
    elif Type == 'Posts' and Content != None:
        db.query("Select * FROM User_Base WHERE User = '%s'" % (cgi.escape(User)))
        r = db.store_result()
        response = r.fetch_row(0,1)
        if response['AT'] != Access_Token:
            Content = ''
            break
        dif = False
        while dif != True:
            db.query("SELECT * FROM Posts")
            o = db.store_result()
            db.query("INSERT INTO Posts (Post_ID, Parent, Author, Content, Timestamp) VALUES (NULL, '%s', '%s', '%s', '%s')" % ((cgi.escape(Parent)), (cgi.escape(User)), (cgi.escape(Content)), (cgi.escape(Timestamp))))
            db.query("SELECT * FROM Posts")
            n = db.store_result()
            if n != o:
                dif = True
            else:
                dif = False
    elif Type == 'UPosts' and Content != None:
        db.query("UPDATE Posts SET Content = '@%s' WHERE Author = '@%s' AND Parent = '@%s' AND Timestamp = '@%s'" % ((cgi.escape(Content)),(cgi.escape(User)),(cgi.escape(Parent)),(cgi.escape(Timestamp))))
    elif Type == 'DPosts':
        db.query("DELETE FROM Posts WHERE Author = '@%s' AND Parent = '@%s' AND Timestamp = '@%s'" % ((cgi.escape(User)),(cgi.escape(Parent)),(cgi.escape(Timestamp))))
    elif Type == 'Img' and Img != None:
        db.query("UPDATE User_Base SET DP = '@%s' WHERE Username = '@%s'" % (Img, User))
except _mysql_exceptions.Error as err:
    print("Something went wrong: {0}".format(err))

    
    
