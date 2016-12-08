import math
class NetworkNode:
    def __init__(self, ssid, rssi):
        self.SSID = ssid
        self.RSSI = rssi
    def isDrone(self):
        return (str(self.SSID).lower()).find(("drone")) == True
    def isNearby(self):
        VIOLATION_LIMIT = 300
        currentDistance = ((27.55 - (20 * math.log10(2400)) + math.fabs(int(self.RSSI))) / 20.0)
        currentDistance = math.log10(currentDistance)
        print currentDistance
        return currentDistance < VIOLATION_LIMIT
