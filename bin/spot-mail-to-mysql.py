#!/usr/bin/python
# Hereward Cooper - 06/04/10
# This script checks an IMAP email account for messages with a
# specific subject line. It then parses the header for the date,
# and the body for the longitude and latitude. The date is
# converted into a unix datestamp and inserted into a MySQL database. 
# This is a re-write of the original script which was hacked together in perl.

import imaplib
import time
import email, email.utils
import datetime
import pytz
import MySQLdb
import re

# Import the secret login details!
from login_details import *

# Setup debugging system
DEBUG = False
if DEBUG:
	print "## Debug On ##"

# Connect to MySQL Database
dbconn = MySQLdb.connect (host = DBHOST, user = DBUSER, passwd = DBPASS, db = DB)
cursor = dbconn.cursor ()


# Connect to IMAP Server
imapserver = imaplib.IMAP4_SSL(SERVER)

# Login
imapserver.login(USER, PASSWORD)
imapserver.select('INBOX')

# Find all messages with a certain subject line
typ, message_matches = imapserver.search(None, '(SUBJECT "Check-in/OK message from Coops SPOT Messenger")')

# Split up each matching messages into it's own sub-array
message_ids = message_matches[0].split();
if DEBUG:
	print "Total number of matching messages is: " + str(len(message_ids))
	print "These messages ids are: " + str(message_ids)

#Reset array of messages to delete and the count
todelete = []
count = 0

for id in message_ids:
	typ, message_date = imapserver.fetch(id, '(RFC822.SIZE BODY[HEADER.FIELDS (Date)])')
	date = message_date[0][1].lstrip('Date: ').strip() + ' '

	if DEBUG:
		print id, date

	# Convert the RFC822 format date into a unix epoch time (using pytz)
	utctimestamp = email.Utils.mktime_tz(email.Utils.parsedate_tz( date ))
	date_utc = datetime.datetime.fromtimestamp( utctimestamp, pytz.utc )
	date_unix = time.mktime(date_utc.timetuple())

        # Pull out detail from email body
        typ2, message_body = imapserver.fetch(id, '(BODY[TEXT])')

	# Extract the Longitude and Latitude information
        message_body_lat = re.search('Latitude:.+', message_body[0][1])
        message_body_lng = re.search('Longitude:.+', message_body[0][1])

	# Trim the Longitude and Latitude information
	Lng = message_body_lng.group(0).lstrip('Longitude:').strip()
        Lat = message_body_lat.group(0).lstrip('Latitude:').strip()

	# Perpare the MySQL query, then insert the data
	insert_values = ('OK', Lng, Lat, message_body[0][1], date_unix, '', '', '')
	try:
		cursor.execute('INSERT INTO ' + DBTBL + '(type,lng,lat,msg,time,tag,img,notes) \
			VALUES (%s,%s,%s,%s,%s,%s,%s,%s)' , insert_values)
	except:
		print date + ' -- MySQL Insert Failure, not marking message for removal'
	else:
		# If Mysql query ran fine, add the msgid to the list of messages to delete
		todelete.insert(count, id)

	count = count + 1


#### MESSAGE REMOVAL

# Once all the data handling is done, move the processed messages into the archive

if DEBUG:
	print "Total number of messages to delete is: " + str(len(todelete))
	print "Messsages ids to delete are: " + str(todelete)

# Reverse the order to get round IMAP lameness when deleting messages
todelete.reverse()

# Copy and remove processed messages
for msgid in todelete:
	if DEBUG:
        	print 'Moving message ' + str(msgid)
	imapserver.copy(msgid, 'PROCESSED')
	imapserver.store(msgid, '+FLAGS', '\\Deleted')

imapserver.expunge()


# Close all the usual stuff
cursor.close ()
dbconn.close()
imapserver.close()
imapserver.logout()
