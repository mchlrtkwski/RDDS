import math
class NetworkNode:
    def __init__(self, ssid, rssi, bssid):
        self.SSID = ssid
        self.RSSI = rssi
        self.BSSID = bssid
    def isDrone(self):
        return ((str(self.SSID).lower()).find(("drone")) == 0)
    def getBSSID(self):
        return BSSID
    def isNearby(self):
        #print int(self.RSSI)
        VIOLATION_LIMIT = 300
        #currentDistance = 400
        currentDistance = 23
        #((27.55 - (20 * math.log10(2400)) + math.fabs(int(self.RSSI))) / 20.0)
        #currentDistance = math.pow(10.0, currentDistance)
        #print currentDistance
        return currentDistance < VIOLATION_LIMIT

