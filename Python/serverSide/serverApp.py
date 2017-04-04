#!/usr/bin/env python

import socket
import sys
import MySQLdb
from time import gmtime,strftime
from thread import start_new_thread
from automateEmail import sendAlert

HOST = '' # all availabe interfaces
PORT = 5005 # arbitrary non privileged port

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

def client_thread(conn):

    data = conn.recv(1024)
    if (data != ""):

        print data
        givenDevice = data
        db = MySQLdb.connect(host='127.0.0.1', user="root", passwd="", db="rdds", unix_socket="/opt/lamp/var/mysql/mysql.sock")
        cursor = db.cursor()
        sql = "SELECT alertMethod,phone,carrier,email,log FROM users WHERE deviceID = '" + givenDevice + "'"
        cursor.execute(sql)
        results = cursor.fetchall()
        prefMethod = ""
        phoneNumber = ""
        carrier = ""
        email = ""
        log = ""
        for row in results:
            prefMethod = row[0]
            phoneNumber = row[1]
            phoneNumber = phoneNumber + row[2]
            email = row[3]
            log = row[4]

        if (prefMethod == "Text"):
           sendAlert(phoneNumber)
        elif (prefMethod == "Email"):
           sendAlert(email)
        elif (prefMethod == "Both"):
           sendAlert(phoneNumber)
           sendAlert(email)
        log = log + "<tr><td>"+ strftime("%m/%d/%Y", gmtime()) + "</td><td>" + strftime("%H:%M", gmtime()) + "</td></tr>"

        sql = "UPDATE users SET log = \'" + log + "\' WHERE deviceID = \'" + str(givenDevice) + "\'"
        cursor.execute(sql)
        db.commit()
        db.close()

    conn.close()
    print "end connections"

while True:
    # blocking call, waits to accept a connection
    conn, addr = s.accept()
    print("[-] Connected to " + addr[0] + ":" + str(addr[1]))

    start_new_thread(client_thread, (conn,))

s.close()
