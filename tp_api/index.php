<? session_start();
if ($_SESSION['partner_id']=="") {
	 header("Location: login.php"); exit(); 
}else{
	require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/tp_api/rtu_config.php');	
}	 
?>
<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>
 </head>
 <body>
  
 <span style="color:red;font-size:20px;">tp API Test list</span><br>
 <a href="<? echo $default_Url;?>/tp_api/tp_api_test.php" target="_blank"><? echo $default_Url;?>/tp_api/tp_api_test.php</a>
 <HR>
 
  <span style="color:red;font-size:20px;">tp API Link</span><br>
  <a href="<? echo $default_Url;?>/tp_api/app_api.php" target="_blank"><? echo $default_Url;?>/tp_api/app_api.php?function=&lTid=</a><br>
 <HR>
  
  <span style="color:red;font-size:20px;">node_info</span><br>
  <a href="<? echo $default_Url;?>/tp_api/app_api.php?function=node_info&lTid=<? echo $LTID;?>" target="_blank"><? echo $default_Url;?>/tp_api/app_api.php?function=node_info&lTid=<? echo $LTID;?></a><br>
 {"status":200,"message":"Node info retrieved successfully.","data":{"ty":"14","ri":"ND00000000000000965898","rn":"<? echo $LTID;?>","pi":"CB00000000000000001014","ct":"2021-12-06T13:49:32+09:00","lt":"2023-12-11T15:28:36+09:00","ppt":{"nsid":"nw\/ds0016"},"ni":"<? echo $LTID;?>","hcl":"RC00000000000001033061","mga":"MQTT|<? echo $LTID;?>"}}
 <HR>
 
  
  <span style="color:red;font-size:20px;">리모트 CSE 정보</span><br>
  <a href="<? echo $default_Url;?>/tp_api/app_api.php?function=remote_cse_info&lTid=<? echo $LTID;?>" target="_blank"><? echo $default_Url;?>/tp_api/app_api.php?function=remote_cse_info&lTid=<? echo $LTID;?></a><br>
 {"status":200,"message":"Remote CSE info retrieved successfully.","data":{"ty":"16","ri":"RC00000000000001033061","rn":"<? echo $LTID;?>","pi":"CB00000000000000001014","ct":"2021-12-06T13:49:32+09:00","lt":"2021-12-06T13:49:32+09:00","acpi":"AP00000000000001930923 AP00000000000001930924","cst":"3","csi":"<? echo $LTID;?>","rr":"true","nl":"ND00000000000000965898"}}
 <HR>
 
  
  <span style="color:red;font-size:20px;">최신 데이터 조회</span><br>
  <a href="<? echo $default_Url;?>/tp_api/app_api.php?function=latest_data&lTid=<? echo $LTID;?>" target="_blank"><? echo $default_Url;?>/tp_api/app_api.php?function=latest_data&lTid=<? echo $LTID;?></a><br>
{"status":200,"message":"Latest data retrieved successfully.","data":{"ty":"4","ri":"CI00000000056445339183","rn":"CI00000000056445339183","pi":"CT00000000000000965459","ct":"2024-09-12T17:23:57+09:00","lt":"2024-09-12T17:24:11+09:00","ppt":{"gwl":"37.455479,126.897711,0","geui":"f4d9fbfffe80e04d","devl":"37.455479,126.897711,0","fp":"2","trid":{},"plidx":"0","ctype":"11","fixType":"8","result":"0","accuracy":"3000"},"et":"2024-09-13T17:23:57+09:00","st":"26986","cr":"RC00000000000001033061","cnf":"LoRa\/Sensor","cs":"10","con":"1406010039","containerCurrentByteSize":"539098"}}
<HR>
  
   <span style="color:red;font-size:20px;">Device Reset</span><br>
   <a href="<? echo $default_Url;?>/tp_api/put_reset.php" target="_blank"><? echo $default_Url;?>/tp_api/put_reset.php</a>
  <div class="responseArea">Response:
{"status":200,"message":"Device reset successfully.","data":{"ty":"12","ri":"MC00000000000003860825","rn":"<? echo $LTID;?>_DevReset","pi":"CB00000000000000001014","ct":"2021-12-06T13:49:32+09:00","lt":"2021-12-06T13:49:32+09:00","cmt":"DevReset","exe":"false","ext":"ND00000000000000965898"}}</div>
  <HR>
 
    <span style="color:red;font-size:20px;"> subscription Create</span><br>
   <a href="<? echo $default_Url;?>/tp_api/sub_create.php" target="_blank"><? echo $default_Url;?>/tp_api/sub_create.php</a>
  <div class="responseArea">{"status":201,"message":"Subscription created successfully.","data":{"ty":"23","ri":"SS00000000000009489622","rn":"<?echo $subscription_key;?>","pi":"CT00000000000000965459","ct":"2024-09-19T15:51:49+09:00","lt":"2024-09-19T15:51:49+09:00","enc":{"rss":"1"},"nu":"HTTP|http:\/\/43.200.77.82:80","nct":"2"}}
</div>
  <HR>
 
 
 
 
  <span style="color:red;font-size:20px;">subscription Retrieve</span><br>
  <a href="<? echo $default_Url;?>/tp_api/app_api.php?function=getRetrieve_Subscription&lTid=<? echo $LTID;?>&subscription_1=<?echo $subscription_key;?>" target="_blank"><? echo $default_Url;?>/tp_api/app_api.php?function=getRetrieve_Subscription&lTid=<? echo $LTID;?>&subscription_1=<?echo $subscription_key;?></a>
  <div class="responseArea">{"status":200,"message":"Remote CSE info retrieved successfully.","data":{"ty":"23","ri":"SS00000000000009487895","rn":"<?echo $subscription_key;?>","pi":"CT00000000000000965459","ct":"2024-09-12T12:36:26+09:00","lt":"2024-09-12T12:36:26+09:00","enc":{"rss":"1"},"nu":"HTTP|http:\/\/43.200.77.82:80","nct":"2"}}</div>
  <HR>
 
  <span style="color:red;font-size:20px;">subscription Update</span><br>
  <a href="<? echo $default_Url;?>/tp_api/put_sub_update.php" target="_blank"><? echo $default_Url;?>/tp_api/put_sub_update.php</a>
  <div class="responseArea">{"status":200,"message":"Subscription update successfully","data":{"ty":"23","ri":"SS00000000000009487895","rn":"<?echo $subscription_key;?>","pi":"CT00000000000000965459","ct":"2024-09-12T12:36:26+09:00","lt":"2024-09-13T15:42:02+09:00","enc":{"rss":"1"},"nu":"HTTP|http:\/\/43.200.77.82:80","nct":"2"}}</div>
  <HR>

  <span style="color:red;font-size:20px;">subscription Delete</span><br>
  <a href="<? echo $default_Url;?>/tp_api/del_sub_delete.php" target="_blank"><? echo $default_Url;?>/tp_api/del_sub_delete.php</a>
  <div class="responseArea"> </div>
  <HR>

 
 
 
 
 
 </body>
</html>
