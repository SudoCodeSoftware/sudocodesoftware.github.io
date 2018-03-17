#!/usr/bin/python
import _mysql
import _mysql_exceptions
import cgi
import cgitb; cgitb.enable()
import time
import hashlib
import random

# Password Hash
def MD5hash(string):
    m = hashlib.md5()
    m.update(string.encode('utf-8'))
    return m.hexdigest()

# Create instance of FieldStorage 
form = cgi.FieldStorage() 

# Get data from fields
Username = form.getvalue('username')
Password = form.getvalue('password')

print ("Content-type: text/html\n\n")
print("""
<html>
<!-- HEAD -->
    <head>
        <title>Sudo-Code Software</title>
        <link rel="shortcut icon" href="../favicon.ico" />
        <link rel='stylesheet' type='text/css' href='../forumsLayout.css' />
        <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <!-- Placeholder for header stuff like CSS, JS and other headerly shit -->
        <script src="jquery.min.js" type="application/javascript"></script>
        <script src="typed.js"></script>
</head>
""")

#Attempt at MySQL DataBase connection

try:
    db=_mysql.connect(user="sudocom",host="localhost", db="sudocom" ,passwd="gate+Grace*civic") 
    db.query("SELECT * FROM User_Base WHERE Username = '%s' " % (Username))
    r = db.store_result()
    user_det = r.fetch_row(0,1)
    if user_det != ():
        if Password == None:
            print("Incorrect")
            print("""
            <script>
            function start() {
                    alert('Lol Password Wrong')
                    var url = "../login.html";    
                    window.location = url;
                }
            </script>
            """)
        elif user_det[0]['Password'] == MD5hash(Password):
            Access_Token = random.randint(99999, 999999)
            db.query("UPDATE User_Base SET Access_Token = '%s' WHERE Username = '%s'" %(Access_Token, user_det[0]['Username']))
            print("Welcome %s!" % (Username))
            print("""
            <script>
            function start() {
                    document.cookie="username=%s; path=/";
                    document.cookie="at=%s; path=/";
                    window.location =  "../index.html";
            }
            </script>
            """) % (user_det[0]['Username'], Access_Token)
        else:
            print("Incorrect")
            print("""
            <script>
            function start() {
                    alert('Lol Password Wrong')
                    var url = "../login.html";    
                    window.location = url;
                }
            </script>
            """)
    else:
        print("No user called '%s'" % (Username))
except _mysql_exceptions.Error as err:
    print("Something went wrong: {0}".format(err))
print("""
<body onload="start()">
<a href="http://sudo-code.com">
    <img class="logo" src='../res/LOGO.png' alt='logo' height='12%' href="http://sudo-code.com">
</a>
</body>
</html>
""")
