<?  /// 권한 체크 : 조회권한 - 메인화면  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_R_HOME_item_history  = permission_ck('HOME입출고현황그래프','R',$_SESSION['admin_role']);  // 그래프 7일 , 30일
	 $pm_R_warehouse        = permission_ck('창고','R',$_SESSION['admin_role']);
 	 $pm_R_angle            = permission_ck('앵글','R',$_SESSION['admin_role']);
	 $pm_R_item		        = permission_ck('제품','R',$_SESSION['admin_role']);
	 $pm_R_history			= permission_ck('HISTORY','R',$_SESSION['admin_role']);
  
     /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('앵글','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "앵글등록권한없음"; }
	 $pm_W_item = permission_ck('제품','W',$_SESSION['admin_role']); if ($pm_W_item == 'F') {  $pm_W_item_button = "display:none;"; $pm_W_item_txt = "권한없음"; }

     /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U2 = permission_ck('창고','U',$_SESSION['admin_role']); if ($pm_rst_U2 == 'F') {  $permission_U2_button = "!"; $permission_U2_txt = "창고수정권한없음"; }
	 $pm_U_item = permission_ck('제품','U',$_SESSION['admin_role']); if ($pm_U_item == 'F') {  $pm_U_item_button = "display:none;"; }
 
  
  
     /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D2 = permission_ck('창고','D',$_SESSION['admin_role']); if ($pm_rst_D2 == 'F') {  $permission_D2_button = "display:none;"; $permission_D2_txt = "<BR>창고삭제권한없음"; }
	   
	   
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('앵글','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = ""; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('앵글','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>앵글삭제권한없음"; }
 	  
	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W3 = permission_ck('제품','W',$_SESSION['admin_role']); if ($pm_rst_W3 == 'F') {  $permission_W3_button = "display:none;"; $permission_W3_txt = "제품등록권한없음"; }


	   /// 권한 체크 : 조회권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	// $pm_rst_R111 = permission_ck('HOME입고현황그래프','R',$_SESSION['admin_role']); if ($pm_rst_R == 'F') {  $permission_R_button = "display:none;"; }
	//   $result_setting = getRTU_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1

 
?>	