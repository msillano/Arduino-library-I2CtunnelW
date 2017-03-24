# Arduino-library-I2CtunnelW

For Arduino Yùn, this library adds 2 new REST commands, enabling tunnelling for I2C messages:

-  /arduino/I2C/&lt;device>/B|W/&lt;address>/&lt;count>/              -> I2C read data (dataHEX)
-  /arduino/I2C/&lt;device>/B|W/&lt;address>/&lt;count>/&lt;dataHEX>  -> I2C write data

This library allows to execute some tasks on linux side (using python, php, javascript ...) and not inside the arduino sketch. This can be a great help, because the Arduino Yùn sketch space is limited to  28.672 byte. 
In examples you can see how to set the linux clock from RTC, or how to adjust the RTC clock from a WEB page, or how put and get a string in EEPROM.

note: tested using Arduino YUN and DS1307, AT24C32 - The code is optimized for smallest footprint.

This library uses Wire library (https://www.arduino.cc/en/reference/wire)

The analog ![I2Ctunnel](https://github.com/msillano/Arduino-library-I2Ctunnel) uses I2C library (https://github.com/rambo/I2C)




