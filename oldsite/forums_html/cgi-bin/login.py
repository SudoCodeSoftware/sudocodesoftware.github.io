#!/usr/bin/python
import cgi
import cgitb; cgitb.enable()


# Create instance of FieldStorage 
form = cgi.FieldStorage() 

# Get fb access token
access_token = form.getvalue('at')

print ("Content-type: text/html\n\n")
print("")

#Check if access token is valid


if access_token != '':
    valid = True
else:
    valid = False


#Response appropriatley
if valid:
    response = 2
else:
    response = 1

print(response)