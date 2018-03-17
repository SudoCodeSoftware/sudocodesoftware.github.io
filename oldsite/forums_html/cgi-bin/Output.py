#!/usr/bin/python
#This is the file that the HTML requests data back from
import sys
import cgi
import cgitb; cgitb.enable()
import json
import math
print ("Content-type: text/html\n\n")

# Create instance of FieldStorage 
form = cgi.FieldStorage() 
#Get data
Username = form.getvalue('username')
Type = form.getvalue('type')
relationships = {
        'Acquaintance':[],
        'Friend':[],
        'BF':[],
        'Partner':[],
        'BFFL':[],
        'Spouse':[]}
stats = {
'Care_About_Image':1,
'Care_About_Friends':1,
'Care_About_Smarts':1,
'Care_About_Enlightenment':1,
'Care_About_Culture':1,
'Age':4,
'Appeal':2,
'Creativity':6,
'Culture':3,
'Education':1,
'Energy':10,
'Enlightenment':8,
'Happiness':10,
'Dental':6,
'Muscular':10,
'Respiratory':3,
'Skin':9,
'General_Health':8,
'Height':10,
'Physical_Appeal':9,
'IQ':10,
'Self_Confidence':7,
'Money':6,
'Popularity':8,
'Self_confidence':9,
'Self_Control':8,
'Sense_of_Humour':7,
'Skin_Health':6,
'Smarts':8,
'Social_Skills':5,
'Strength':1,
'Weight':5,
'Acquaintance':len(relationships['Acquaintance']),
'Friend':len(relationships['Friend']),
'BF':len(relationships['BF']),
'Partner':len(relationships['Partner']),
'BFFL':len(relationships['BFFL']),
'Spouse':len(relationships['Spouse'])}

def stat_calc(stats):
    stats['Appeal'] = stats['Social_Skills'] + stats['Physical_Appeal'] + stats['Happiness'] + stats['Creativity']+ stats['Education'] + stats['Sense_of_Humour'] + stats['Enlightenment'] + stats['Money'] + stats['Energy'] + stats['Self_Confidence']*math.fabs(stats['Age']*25)
    stats['Beauty'] = stats['Dental_Health'] + stats['Skin']
    stats['BMI'] = stats['Weight'] / (stats['Height'] ^ 2)
    stats['Happiness'] = stats['Self_Confidence']
    stats['Physical_Appeal'] = stats['Strength'] * math.fabs(stats['BMI']*21) + stats['Beauty']*math.fabs(stats['Age']*24)
    stats['Popularity'] = (math.fsum(relationships['Acquaintance'])) + (2) * (math.fsum(relationships['Friend'])) + (4) * (math.fsum(relationships['BF'])) + (8) * (math.fsum(relationships['Partner'])) + (math.fsum(relationships['BFFL'])) + (16) * (math.fsum(relationships['Spouse']))
    stats['Self_Confidence'] = stats['Care_About_Image'] * (stats['Physical_Appeal']) + stats['Care_About_Friends'] * stats['Popularity'] + stats['Care_About_Smarts'] * stats['Smarts'] + stats['Care_About_Enlightenment'] * stats['Enlightenment'] + stats['Care_About_Culture'] * stats['Culture']
    stats['Sense _of_Humour'] = stats['Culture'] + stats['Smarts'] + stats['Creativity']
    stats['Smarts'] = stats['Education'] * stats['IQ']
    stats['Social Skills'] = stats['Self_confidence'] + stats['Popularity']
    return(stats)

if Type == 'feed':
    print(' \
    <div class="post"> \
            <p2>"Honestly, '+Username+' is kind of a poop." <br> - Nathan Cohen</p2> \
        </div> \
        <br>'
    )
if Type == 'stats':
    print(sys.stdout.write(str(stat_calc(stats))))
