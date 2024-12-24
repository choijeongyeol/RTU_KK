function popup_win(arg) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no, height=400, width=600, left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}
 
function moveto (arg){
	location.href = arg;
} 
 
function popup_out_win(arg) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no, height=400, width=600, left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}
 
function popup_win400_400(arg) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no, height=400, width=400, left='+ popupX + ', top='+ popupY);
}
function popup_win400_260(arg) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no, height=260, width=400, left='+ popupX + ', top='+ popupY);
}

function popup_win_size_warehouse(arg,w,h) {
	 
	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no,  height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}


function popup_win(arg1,arg2) {
 
	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2,  '', 'status=no, height=400, width=400, left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}


function popup_win3(arg1,arg2) {
	
	//alert("sdfdsf");

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2,  '', 'status=no, height=400, width=400, left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}


function popup_win_size(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
 
}

function popup_win_size_angle(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}



function popup_win(arg1,arg2,arg3,arg4) {
 

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3+'&arg4='+arg4, '', 'status=no, height=300, width=500, left='+ popupX + ', top='+ popupY);
	//var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3+'&arg4='+arg4, '자식 창', 'width=400,height=300');
 
}

function popup_win_in(arg1,arg2,arg3,arg4) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m03/ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3+'&arg4='+arg4, '', 'status=no, height=300, width=500, left='+ popupX + ', top='+ popupY);
	//var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3+'&arg4='+arg4, '자식 창', 'width=400,height=300');
	
	top.location.reload();
 
}

function popup_win_inreg(arg1,arg2) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m03/ctrl_'+arg1+'_input.php?arg2='+arg2, '', 'status=no, height=500, width=500, left='+ popupX + ', top='+ popupY);
	//var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3+'&arg4='+arg4, '자식 창', 'width=400,height=300');
	
	top.location.reload();
 
}

function popup_win_stock_move(arg1,item_id,angle_id,warehouse_id,quantity) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?item_id='+item_id+'&angle_id='+angle_id+'&warehouse_id='+warehouse_id+'&max_cnt='+quantity, '', 'status=no, height=300, width=500, left='+ popupX + ', top='+ popupY);
}


function popup_win_stock_move_stock(arg1,stock_id,angle_id,warehouse_id,quantity) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?stock_id='+stock_id, '', 'status=no, height=300, width=500, left='+ popupX + ', top='+ popupY);
}



function popup_winm05(arg) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '', 'status=no, height=800, width=1000, left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}


function goto_step_1(arg){
	if (arg=="Y")
	{
		popup_form.step_1.value = "Y";
		popup_form.save_YN.value = arg;
		popup_form.submit();		
	}else{
		popup_form.step_1.value = "N";
		popup_form.step_2.value = "N";
		popup_form.step_3.value = "Y";
		popup_form.save_YN.value = "N";
		popup_form.submit();		
	}

}	

function fn_step_1(arg){
	popup_form.to_ware_v.value = arg;
	popup_form.save_YN.value = "Y";
	popup_form.step_1.value = "Y";
	popup_form.step_2.value = "Y";
	popup_form.submit();	
}

function fn_step_2(arg){
	popup_form.to_ware_v.value = document.getElementById("to_ware").value;
	popup_form.to_angle_v.value = document.getElementById("to_angle").value;
	//popup_form.to_company_v.value = document.getElementById("to_company").value;
	popup_form.save_YN.value = "Y";
	popup_form.step_1.value = "Y";
	popup_form.step_2.value = "Y";
	popup_form.step_3.value = "Y";
	popup_form.submit();	
}

function fn_step_2_2(arg){ // 제품이동  재고관리 > 재고목록(창고 내부)
	popup_form.to_ware_v.value = document.getElementById("to_ware").value;
	popup_form.to_angle_v.value = document.getElementById("to_angle").value;
 	popup_form.save_YN.value = "Y";
	popup_form.step_1.value = "Y";
	popup_form.step_2.value = "Y";
	popup_form.step_3.value = "Y";
	popup_form.submit();	
}
 
function fn_step_3(arg){
	popup_form.to_ware_v.value = document.getElementById("to_ware").value;
	popup_form.to_angle_v.value = document.getElementById("to_angle").value;
	popup_form.to_company_v.value = document.getElementById("to_company").value;
	popup_form.save_YN.value = "Y";
	popup_form.step_1.value = "Y";
	popup_form.step_2.value = "Y";
	popup_form.step_3.value = "Y";
	popup_form.submit();	
}
 
 
 
function order_up_angle(arg1,arg2,arg3) {
 
    var iframe = document.getElementById('myIframe');
    var url = '/gn/inc/iframe_angle_up.php?warehouse_id='+arg1+'&angle_id='+arg2+'&angle_order='+arg3;
    iframe.src = url;
	location.href="/gn/m02/list.php";
}

 
function order_down_angle(arg1,arg2,arg3) {
 
    var iframe = document.getElementById('myIframe');
    var url = '/gn/inc/iframe_angle_down.php?warehouse_id='+arg1+'&angle_id='+arg2+'&angle_order='+arg3;
    iframe.src = url;
	location.href="/gn/m02/list.php";
}


function popup_win_angle_update(arg1,arg2,arg3,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2+'&arg3='+arg3,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}



function popup_win_angle_del(arg1,arg2,arg3,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2+'&arg3='+arg3,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

function popup_win_productlist_in_angle(arg1,arg2,arg3,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2+'&arg3='+arg3,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

function popup_win_m01(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
	 

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
 
}

function popup_win_m01_reg(arg,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음
	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음
 
	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg+'_input.php',  '','status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);

	// 부모 창에서 자식 창으로 데이터 전달
	if (childWindow) {
		var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
		childWindow.postMessage(dataToSend, '*');
	}
}


function fn_user_cate(arg1,arg2,arg3){  //   W,1,  {$user['access_id']}//{$user['access_name']}//W//{$cate['cate_admin_role']}

 	str =document.getElementById("user_cate_"+arg1+arg2).value;
	var arr_str = str.split("//");
   
    var url = '/gn/inc/iframe.php?mode='+arg1+'&num='+arg2+'&m04_access_id='+arr_str[0]+'&m04_access_name='+arr_str[1]+'&m04_access_type='+arr_str[2]+'&m04_access_value='+arr_str[3];
 
     var iframe = document.getElementById('myIframe');	
     iframe.src = url;
     location.href="/gn/s02/list.php";	
}

function fn_cate_change(arg1,arg2){
     var url;	
	if (arg1=="x")
	{
		alert("변경할 항목을 선택하세요");
		return false;
	}else{
		url = '/gn/inc/iframe.php?cate_change=Y&item_cate='+arg1+'&item_id='+arg2;	
	}
     var iframe = document.getElementById('myIframe');	
     iframe.src = url;
	 alert("변경되었습니다.");
	 top.location.reload();
     //location.href="/gn/m03/list.php";		
}



function fn_user_cate_del(arg1,arg2,arg3){  //   1,W,99
    
     var url = '/gn/inc/iframe.php?access_id='+arg1+'&access_type='+arg2+'&role='+arg3;
 
     var iframe = document.getElementById('myIframe');	
     iframe.src = url;
     location.href="/gn/s02/list.php";	
}



function popup_win_inbound_update(arg1,arg2,w,h,plandate) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m05/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2+'&plandate='+plandate,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

function popup_win_outbound_update(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m06/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}


function popup_win_inbound_del(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m05/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}


function popup_win_outbound_del(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m06/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

 

function popup_win_warehouse_update(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

 
 
function popup_win_warehouse_del(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m02/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}

 
 
function popup_win_3(arg1,arg2,arg3) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg2='+arg2+'&arg3='+arg3,   '', 'status=no, height=400, width=600, left='+ popupX + ', top='+ popupY);
}



 
function popup_win_product_del(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}


function popup_win_item_update(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('/gn/m03/ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}


 
function popup_win_cate_del(arg1,arg2,w,h) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}



function popup_win_sys_update(arg1,arg2,w,h,col) {

	var popupX = (document.body.offsetWidth / 2) - (200 / 2);
	// 만들 팝업창 좌우 크기의 1/2 만큼 보정값으로 빼주었음

	var popupY= (window.screen.height / 2) - (300 / 2);
	// 만들 팝업창 상하 크기의 1/2 만큼 보정값으로 빼주었음

	// 자식 창을 열고 창 객체를 저장
	var childWindow = window.open('ctrl_'+arg1+'_input.php?arg1='+arg1+'&arg2='+arg2+'&col='+col,  '', 'status=no, height='+h+', width='+w+', left='+ popupX + ', top='+ popupY);
}


function sendit_loc(){
	var form = document.getElementById("searchform");	
	var h_location_select = document.getElementById("h_location_select").value;	
	form.h_location_hidden.value = h_location_select.substring(3);	
	
	var h_loc_code = document.getElementById("h_location_select").value;	
	form.h_loc_code_hidden.value = h_loc_code.slice(0, 3);	
	
	//alert(form.h_loc_code_hidden.value);
	form.submit();	
}

