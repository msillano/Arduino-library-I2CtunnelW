#!/usr/bin/python
"""Updates Linux date using REST to get Arduino Yun RTC date-time.

      Requires I2Ctest sketch and RTC (DS1307) on Arduino Yun.""" 
      
##    This program is free software; you can redistribute it and/or modify
##    it under the terms of the GNU General Public License as published by
##    the Free Software Foundation; either version 2 of the License, or
##    (at your option) any later version.
##
##    This program is distributed in the hope that it will be useful,
##    but WITHOUT ANY WARRANTY; without even the implied warranty of
##    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##    GNU General Public License for more details.
##
##    You should have received a copy of the GNU General Public License
##    along with this program; if not, write to the Free Software
##    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
##
##    Copyright (c) 2014 Marco Sillano.  All right reserved.
             
## This example shows the use of I2Ctunnel library to read RTC data and adjust 
## Linux time using python. 
## note:  linux time is set at boot using NTP client, if avalaible 
##    (see LUCI, system/system). 
##
## USE
## Start I2Ctest sketch, start Console, then runt this file from terminal.
## note:  make it executable (chmod +x settimefromrtc.py) and then run it 
##      directly: ./settimefromrtc.py
##
## HARDWARE
## This example requires a RTC (DS1307) on Arduino Yun.
			 
import urllib 
import urllib2
import subprocess

# REST request for I2C
URL = "http://127.0.0.1/arduino/I2C/0x68/B/0/7"
s = urllib2.urlopen(URL).read()

# ssmmhh00DDMMYY raw data (DS1307) to timestamp "YYYY-MM-DD hh:mm:ss"
# 01234567890123  
if len(s) > 13: 
    d="20" + s[12:14] + "-" + s[10:12] + "-" + s[8:10] + " " + s[4:6] + ":" + s[2:4] + ":" + s[:2]
    # updates linux time
    subprocess.call(["date", "-s", d])
