#!/usr/bin/env python

import subprocess
import os
import time
from threading import Thread
from uuid import getnode as get_mac
#from pythonwifi.iwlibs import Wireless

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
    results = subprocess.check_output(["/System/Library/PrivateFrameworks/Apple80211.framework/Versions/A/Resources/airport", "-s"])
    results = results.split()
    print results[8]
    print results[15]
    print results[22]

    mac = get_mac()
    print mac
    time.sleep(30)