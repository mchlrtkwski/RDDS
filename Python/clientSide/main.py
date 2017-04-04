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
    #networkData = subprocess.check_output(["/System/Library/PrivateFrameworks/Apple80211.framework/Versions/A/Resources/airport", "-s"])
    networkDataNames = subprocess.check_output(["nmcli", "-t", "-f", "SSID", "dev", "wifi"])
    networkDataStrength = subprocess.check_output(["nmcli", "-t", "-f", "SIGNAL", "dev", "wifi"])
    networkDataMACA = subprocess.check_output(["nmcli", "-t", "-f", "BSSID", "dev", "wifi"])
    #networkData = networkData.split()
    #networkData = networkData[8:
    networkDataNames = networkDataNames.split()
    networkDataStrength = networkDataStrength.split()
    numberOfNetworks = len(networkDataNames)
    SSID_List = []
    RSSI_List = []

    #create NetworkNode objects to
    for x in range(0, numberOfNetworks):
        node = NetworkNode(networkDataNames[x], networkDataStrength[x])
        SSID_List.append(node)

    #Verify if an alert should be made
    for x in range(0, numberOfNetworks):
        print SSID_List[x].SSID
        print SSID_List[x].isDrone()
        print SSID_List[x].isNearby()
        if SSID_List[x].isDrone() and SSID_List[x].isNearby():
            alertUser = True
        print alertUser

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