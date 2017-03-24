<!DOCTYPE html>
<!--
  I2CEEPROMphp.php - Example client for IC2tunnel comunications.
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
-->
<!--
This example shows the use of I2Ctunnel library to read/write EEPROM data
using php. 

USE
Start I2Ctest sketch, start Console, then open this file in a browser.

HARDWARE
This example requires an EEPROM (AT24C32) on Arduino Yùn.

SOFWARE
php must be installed in OpenWrt-Yùn.

-->
<html>
	<head>      
<!-- uses  zepto -->  
<script type="text/javascript" src="zepto.min.js"></script>

 <?php
//  I2Ctunnel for DS1307 using php
$theint = '????';
$thestring   = '....';   

  
if (isset($_GET['action'])){
   if ($_GET['action'] == 'saveUInt'){  
      saveUInt();
                
      } elseif ($_GET['action'] == 'getUInt'){ 
      $theint = getUInt();              

      } elseif ($_GET['action'] == 'saveString'){ 
      saveString();
          
     } elseif ($_GET['action'] == 'getString'){ 
      $thestring = getString();    
   } 
} else {       
 }
 
 
// ------------------------------------------------    

function saveUInt(){           
  // char dec|hex  to int
  eval('$val =  intval('.$_GET['newInt'].');');   
  // int to charHEX[4] string
  $x ='0000'.dechex($val);
  $x = substr($x, -4);    
  // REST command for I2C EEPROM
  $xcommand = '/arduino/I2C/0x50/W/0x0020/2/'.$x;
 _yunbridge($xcommand); 
}  

function getUInt() { 
  //  get UInt from RTC using I2C tunnelling
  $i2cdata =  _yunbridge('/arduino/I2C/0x50/W/0x0020/2');
  // char HEX to int
  return( hexdec( $i2cdata));
}         

function getString(){  
  $i2cdata =  _yunbridge('/arduino/I2C/0x50/W/0x0000/30');
   $data = "";
     for($i= 0; $i<30; $i++) {
       $s = "0x".$i2cdata[$i*2].$i2cdata[($i*2)+1];
        // from string like "0x48" to int
       $x = intval($s,16);
       if ($x == 0) {
           return($data);
        }
        // from Int to String
        $data .= chr($x);
     }
    return($data);
}

function saveString(){  
  $val =  $_GET['newString'];    
  $val = substr($val,0,29); 
  $n = strlen($val);   
  $data = bin2hex($val);
  $data.= "00";      
  $xcommand = '/arduino/I2C/0x50/W/0x0000/'.($n+1).'/'.$data;
 _yunbridge($xcommand); 
 }

// bridge REST access
function _yunbridge($command){  
  return  file_get_contents('http://127.0.0.1'.$command);
}       
?>
</head>                                   
<body bgcolor='lime'>  
    <h1> I2C REST tunnel for EEPROM using php</h1>
	<hr color="050000">
    <form action="I2CEEPROMphp.php" method="get">
      <table width=100% border=0 summary="">
     <tr>
		<td colspan=2>Writing an UInt to address 0x0020 (0-65535 or 0x0-0xFFFF ) </td>
	</tr>
     <tr>                                                  
		<td width=200><input type="text" name="newInt" size="5" maxlength="256">
</td>       
		<td>
        <input type="hidden" name="action" value="saveUInt">
        <input type="submit" value="UPDATE"> </td>
	</tr> 
     </table></form>
    <hr color="050000">
    
       <form action="I2CEEPROMphp.php" method="get">
      <table width=100% border=0 summary="">

    <tr>
		<td colspan=2>Reading an UInt from address 0x0020</td>
	</tr>
     <tr>
		<td width=200><span id="aInt"><?php echo $theint ; ?></span></td>       
		<td>        
        <input type="hidden" name="action" value="getUInt">
        <input type="submit" value="REFRESH"> </td>

	</tr>  
      </table></form>
    <hr color="050000">
    
       <form action="I2CEEPROMphp.php" method="get">
      <table width=100% border=0 summary="">

          <tr>
		<td colspan=2>Writing a String to address 0x0000 (max 29 char) </td>
	</tr>
     <tr>                                                  
		<td width=200><input type="text" name="newString" size="40" maxlength="256">
</td>       
		<td>        
        <input type="hidden" name="action" value="saveString">
        <input type="submit" value="UPDATE"> </td>

	</tr>  
      </table></form>
    <hr color="050000">
    
       <form action="I2CEEPROMphp.php" method="get">
      <table width=100% border=0 summary="">

    <tr>
		<td colspan=2>Reading a String from address 0x0000</td>
	</tr>
     <tr>
		<td width=200><span id="aString"><?php echo $thestring; ?></span></td>       
		<td>        
        <input type="hidden" name="action" value="getString">
        <input type="submit" value="REFRESH"> </td>

	</tr>  
      </table></form>
    <hr color="050000">
    
    	</body>
</html>
         