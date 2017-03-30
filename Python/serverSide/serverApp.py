#!/usr/bin/env python

import socket
import sys
import MySQLdb
#import mysql.connector
from thread import start_new_thread
from automateEmail import sendAlert

HOST = '' # all availabe interfaces
PORT = 5005 # arbitrary non privileged port

#db = MySQLdb.connect(host='127.0.0.1', user="root", passwd="", db="rdds", unix_socket="/opt/lamp/var/mysql/mysql.sock")
#mariadb_connection = mariadb.connect(user='root', password='', host='127.0.01', database='rdds')
#cursor = db.cursor()
#sql = "SELECT * FROM users \
 #      WHERE email = 'mchlrtkwski@gmail.com"
#cle
try:
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
except socket.error, msg:
    print("Could not create socket. Error Code: ", str(msg[0]), "Error: ", msg[1])
    sys.exit(0)

print("[-] Socket Created")

# bind socket
try:
    s.bind((HOST, PORT))
    print("[-] Socket Bound to port " + str(PORT))
except socket.error, msg:
    print("Bind Failed. Error Code: {} Error: {}".format(str(msg[0]), msg[1]))
    sys.exit()

s.listen(10)
print("Listening...")

# The code below is what you're looking for ############

def client_thread(conn):
    conn.send("Welcome to the Server. Type messages and press enter to send.\n")

    while True:
	
        data = conn.recv(1024)
	db = MySQLdb.connect(host='127.0.0.1', user="root", passwd="", db="rdds", unix_socket="/opt/lamp/var/mysql/mysql.sock")
	cursor = db.cursor()
	sql = "SELECT alertMethod,phone,carrier,email FROM users WHERE deviceID = '" + data + "'"
        cursor.execute(sql)
	results = cursor.fetchall()
	prefMethod = ""
	phoneNumber = ""
	carrier = ""
	email = ""
	for row in results:
	   prefMethod = row[0]
	   phoneNumber = row[1]
	   phoneNumber = phoneNumber + row[2]
	   email = row[3]
	
	if (prefMethod == "Text"):
	   sendAlert(phoneNumber)
	elif (prefMethod == "Email"):
	   sendAlert(email)
	#else if (prefMethod == "Both"):
	 #  cursor = db.cursor()
	  # sql = "SELECT phone,carrier,email FROM users WHERE deviceID = '" + data + "'"
           #cursor.execute(sql)
	   #results = cursor.fetchall()
        #sql = "SELECT * FROM users WHERE deviceID = '" + data + "'"
	#cursor.execute(sql)
	#results = cursor.fetchone()
	#sendAlert("mchlrtkwski@gmail.com")
       # print results
	db.close()
        if not data:
            break
        reply = "OK . . " + data
        conn.sendall(reply)
    conn.close()
    print "end connections"

while True:
    # blocking call, waits to accept a connection
    conn, addr = s.accept()
    print("[-] Connected to " + addr[0] + ":" + str(addr[1]))

    start_new_thread(client_thread, (conn,))

s.close()

