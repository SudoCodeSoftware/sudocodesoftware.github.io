#!/usr/bin/python


import _mysql
import _mysql_exceptions
import cgi
import cgitb; cgitb.enable()
import hashlib

# Password Hash
def MD5hash(string):
    m = hashlib.md5()
    m.update(string.encode('utf-8'))
    return m.hexdigest()

# Create instance of FieldStorage 
form = cgi.FieldStorage() 

# Get data from fields
Username = form.getvalue('username')
Email    = form.getvalue('email')
Password = form.getvalue('password')
Password = MD5hash(Password)

print ("Content-type: text/html\n\n")
print("""<h1>Welcome, your credentials are as follows
    <br>
      Username: %s 
    <br>  
      Email: %s 
    <br>  
      Password: %s </h1>""" % (Username, Email, Password))  
print ("""
<html>
<head>
<title>Sudo-Code Software</title>
        <link rel="shortcut icon" href="../favicon.ico" />
        <link rel='stylesheet' type='text/css' href='../layout.css' />
        <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <!-- Placeholder for header stuff like CSS, JS and other headerly shit -->
        <script src="jquery.min.js" type="application/javascript"></script>
            <script src="typed.js"></script>

</head>
<body>
<h1>
""")

#Attempt at MySQL DataBase connection
try:
    db=_mysql.connect(user="sudocom",host="localhost", db="sudocom" ,passwd="gate+Grace*civic") 
    db.query("INSERT INTO User_Base (User_ID, Email, Username, Password) VALUES (NULL, '@%s', '@%s', '@%s')" % (Email, Username, Password))
except _mysql_exceptions.Error as err:
    print("Something went wrong: {0}".format(err))

print ("""
<br>
<a href="../index.html">Back To Forums</a>
</h1>
</body>
</html>
""")
