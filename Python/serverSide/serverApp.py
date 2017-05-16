#!/usr/bin/env python
import socket
import sys
import MySQLdb
from time import gmtime,strftime
from thread import start_new_thread
from automateEmail import sendAlert

HOST = ''
PORT = 5005

try:
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
except socket.error, msg:
    print("ERROR")
    sys.exit(0)

    try:
        s.bind((HOST, PORT))
    except socket.error, msg:
        print("ERROR")
        sys.exit()

        s.listen(10)
        print("Listening...")

        def client_thread(conn):

            data = conn.recv(1024)
            if (data != ""):
                #Find User by the sent MAC address
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
                #Use Given Information to Send
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
                        print "Connection Finished"

                        #Use Threading to allow multiple connections.
                        while True:
                            conn, addr = s.accept()
                            print("[-] Connected to " + addr[0] + ":" + str(addr[1]))

                            start_new_thread(client_thread, (conn,))

                            s.close()
