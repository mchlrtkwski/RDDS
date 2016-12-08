#!/usr/bin/env python

import subprocess
import os
import time
from NetworkNode import NetworkNode
from threading import Thread
from uuid import getnode as get_mac
#from pythonwifi.iwlibs import Wireless
import math

from socket import *
#from socket import socket, AF_INET, SOCK_RAW


##########################################################################
#
#
#
#
#public double calculateDistance(double signalLevelInDb, double freqInMHz) {
#    double exp = (27.55 - (20 * Math.log10(freqInMHz)) + Math.abs(signalLevelInDb)) / 20.0;
#    return Math.pow(10.0, exp);
#}
#
#
#
##########################################################################

while 1:
    networkData = subprocess.check_output(["/System/Library/PrivateFrameworks/Apple80211.framework/Versions/A/Resources/airport", "-s"])
    networkData = networkData.split()
    networkData = networkData[8:]
    numberOfNetworks = len(networkData)/7
    SSID_List = []
    RSSI_List = []
    for x in range(0, numberOfNetworks):
        node = NetworkNode(networkData[x * 7], networkData[(x * 7) + 2])
        SSID_List.append(node)



    print SSID_List[0].isDrone()
    print SSID_List[0].isNearby()
    #print RSSI_List[0]
    mac = get_mac()
    #print mac
    time.sleep(30)