#!/usr/bin/env python

import subprocess
import time
from NetworkNode import NetworkNode
from uuid import getnode as get_mac
from socket import *

##########################################################################
#
#
#
#
#
#
#
#
##########################################################################

#Initalize Constants
MAC = str(get_mac())
TCP_IP = '127.0.0.1'
TCP_PORT = 5005
BUFFER_SIZE = 1024

while 1:

    alertUser = False

    #Obtain all terminal output for parsing
    networkData = subprocess.check_output(["/System/Library/PrivateFrameworks/Apple80211.framework/Versions/A/Resources/airport", "-s"])

    networkData = networkData.split()
    networkData = networkData[8:]
    numberOfNetworks = len(networkData)/7
    SSID_List = []
    RSSI_List = []

    #create NetworkNode objects to
    for x in range(0, numberOfNetworks):
        node = NetworkNode(networkData[x * 7], networkData[(x * 7) + 2])
        SSID_List.append(node)

    #Verify if an alert should be made
    for x in range(0, numberOfNetworks):
        print SSID_List[x].SSID
        print SSID_List[x].isDrone()
        print SSID_List[x].isNearby()
        if SSID_List[x].isDrone() or SSID_List[x].isNearby():
            alertUser = True

    #Create a TCP connection to the server and send identification number
    if alertUser:
        s = socket(AF_INET, SOCK_STREAM)
        s.connect((TCP_IP, TCP_PORT))
        s.send(MAC)
        data = s.recv(BUFFER_SIZE)
        s.close()
        print "received data:", data

    #Time Delay in seconds
    time.sleep(30)