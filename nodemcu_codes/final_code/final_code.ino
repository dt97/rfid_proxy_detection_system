#include <SPI.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#define pirPin1 15//For first pir pin weak one to be placed at front when facing out of class and connected to d8 pin of nodemcu
#define pirPin2 5//For second pir pin more sensitive one to be placed at front when facing into the class and connected to d1 pin of nodemcu
char input[12];
//String tid = "5800A9334785";
char *ssid = "Connectify-dt"; //  your network SSID (name) 
char *password = "chillout";    // your network password (use for WPA, or use as key for WEP)
int count = 0;
String orfid = "";//to store just previous rfid value
String rfid = "";//to store rfid value scanned and send to php code
String c_rfid = "";//course rfid initially null if not null means teacher has entered the class by showing the rfid card
String mark = "";//To keep track of students showing their rfid cards more than once towards the em18 rfid sensor to give proxy
HTTPClient http;//Declare an object of class HTTPClient for sending http requests to php page for database updation
int httpCode = 0;//initially no http connection so http code intialized to 0
String httppage;//for sending url of http page
String request;//for http request
boolean alock = true;//for attendance lock i.e. only after mark is received and set properly attendance can be uploaded or proxy detected
boolean classlock = true;
boolean exit_status = false;
boolean lockLow1 = true;
boolean lockLow2 = true;
boolean motion1 = false;//to indicate motion detected by pir sensor1(weak pir sensor), initally no motion detected so set as false
boolean motion2 = false;//to indicate motion detected by pir sensor1, initally no motion detected so set as false
unsigned int tlag = 0;//to measure time lag between successive rfid cards detected in order to detect proxy, if tlag<2s then there is chance of proxy  
unsigned int otime = 0;//to store old time i.e. last time when a rfid card was detected
unsigned int cur_time = 0;//to store current time when a rfid card was detected
//unsigned int ontime1 = 0;
//unsigned int ontime2 = 0; 
int PIRValue1 = 0;
int PIRValue2 = 0;
 
void setup()
{
  Serial.begin(9600);//Standard baud rate for serial communication between nodemcu and server
  //To initialize the digital pins as i/p or o/p
  pinMode(pirPin1, INPUT);
  pinMode(pirPin2, INPUT);
  //To connect to wifi network
  Serial.println();//print garbage values if any
  Serial.print("Attempting to connect to ");
  Serial.println(ssid);
  WiFi.mode(WIFI_STA); // <<< Define client as Station so that it doesn't act as server itself
  WiFi.begin(ssid, password);//to begin connection with wifi network/hotspot on laptop
  while(WiFi.status()!=WL_CONNECTED)
  {
    delay(500);//wait at intervals of 500ms or 0.5s
    Serial.print(".");
  }
  Serial.println();//to start printing from newline after connection is successful
  Serial.print("Connected to network:");
  Serial.print(WiFi.SSID());
  Serial.print(" with client ipaddress:");
  Serial.println(WiFi.localIP().toString());//to display network configuration of connected network
}
String getrfid()
{
  String op = "";
  if(Serial.available())
  {
    count = 0;
    while(Serial.available() && count < 12)//Read 12 characters and store them in input array
    {
      input[count] = Serial.read();
      count++;
      delay(5);
    } 
    if((input[0]^input[2]^input[4]^input[6]^input[8]==input[10]) && (input[1]^input[3]^input[5]^input[7]^input[9]==input[11]))
    {
      //op = String(input);
      int i = 0;
      for(i=0; i<12; i++)
      {
        op = op+String(input[i]);
      }
      Serial.print("Successfully detected rfid tag:");
      Serial.println(String(op));//Print RFID tag number 
    }
    else
      Serial.println("Error in detecting rfid card");      
  }
  return op;
}
void get_c_rfid(String rfid, String payload)
{
  if(payload!="null")//if we recieved some string at payload other than null 
  {
    String res = "";
    int i = 0;
    for(i=0; i<12; i++)
    {
      res += String(payload[i]);
    }  
    if(res==rfid)//Match found in courses table in attendance database
    {
      c_rfid = res;
      Serial.println("Match found in courses table in attendance database");
      Serial.print("Course rfid for this class is: ");
      Serial.println(c_rfid);
    }
    else
    {
      Serial.println("Error in connection to database... Please try again!!!");
    }
  }
}
void get_mark(String payload)
{
  if(payload!="null")//if we recieved some string at payload other than null 
  {
    String res = "";
    int i = 0;
    for(i=0; i<1; i++)
    {
      res += String(payload[i]);
    }
    if(res=="1" || res=="0")//as mark can either be 0 or 1 so this part of code checks if mark of a student is received correctly or not
    {
      mark = res;
      Serial.println("Match found in attendance database");
      Serial.print("Mark for student with rfid ");
      Serial.print(rfid);
      Serial.print(" is ");
      Serial.println(mark);
    }
    else
    {
      Serial.println("Error in receiving mark of student from attendance database");
    }
  }
}
void check_teacher_entry()
{
  rfid = getrfid();
  Serial.print("Recieved rfid:");
  Serial.println(rfid);
  Serial.println();
  if(rfid!="")//if rfid detected successfully
  {
    Serial.println("\nStarting connection to server via httpclient object..."); 
    //if you get a connection, report back via serial:
    Serial.println("Attempting to connect to server via httpclient object");
    if(WiFi.status()==WL_CONNECTED) 
    { 
      //Check WiFi connection status
      httppage = "http://192.168.87.1/pd_lab/get_crfids_to_node_mcu.php?c_rfid=";//Specify request destination 
      request = httppage+rfid;
      http.begin(request); 
      httpCode = http.GET();//Send the request
      if (httpCode > 0) 
      { 
        //Check the returning code
        Serial.println("HTTP Request to server successful. Payload recieved...");
        String payload = http.getString();   //Get the request response payload
        Serial.println(payload);                     //Print the response payload
        get_c_rfid(rfid, payload);//to check if course rfid is matched or not and then assign it to c_rfid for door locking purpose
        if(c_rfid!="")//if course rfid is recieved i.e. teacher entered
        {
          classlock = false;
          Serial.print("Teacher entered class with course rfid = ");
          Serial.println(c_rfid);
          Serial.println("Class started...");
          httppage = "http://192.168.87.1/pd_lab/reset_marks.php?c_rfid=";//Specify request destination 
          request = httppage+rfid;
          http.begin(request); 
          httpCode = http.GET();//Send the request
          if(httpCode>0)
          {
            String payload = http.getString();   //Get the request response payload
            Serial.println(payload);                     //Print the response payload  
          }
          else
          {
            Serial.println("HTTPClient GET Request failed for resetting mark of all students");    
          }
        }
      }
      else
      {
        Serial.println("HTTPClient GET Request failed");
      }
      http.end();   //Close HTTP connection with server
    }
    else
    {
      Serial.println("WiFi disconnected");       
    }
    delay(1000);//wait for 1000 ms or 1s
  }
  else//rfid detection failed
  {
    Serial.println("RFID Scan failed retrying...");
    //return;
  }
  delay(2000);//wait for 2s between readings of rfids may or may not be required
}
void check_teacher_exit()
{
  rfid = getrfid();
  Serial.print("Recieved rfid:");
  Serial.println(rfid);
  Serial.println();
  if(rfid==c_rfid)
  {
    exit_status = true; 
    Serial.println("Teacher exits. Attendance begins");
  }
  else
  {
    Serial.println("Teacher not yet exited. Class going on!!!");
  }
  delay(1000);//wait for 1s between readings of rfids may or may not be required
} 
void upload_attendance()
{
  rfid = getrfid();
  Serial.print("Recieved rfid:");
  Serial.println(rfid);
  Serial.println();
  if(rfid!="")//if rfid detected successfully
  {
    alock = true;
    if(WiFi.status()==WL_CONNECTED)
    {
      //to get mark of the student with the given rfid card
      httppage = "http://192.168.87.1/pd_lab/get_marks.php?c_rfid=";//Specify request destination 
      request = httppage+c_rfid+"&s_rfid="+rfid;
      http.begin(request); 
      httpCode = http.GET();//Send the request
      if(httpCode>0)
      {
        //Check the returning code
        Serial.println("HTTP Request to server successful. Payload recieved...");
        String payload = http.getString();   //Get the request response payload
        Serial.println(payload);             //Print the response payload
        get_mark(payload);
        if(mark=="0")
        {
          if(WiFi.status()==WL_CONNECTED)
          {
            //to set mark of the student with the given rfid card
            httppage = "http://192.168.87.1/pd_lab/set_marks.php?c_rfid=";//Specify request destination 
            request = httppage+c_rfid+"&s_rfid="+rfid;
            http.begin(request); 
            httpCode = http.GET();//Send the request
            //To update mark of the student with given rfid
            if(httpCode>0)
            {
              //Check the returning code whether mark is updated successfully or not
              Serial.println("HTTP Request to server successful. Payload recieved...");
              alock = false;//only after set_mark is successful attendance can be uploaded
              String payload = http.getString();   //Get the request response payload
              Serial.println(payload);             //Print the response payload
            }
            else
            {
              Serial.println("HTTPClient GET Request failed");
            }
            http.end();   //Close HTTP connection with server
          }
          else
          {
            Serial.println("Error in wifi conncetion");      
          } 
          delay(200);//wait for 200ms or 1s
          if(alock==false)//now attendance can be uploaded after successful updation and verificatio of mark
          {
            //To upload check for proxy and suitably update attendance of the student with the given rfid
            cur_time = millis();
            tlag = cur_time-otime;//gives time lag between 2 successive rfid cards detection for efficient proxy detection
            otime = cur_time;
            Serial.println(tlag);
            if((motion1==true && motion2==true) || (tlag>=4000))//tlag>=4000ms or both motion1 and motion2 are true means successful attendance upload
            {
              if(rfid!=orfid)
              {
                Serial.println("No proxy upload attendance...");
                Serial.println("\nStarting connection to server via httpclient object..."); 
                //if you get a connection, report back via serial:
                Serial.println("Attempting to connect to server via httpclient object");
                if(WiFi.status()==WL_CONNECTED) 
                { 
                  //Check WiFi connection status
                  httppage = "http://192.168.87.1/pd_lab/postdata_to_phpmyadmin.php?c_rfid=";//Specify request destination 
                  request = httppage+c_rfid+"&s_rfid="+rfid;
                  http.begin(request); 
                  httpCode = http.GET();//Send the request
                  if(httpCode>0) 
                  { 
                    //Check the returning code
                    Serial.println("HTTP Request to server successful. Payload recieved...");
                    String payload = http.getString();   //Get the request response payload
                    Serial.println(payload);                     //Print the response payload+
                  }
                  else
                  {
                    Serial.println("HTTPClient GET Request failed");
                  }
                  http.end();   //Close HTTP connection with server
                }
                else
                {
                  Serial.println("WiFi disconnected");       
                }
                delay(500);//wait for 500ms or 1s  
              }
              else
              {
                Serial.println("Oops!!! Same rfid card detected");       
              }
              orfid = rfid;
            }
            else
            { 
              Serial.print("Proxy detected for rfid no: ");
              Serial.println(rfid);
            }  
          }
        }
        else if(mark=="1")
        {
          Serial.println("Oops!!!No attendance for you because you have already been marked...");
        }
        else if(mark=="")
        {
          Serial.print("Failure to fetch mark of student with rfid ");
          Serial.println(rfid);
        }
      }
      else
      {
        Serial.println("HTTPClient GET Request failed");
      }
      http.end();   //Close HTTP connection with server
    }
    else
    {
      Serial.println("Error in wifi conncetion");
    }
    delay(200);//wait for 200ms or 1s
  }
  else//rfid detection failed
  {
    Serial.println("RFID Scan failed retrying...");
    //return;
  }
  delay(1000);//wait for 1s between readings of rfids may or may not be required   
}
void PIRSensor1()
{
  if(digitalRead(pirPin1)==HIGH)
  {
    if(lockLow1)
    {
      PIRValue1 = 1;
      lockLow1 = false;
      //Serial.println("Motion detected at sensor1/weak sensor");
      motion1 = true;
      //delay(50);
    }
  }
  if(digitalRead(pirPin1)==LOW)
  {
    if(!lockLow1)
    {
      PIRValue1 = 0;
      lockLow1 = true;
      //Serial.println("Motion ended at sensor1/weak senser");
      motion1 = true;
      //delay(50);
    }
  }
}
void PIRSensor2()
{
  if(digitalRead(pirPin2)==HIGH)
  {
    if(lockLow2)
    {
      PIRValue2 = 1;
      lockLow2 = false;
      //Serial.println("Motion detected at sensor2");
      motion2 = true;
      //delay(50);
    }
  }
  if(digitalRead(pirPin2)==LOW)
  {
    if(!lockLow2)
    {
      PIRValue2 = 0;
      lockLow2 = true;
      //Serial.println("Motion ended at sensor2");
      motion2 = true;
      //delay(50);
    }
  }
}
void check_sensor_status()
{
  Serial.println("------------------Sensor status check begins from 2nd pir sensor to weak one--------------------------");
  PIRSensor2();//As person has to pass through the 2nd pir sensor first while giving attendance
  delay(50);//wait for 50ms
  PIRSensor1();//1 for weak pir sensor
  Serial.println("------------------Sensor status check ends----------------------");
}
void loop()
{
  while(classlock==true)
  {
    check_teacher_entry();
    //till teacher doesn't enter nothing will happen like an infinite loop
  }
  while(exit_status==false)
  {
    check_teacher_exit();
    //till teacher doesn't exit nothing will happen like an infinite loop
  }
  //After teacher exits then only attendance is uploaded
  check_sensor_status();
  /*if(motion1==true && motion2==false)//if after teacher exit i.e. while attendance is going on if someone enters to the class then stop attendance upload as such movement during attendance is invalid
  {
    //buzzer alarm
    while(1);
  }*/
  do{
    //Upload attendance
    upload_attendance(); 
  }while((millis()-cur_time)<=300000);//continue uploading attendance till time interval between successive RFID detection is <= 3s assuming after that nobody else is there in the room
  motion1 = false;//reintialize motion1 and motion2 for next iteration of void loop()
  motion2 = false;
}
