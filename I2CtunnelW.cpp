/*
  I2Ctunnel.cpp - Library for IC2-bridge comunications.
  Copyright (c) 2014 Marco Sillano.  All right reserved.

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* 
New REST commands, enabling tunnelling for I2C messages:
 /arduino/I2C/<device>/B|W/<address>/<count>/     -> I2C read data (dataHEX)
 /arduino/I2C/<device>/B|W/<address>/<count>/<dataHEX>  -> I2C write data
 
   device:  the device ID, accepts decimal, hex, octal 
                            (max 7 bits: 0x00 to 0x7F or 0-127)
           for DS1307 (RTC)     = 0x68 (104)
		    	 for AT24C32 (EEPROM) = 0x50 (80)

   B|W:     one capital letter, 'B' or 'W'
                 B = the device uses byte address ( see I2Ctunnel.h: I2C_ADD8)
                 W = the device uses word address ( see I2Ctunnel.h: I2C_ADD16)

   address: the start address inside the device,
                                   accepts decimal, hex, octal: byte or word.
           for DS1307 (RTC)     = 0 to 7 or 0x08 to 0x3F (see datasheet) 
		    	 for AT24C32 (EEPROM) = 0x0000 to 0x0FFF     (see datasheet)  

   count:   number of bytes (read or write), accepts only decimal
            note: the limit is 30 bytes

   dataHEX: 2 char HEX ('0'..'F')for every byte to write (or read): 
                 example: "64" for 0x64 (100)
   
   examples:
     /arduino/I2C/0x68/B/0/7           -> reads data and time from DS1307 
     /arduino/I2C/0x50/W/0x0000/2/0064 -> writes 2 bytes 0, 0x64 (100) in 
                                          AT24C32[0000-0001] 

This small footprint library allows to execute some tasks on linux side (using
 python, php, javascript ...) and not inside the arduino sketch. This can be a
 great help, because the Arduino Yùn mem space is limited to  28.672 byte. 
In examples you can see how to set the linux clock from RTC, or how to adjust 
 the RTC clock from a WEB page, or how put and get a string in EEPROM.
   
USE: see example I2Ctest.ino and clients in python, php and Javascript 
           in I2Ctest/www/ directory.   
note: "#define I2C_DEBUG" (in I2Ctunnel.h) adds the extra function  
      getLastCommand() for debug/trace: you get a string like:
        " R: /arduino/I2C/0x68(104)/B/0x0(0)/7 data: 38081103070714" or
        " W: /arduino/I2C/0x50(80)/W/0x0(0)/11/48656c6c6f20776f726400"
note: tested using YUN and DS1307, AT24C32 - The code is optimized for 
      smallest footprint.
*/


#include "I2CtunnelW.h"

// main method, does all REST work
   void I2Ctunnel_t::command(YunClient client) { 
// ============== parsing REST commad   
   uint8_t  device, count;
   uint16_t address;
   char asize[2]="B";
  String dx = client.readStringUntil('/'); //device: accept decimal,hex,octal
  device  =  (uint8_t)strtol(dx.c_str(), NULL, 0);
  asize[0] = client.read();                   // address size: oneof 'W', 'B' 
  client.read();                           // '/' char
  dx = client.readStringUntil('/');    //address: accept decimal, hex, octal
  address  = (uint16_t) strtol(dx.c_str(), NULL, 0);
  count = client.parseInt();           // count: accept only decimal, max 127
#ifdef I2C_DEBUG
  strcpy(lastCommand,  " W: /arduino/I2C/");
  strcat(strcat(lastCommand, "0x"),String(device, 16).c_str());
  strcat(strcat(strcat(lastCommand,"("), String(device).c_str()),")/");
  strcat(strcat(strcat(lastCommand, asize),"/0x"),String(address, 16).c_str());
  strcat(strcat(strcat(lastCommand,"("), String(address).c_str()),")/"); 
  strcat(lastCommand, String(count).c_str());
  #endif
// ========= executing I3C command
  uint8_t* bindata = (uint8_t*)malloc(count * 2); // temporay data buffer
  if ( client.available() && (client.read() == '/')) {  
// ======== more HEXdata: write
    char tbuff[] = {0, 0, 0};
#ifdef I2C_DEBUG
    strcat(lastCommand,"/");
#endif
    for (uint8_t i = 0; i < count; i++) {       // HEXstring to byte[]
      tbuff[0] = client.read();
      tbuff[1] = client.read();
#ifdef I2C_DEBUG
      strcat(lastCommand,tbuff);
#endif
      bindata[i] = (uint8_t)strtol(tbuff, NULL, 16);
    }
    write(device, asize[0], address, bindata, count);    // do I2C write
 }
  else {                                             
// ============= no HEXdata: read
#ifdef I2C_DEBUG
	lastCommand[1] = 'R';
    strcat(lastCommand, " data: ");	
#endif
    read(device, asize[0], address, bindata, count);    // do I2C read
	
    for (int i = 0; i < count; i++) {             // byte[] to HEXstring
	   if (bindata[i] < 16){
	     client.print('0');
#ifdef I2C_DEBUG
          strcat(lastCommand, "0");	
#endif
	   }
     String dHex = String(bindata[i], 16);
	 client.print(dHex);
//       client.print(bindata[i], HEX);
#ifdef I2C_DEBUG
    strcat(lastCommand, dHex.c_str());	
#endif
    }
    client.print('\n');
  }
  free(bindata);  // free temporay data buffer
 }

#ifdef I2C_DEBUG
 // for debug only
   char* I2Ctunnel_t::getLastCommand(){
   return (lastCommand);
   }
#endif

void I2Ctunnel_t::startI2C(const byte dev_id, const char size_add, 
          const unsigned int data_add){
  Wire.beginTransmission(dev_id);
  if (size_add == I2C_ADD16) {
    Wire.write(highByte(data_add ));
    }   
  Wire.write(lowByte(data_add));
  }
  
// low level write I2C, can be used in arduino sketch to do local I2C tasks
 void I2Ctunnel_t::write(const byte dev_id, const char size_add, 
          const unsigned int data_add, const byte data[], const byte ndata) {
  startI2C(dev_id, size_add, data_add); 
  for (uint8_t i = 0; i < ndata; i++) {
    Wire.write(data[i]);
  }
  Wire.endTransmission();
}

// low level read I2C, can be used in arduino sketch to do local I2C tasks
 void I2Ctunnel_t::read(const byte dev_id, const char size_add,
               const unsigned int data_add, byte data[], const byte ndata) {
  startI2C(dev_id, size_add, data_add); 
  Wire.endTransmission();
  Wire.requestFrom(dev_id, ndata);
    for (uint8_t  i = 0; i < ndata; i++) {
    data[i] = Wire.read();
  }
}
// Preinstantiate Object ////////////////////////////////////////////////

I2Ctunnel_t I2Ctunnel;
