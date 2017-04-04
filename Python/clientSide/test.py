import subprocess
import time
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
TCP_IP = '184.57.165.207'
TCP_PORT = 5005
BUFFER_SIZE = 1024

s = socket(AF_INET, SOCK_STREAM)
s.connect((TCP_IP, TCP_PORT))
s.send("1233455")
#data = s.recv(BUFFER_SIZE)
s.close()
#print "received data:", data
