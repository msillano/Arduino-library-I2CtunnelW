#include <Bridge.h>
#include <Console.h>
#include <YunServer.h>
#include <YunClient.h>
#include <Wire.h>
#include <I2CtunnelW.h>
/*
  I2Ctest.ino - Test sketch for IC2-bridge comunications.
  Copyright (c) 2014 Marco Sillano.  All right reserved.

This example shows the use of I2CtunnelW library.
The sketch is universal, it extends REST and can be used with many 
IC2 devices. Clients can be in python, php, javascript...:  examples 
are in www directory.

USE
Compile and start this sketch, start Console, then open one of IC2xxxx.yyy 
files in a browser, or execute settimefromrtc.py from OpenWrt-Yun terminal 
(check execute permission).
Files in /www/sd/I2Ctest/ : 
    I2CEEPROMjscript.html  I2CRTCjscript.html     settimefromrtc.py
    I2CEEPROMphp.php       I2CRTCphp.php          
URLS: http://arduino.local/sd/I2Ctest/I2CRTCjscript.html etc.

note: if Console is not used, you can undefine I2C_DEBUG in I2CtunnelW.h

HARDWARE
This sketch requires some I2C devices on Arduino Yùn:
  - a RTC clock (DS1307) is required to run the IC2RTCxxxx.yyy web pages and 
        for settimefromrtc.py.
  - an EEPROM (AT24C32) is required to run the IC2EEPROMxxx.yyy web pages

SOFTWARE
For the php examples (IC2xxxx.php web pages) php must be installed in
OpenWrt-Yun. On my Arduino, luci says:
 	Package name	    Version
    php5	            5.4.5-3
	  php5-cgi	        5.4.5-3
    php5-mod-json	    5.4.5-3
    php5-mod-session	5.4.5-3
    php5-mod-sockets	5.4.5-3
    php5-mod-sqlite3	5.4.5-3
*/

YunServer server;

// basic setup
void setup() {
  Bridge.begin();
  Console.begin();
  Wire.begin();
  // note: I2Ctunnel don't requires begin() call.
  // Wait for the Console port to connect
   while (!Console);
   Console.println(F("I2C REST tunnel test"));
   Console.println(F(" Now open in a browser one of:"));
   Console.println(F("      I2CEEPROMjscript.html  I2CRTCjscript.html "));
   Console.println(F("      I2CEEPROMphp.php       I2CRTCphp.php  "));
   Console.println(F("   (urls: http://arduino.local/sd/I2Ctest/I2CRTCjscript.html) "));
   Console.println(F(" or execute settimefromrtc.py on the linux terminal"));
   Console.println(F("   (root@Arduino:/mnt/sda1/arduino/www/I2Ctest# python settimefromrtc.py)"));
   Console.println(F(" ------------------------------------- debug log: "));
   server.listenOnLocalhost();
   server.begin();
}

// standard loop for Bridge (see Bridge.ino example)
void loop() {
  // Get clients coming from server
  YunClient client = server.accept();
  // There is a new client?
  if (client) {
    client.setTimeout(5);  // speed-up, see http://forum.arduino.cc/index.php?topic=205303.0
    // Process request
    doCommand(client);
    // Close connection and free resources.
    client.stop();
  }
  delay(20);
}

void doCommand(YunClient client) {
  // read the command
  String commandstr = client.readStringUntil('/');
  // is "I2C" command?
  if (commandstr == "I2C") {
    I2Ctunnel.command(client);  // I2Ctunnel.command() does all the work
  // for debug only
#ifdef I2C_DEBUG 
    Console.println(I2Ctunnel.getLastCommand()); 
#endif	
    } else {
	/* more commands here */
	}
}
/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

