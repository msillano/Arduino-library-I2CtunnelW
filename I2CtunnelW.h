/*
  I2Ctunnel.h - Library for IC2-bridge comunications.
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

#ifndef I2Ctunnel_h
#define I2Ctunnel_h

#include "Arduino.h"
#include <Wire.h>
#include <YunClient.h>

#define  I2C_ADD8   'B'
#define  I2C_ADD16  'W'

// adds the extra function getLastCommand() for debug/trace
#define I2C_DEBUG

class I2Ctunnel_t {
  public:
  void command(YunClient client);
  void write(const byte dev_id, const char size_add, 
       const unsigned int data_add, const byte data[], const byte ndata);
   void read( const byte dev_id, const char size_add, 
       const unsigned int data_add, byte data[], const byte ndata);
 private:
  void startI2C(const byte dev_id, const char size_add, 
          const unsigned int data_add);

#ifdef I2C_DEBUG
  public:
     char* getLastCommand();
  private:
 //   String lastCommand;
     char lastCommand[107];
#endif
};

extern I2Ctunnel_t I2Ctunnel ;

#endif