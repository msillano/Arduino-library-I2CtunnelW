<!DOCTYPE html>
<!--
  I2CERTCphp.php - Example client for IC2tunnel comunications.
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
This example shows the use of I2Ctunnel library to read/write RTC date/time
using php. 

USE
Start I2Ctest sketch, start Console, then open this file in a browser.

HARDWARE
This example requires a RTC (DS1307) on Arduino Yùn.

SOFWARE
php must be installed in OpenWrt-Yun.

-->

<html>
	<head>      
<!-- uses  zepto -->
 <?php
//  I2Ctunnel for DS1307 using php
$timestamp_linux = 'YYYY-MM-DD hh:mm:ss';
$timestamp_i2c   = 'YYYY-MM-DD hh:mm:ss';   

ob_start();               

if (isset($_GET['action'])){
   if ($_GET['action'] == 'linuxtime'){  
      $timestamp_linux = getLinuxTimestp();
                
      } elseif ($_GET['action'] == 'i2ctime'){ 
      $timestamp_i2c = geti2ctimestamp();              

      } elseif ($_GET['action'] == 'i2ctolinux'){ 
      setLinuxTimeFromRTC();
          
     } elseif ($_GET['action'] == 'i2cadjust'){ 
      setRTCtime();    
   } 
} else {       
  $timestamp_linux = getLinuxTimestp();   
  $timestamp_i2c   = geti2ctimestamp();
}
 
ob_end_clean();                 

// ------------------------------------------------    

function getLinuxTimestp(){
//  get TimeStamp from OpenWrt-Yun
$today =  system('date \'+%Y-%m-%d %H:%M:%S\'');
return ($today);
}                  

function geti2ctimestamp(){
//  get TimeStamp from RTC using I2C tunnelling
  $i2cdata =  _yunbridge('/arduino/I2C/0x68/B/0/7');
// asciiHEX (packed bcd) to timeStamp
  $timestp = "20".$i2cdata[12].$i2cdata[13].'-' ;   
  $timestp .= $i2cdata[10].$i2cdata[11].'-';                                           
  $timestp .= $i2cdata[8].$i2cdata[9].' ';                                           
  $timestp .= $i2cdata[4].$i2cdata[5].':';                                           
  $timestp .= $i2cdata[2].$i2cdata[3].':';                                           
  $timestp .= $i2cdata[0].$i2cdata[1] ;   
  return $timestp;       
}                         

function setLinuxTimeFromRTC(){
// set OpenWrt-Yun date-time from RTC
  $nowt =  geti2ctimestamp();
  system('date -s "'.$nowt.'"'); 
}    

function setRTCtime(){    
// set RTC time
  $xcommand = '/arduino/I2C/0x68/B/0/3/'.$_GET['second'].$_GET['minute'].$_GET['hour'];
 _yunbridge($xcommand); 
}

// bridge REST access
function _yunbridge($command){  
  return  file_get_contents('http://127.0.0.1'.$command);
}       
?>
	</head>
	<body bgcolor='lime'>  
    <h1> I2C REST tunnel for RTC using php</h1>
    <hr color="050000">
    <form action="I2CRTCphp.php" method="get">
      <table width=100% border=0 summary="">
	    <tr>
		<td colspan=2>Linux time:</td>
	    </tr> 
     <tr>
		<td width=200><?php echo $timestamp_linux; ?></td>  
        <td>  
        <input type="hidden" name="action" value="linuxtime">
        <input type="submit" value="REFRESH"> </td>
	</tr>  
     </table>   
  </form>     
        <hr color="050000">
  <form action="I2CRTCphp.php" method="get">
     <table width=100% border=0 summary="">
	   <tr>
		<td colspan=2>arduino RTC time:</td>
	</tr> 
     <tr>
		<td width=200><?php echo $timestamp_i2c; ?></td>  
        <td>  
        <input type="hidden" name="action" value="i2ctime">
        <input type="submit" value="REFRESH"> </td> </tr>  
     </table>   
  </form>     
        <hr color="050000">
  <form action="I2CRTCphp.php" method="get">
     <table width=100% border=0 summary="">
	   <tr>
		<td width=200>Adjust linux time from RTC</td>  
        <td>  
        <input type="hidden" name="action" value="i2ctolinux">
        <input type="submit" value="UPDATE"> </td> </tr>  
     </table>   
  </form>     
        <hr color="050000">
  <form action="I2CRTCphp.php" method="get">
     <table width=100% border=0 summary="">
	        <td> 
                   <tr>
		<td colspan=2>Adjust RTC time on arduino:</td>
	</tr>
  <tr>
		<td width=200>&nbsp;<select name="hour">
                   	<option value="00"> 00</option>
                   	<option value="01"> 01</option>
                   	<option value="02"> 02</option>
                   	<option value="03"> 03</option>
                   	<option value="04"> 04</option>
                  	<option value="05"> 05</option>
                   	<option value="06"> 06</option>
                   	<option value="07"> 07</option>
                   	<option value="08"> 08</option>
                   	<option value="09"> 09</option>
                   	<option value="10"> 10</option>
                   	<option value="11"> 11</option>
                   	<option value="12"> 12</option>
                   	<option value="13"> 13</option>
                   	<option value="14"> 14</option>
                  	<option value="15"> 15</option>
                   	<option value="16"> 16</option>
                   	<option value="17"> 17</option>
                   	<option value="18"> 18</option>
                   	<option value="19"> 19</option>
                   	<option value="20"> 20</option>
                   	<option value="21"> 21</option>
                   	<option value="22"> 22</option>
                   	<option value="23"> 23</option>
                       </select>
     :<select name="minute">
                   	<option value="00"> 00</option>
                   	<option value="01"> 01</option>
                   	<option value="02"> 02</option>
                   	<option value="03"> 03</option>
                   	<option value="04"> 04</option>
                  	<option value="05"> 05</option>
                   	<option value="06"> 06</option>
                   	<option value="07"> 07</option>
                   	<option value="08"> 08</option>
                   	<option value="09"> 09</option>
                   	<option value="10"> 10</option>
                   	<option value="11"> 11</option>
                   	<option value="12"> 12</option>
                   	<option value="13"> 13</option>
                   	<option value="14"> 14</option>
                  	<option value="15"> 15</option>
                   	<option value="16"> 16</option>
                   	<option value="17"> 17</option>
                   	<option value="18"> 18</option>
                   	<option value="19"> 19</option>
                 	<option value="20"> 20</option>
                   	<option value="21"> 21</option>
                   	<option value="22"> 22</option>
                   	<option value="23"> 23</option>
                   	<option value="24"> 24</option>
                  	<option value="25"> 25</option>
                   	<option value="26"> 26</option>
                   	<option value="27"> 27</option>
                   	<option value="28"> 28</option>
                   	<option value="29"> 29</option>
                   	<option value="30"> 30</option>
                   	<option value="31"> 31</option>
                   	<option value="32"> 32</option>
                   	<option value="33"> 33</option>
                   	<option value="34"> 34</option>
                  	<option value="35"> 35</option>
                   	<option value="36"> 36</option>
                   	<option value="37"> 37</option>
                   	<option value="38"> 38</option>
                   	<option value="39"> 39</option>
                 	<option value="40"> 40</option>
                   	<option value="41"> 41</option>
                   	<option value="42"> 42</option>
                   	<option value="43"> 43</option>
                   	<option value="44"> 44</option>
                  	<option value="45"> 45</option>
                   	<option value="46"> 46</option>
                   	<option value="47"> 47</option>
                   	<option value="48"> 48</option>
                   	<option value="49"> 49</option>
                   	<option value="50"> 50</option>
                   	<option value="51"> 51</option>
                   	<option value="52"> 52</option>
                   	<option value="53"> 53</option>
                   	<option value="54"> 54</option>
                  	<option value="55"> 55</option>
                   	<option value="56"> 56</option>
                   	<option value="57"> 57</option>
                   	<option value="58"> 58</option>
                   	<option value="59"> 59</option>
                       </select>
                   
           :<select name="second">
                   	<option value="00"> 00</option>
                   	<option value="01"> 01</option>
                   	<option value="02"> 02</option>
                   	<option value="03"> 03</option>
                   	<option value="04"> 04</option>
                  	<option value="05"> 05</option>
                   	<option value="06"> 06</option>
                   	<option value="07"> 07</option>
                   	<option value="08"> 08</option>
                   	<option value="09"> 09</option>
                   	<option value="10"> 10</option>
                   	<option value="11"> 11</option>
                   	<option value="12"> 12</option>
                   	<option value="13"> 13</option>
                   	<option value="14"> 14</option>
                  	<option value="15"> 15</option>
                   	<option value="16"> 16</option>
                   	<option value="17"> 17</option>
                   	<option value="18"> 18</option>
                   	<option value="19"> 19</option>
                 	<option value="20"> 20</option>
                   	<option value="21"> 21</option>
                   	<option value="22"> 22</option>
                   	<option value="23"> 23</option>
                   	<option value="24"> 24</option>
                  	<option value="25"> 25</option>
                   	<option value="26"> 26</option>
                   	<option value="27"> 27</option>
                   	<option value="28"> 28</option>
                   	<option value="29"> 29</option>
                   	<option value="30"> 30</option>
                   	<option value="31"> 31</option>
                   	<option value="32"> 32</option>
                   	<option value="33"> 33</option>
                   	<option value="34"> 34</option>
                  	<option value="35"> 35</option>
                   	<option value="36"> 36</option>
                   	<option value="37"> 37</option>
                   	<option value="38"> 38</option>
                   	<option value="39"> 39</option>
                 	<option value="40"> 40</option>
                   	<option value="41"> 41</option>
                   	<option value="42"> 42</option>
                   	<option value="43"> 43</option>
                   	<option value="44"> 44</option>
                  	<option value="45"> 45</option>
                   	<option value="46"> 46</option>
                   	<option value="47"> 47</option>
                   	<option value="48"> 48</option>
                   	<option value="49"> 49</option>
                   	<option value="50"> 50</option>
                   	<option value="51"> 51</option>
                   	<option value="52"> 52</option>
                   	<option value="53"> 53</option>
                   	<option value="54"> 54</option>
                  	<option value="55"> 55</option>
                   	<option value="56"> 56</option>
                   	<option value="57"> 57</option>
                   	<option value="58"> 58</option>
                   	<option value="59"> 59</option>
                       </select>
                   
      
  </td>    <td>
        <input type="hidden" name="action" value="i2cadjust">
        <input type="submit" value="UPDATE"> </td> </tr>  
     </table>   
  </form>     
        <hr color="050000">


  
   	</body>   
    
</html>

