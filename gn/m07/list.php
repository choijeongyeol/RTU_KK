<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('HISTORY','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('HISTORY조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('HISTORY','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "HISTORY등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('HISTORY','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "HISTORY수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('HISTORY','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>HISTORY삭제권한없음"; }

    
	?>	
	
		
	<!-- 게시판 리스트 계산 start -->
	 <?
	 
	 if ($_GET['h_location']!="") {		 $h_location = $_GET['h_location']; 	 }else{											 
		 if ($_POST['h_location']!="") { $h_location = $_POST['h_location'];	 }
	 }
	 
	 if ($_GET['h_loc_code']!="") {		 $h_loc_code = $_GET['h_loc_code']; 	 }else{												 
		 if ($_POST['h_loc_code']!="") { $h_loc_code = $_POST['h_loc_code'];	}
	 }
		$add_condition_menu_cate = " and h_loc_code = '".$h_loc_code."'";
	?>
													
	
	<?
	// 검색결과 추가조건 sql
	if ($_POST['search_name']!="") {  	$add_condition =" and ".$_POST['search_name']." = '".$_POST['SearchString']."'";  
		if ($_POST['search_name']=="h_name") { $name_txt_color ="style='color:#ff0000'";}else{ $name_txt_color ="";	 }	
		if ($_POST['search_name']=="h_id")   { $id_txt_color ="style='color:#ff0000'";}else{ $id_txt_color ="";	 }	
	}else{
		$add_condition = "";
	}
	
	$add_condition = $add_condition.$add_condition_menu_cate;  // 검색추가 쿼리
	
	if ($_POST['search_location']!=""){		$add_condition = $add_condition." and h_location = '".$_POST['search_location']."'"; $menu_txt_color ="style='color:#ff0000'";}else{$menu_txt_color="";}
	if ($_POST['search_action']!=""){	$add_condition = $add_condition." and h_action = '".$_POST['search_action']."'"; $action_txt_color ="style='color:#ff0000'";}else{$action_txt_color="";}
	if ($_POST['search_ip']!=""){		$add_condition = $add_condition." and h_ip = '".$_POST['search_ip']."'"; $ip_txt_color ="style='color:#ff0000'";}else{$ip_txt_color="";}
	if ($_POST['date_s']!=""){		$add_condition = $add_condition." and date(h_date) >= '".$_POST['date_s']."'"; $date_txt_color ="style='color:#ff0000'";}else{$date_txt_color="";}
	if ($_POST['date_e']!=""){		$add_condition = $add_condition." and date(h_date) <= '".$_POST['date_e']."'"; $date_txt_color ="style='color:#ff0000'";}else{$date_txt_color="";}
	
 
	$list_condition = "wms_history where 1=1 ".$add_condition;
 
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>	
	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->
	
	<script>
		function search_box(){
			
		   var form = document.getElementById("searchform");
 
           var selectedValue = document.getElementById("search_name").value;
           var name_id_Value = document.getElementById("SearchString").value;
		   
           //var selected_location = document.getElementById("search_location").value;
           var selected_action = document.getElementById("search_action").value;
           var selected_ip = document.getElementById("search_ip").value;
		   
		   if ((selectedValue=="")&&(name_id_Value!="")){
				document.getElementById("SearchString").value = "";
				alert("이름 또는 아이디를 먼저 선택하세요");
				return false();
		   }			 
		   if ((selectedValue!="")&&(name_id_Value=="")){
				if (selectedValue=="h_name")
				{
					alert("이름을 입력하세요");
				}else{
					alert("아이디를 입력하세요");
				}
				document.getElementById("SearchString").focus();				
				return false();
		   }
		   
		   if ((selectedValue=="")&&(name_id_Value=="")&&(form.date_s.value=="")&&(form.date_e.value=="")&&(selected_action=="")&&(selected_ip=="")) {
			   alert("최소한 한개이상의 검색 입력(선택) 후 검색하여주세요");
			   return false();
		   }
			 
		    form.submit();   
		}
	</script>
	
	
	

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <!-- <div class="title_left">
                <h3> <small> </small></h3>
              </div> -->
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>HISTORY관리 > <?echo $h_location;?><small></small></h2> 
 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  HISTORY를 조회하실 수 있습니다.
								</p>
									
								<form method="post" id="searchform" action="<?echo $_SERVER['PHP_SELF']?>" name="searchform" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
								
								 [검색옵션] <BR>
									<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info"  >
									<tr style="background:#eee">
										<td> 
											<div style="text-align:left;display:inline">
											<!-- 이름/아이디 검색   -->
												 <div class="col-md-6 col-sm-6" >
													 <select name="search_name"  class="form-control" id="search_name">
													<option value="" >이름 또는 아이디 선택</option>
													<option value="h_name" <? if ($_POST['search_name']=="h_name") { echo "selected"; }?> >이름</option>
													<option value="h_id" <? if ($_POST['search_name']=="h_id") { echo "selected"; }?>  >아이디</option>
												</select> 
												 </div>
											 
												 <div class="col-md-6 col-sm-6">
													 <input id="SearchString" class="form-control" class='date' type="text"  name="SearchString"  placeholder="이름 / 아이디 선택후 입력하세요" <? if ($_POST['SearchString']!="") { echo 'value="'.$_POST['SearchString'].'"'; }?>>
												 </div>
											</div>	
										</td>
										
										<td> 
											<div style="text-align:left;display:inline">
											<!-- 기간검색 -->
												 <div class="col-md-6 col-sm-6" >
													검색시작날짜<input class="form-control" class='date' type="date" name="date_s" <? if ($_POST['date_s']!="") { echo 'value="'.$_POST['date_s'].'"'; }?> >
												 </div>
											 
												 <div class="col-md-6 col-sm-6">
													검색끝날짜<input class="form-control" class='date' type="date" name="date_e" <? if ($_POST['date_e']!="") { echo 'value="'.$_POST['date_e'].'"'; }?> >
												 </div>
											</div>	
										</td>
 
									</tr>
									<tr style="background:#eee">
										<td> 
											<div style="text-align:left;display:inline;justify-content: center;">
											<!-- 이용메뉴위치 검색   -->
												 <div class="col-md-6 col-sm-6" >

												<span style="text-align:center;font-size:12pt;color:#747474;height:100%;display: inline-block;margin-top:5px;margin-left:15px">LOCATION : <? echo $h_location; ?></span>
												
												<input type="hidden" id="h_location"  name="h_location" value="<? echo $h_location; ?>" >
												<input type="hidden" id="h_loc_code" name="h_loc_code" value="<? echo $h_loc_code; ?>" >
												 
													 <!-- <select name="search_location"  class="form-control" id="search_location" >
													<option value="" >메뉴위치 선택</option>
												<?
												$location_lists = get_history_item_list('h_location');
												if ($location_lists) {
													foreach($location_lists as $location_list){

												?>	
												
													<option value="<?echo "{$location_list['h_location']}";?>" <? if ($_POST['search_location']=="{$location_list['h_location']}") { echo "selected"; }?> ><?echo "{$location_list['h_location']}";?></option>
												<?
													}
												}
												?>
												</select>	 -->
 
												 </div>
												 
												 <div class="col-md-6 col-sm-6" >
													 <select name="search_action"  class="form-control" id="search_action">
													<option value="" >실행한 업무 선택</option>
												<?
												$action_lists = get_history_item_list_cate('h_action',$h_loc_code);
												if ($action_lists) {
													foreach($action_lists as $action_list){

												?>	
												
													<option value="<?echo "{$action_list['h_action']}";?>" <? if ($_POST['search_action']=="{$action_list['h_action']}") { echo "selected"; }?> ><?echo "{$action_list['h_action']}";?></option>
												<?
													}
												}
												?>
												</select> 	
												 </div>
											</div>	
											<?// echo $add_condition; ?>
										</td>
										
										<td> 
											<div style="text-align:left;display:inline">
											<!-- 기간검색 -->
												 <div class="col-md-6 col-sm-6" >
													 <input id="search_ip" class="form-control" class='date' type="text" name="search_ip"  placeholder="작업장소 IP로 검색합니다." value="<? if($_POST['search_ip']!=""){ echo $_POST['search_ip']; }?>" >
												 </div>
											 
												 <div class="col-md-6 col-sm-6">
													<button type='button' class='btn btn-secondary btn-sm' style='width:48%;height:100%;margin-top:5px' value="검색" onclick="search_box()">검색</button><button type='button' class='btn btn-info btn-sm' style='width:48%;height:100%;margin-top:5px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>?h_loc_code=<?echo $h_loc_code?>&h_location=<?echo $h_location;?>'">초기화</button>	 
												 </div>
											</div>	
										</td>
 
									</tr>									
									<!-- <tr style="background:#fff">

										<td colspan="2">
											<div style="text-align:right;width:100%;">
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>											
										</td>
									</tr>	 -->
									</table><br>
 
							
									<table width="100%">
									<!-- <tr>
										<td colspan="3" style="text-align:right;width:100%;"> <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:127px;background:#ff0000' value="검색">신규등록</button></td>
									</tr> -->
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=10&search=&SearchString=&gubun=free<? echo "&h_loc_code=".$h_loc_code."&h_location=".$h_location;?>"  <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=20&search=&SearchString=&gubun=free<? echo "&h_loc_code=".$h_loc_code."&h_location=".$h_location;?>"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=50&search=&SearchString=&gubun=free<? echo "&h_loc_code=".$h_loc_code."&h_location=".$h_location;?>"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=100&search=&SearchString=&gubun=free<? echo "&h_loc_code=".$h_loc_code."&h_location=".$h_location;?>"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>									

										 </div>											
										</td>
										<td width="40%" align="right" style="text-align:right">
 
										</td>
										<td  width="10%" > 
																				
										</td>
									</tr>
									</table>
								</form>
			

				<?php
				// 제품 목록 가져오기
				$historys = get_history($start_record_number,$itemsPerPage,$add_condition);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<!-- <colgroup>
						<col class="w5">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col class="w10">
						<col>
						<col class="w15">
					</colgroup> -->
					<thead>
						<tr>
							<th>NO</th>
							<th>CONNECT</th>
							<th>LOCATION</th>
							<th>HISTORY</th>
							<th>IP</th>
							<th>DATE</th>
						</tr>
					</thead>		
					<tbody>
					<?
  //{A} {홍길동}({admin11})님이 / {2024년 1월 15일 11시12분 12초}에 / IP {23.23.45.103} 에서  /  {창고관리 > 창고등록} 업무로 /  {Warehouse1043}             / {창고를 등록}하였습니다.
						if ($historys) {
							foreach ($historys as $history) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$history['h_gubun']}</td>";
  								echo "<td><span ".$menu_txt_color.">{$history['h_location']}</span></td>";
								echo "<td style='text-align:left;padding-left:10px'>";
								echo "<span ".$name_txt_color.">{$history['h_name']}</span>";
								echo "(";
								echo "<span ".$id_txt_color.">{$history['h_id']}</span>";
								echo ")님이 ";
								
								if ("{$history['h_type']}"=="A") {  // 단일등록
								echo "<span ".$col1_txt_color.">{$history['h_col1']}</span> ";								
								}elseif ("{$history['h_type']}"=="B"){  // 이동
								echo "<span ".$col1_txt_color.">{$history['h_col1']}에서</span> ";
								echo "<span ".$col1_txt_color.">{$history['h_col2']}로</span> ";								
								}elseif ("{$history['h_type']}"=="C"){  // 2개 칼럼
								echo "<span ".$col1_txt_color.">{$history['h_col1']}</span> ";
								echo "<span ".$col1_txt_color.">{$history['h_col2']}</span> ";								
								} 
								
								echo "<span ".$action_txt_color.">{$history['h_action']}</span>";	
								echo "하였습니다.";
								echo "<td><span ".$ip_txt_color.">{$history['h_ip']}</span></td>";
								echo "<td><span ".$date_txt_color.">{$history['h_date']}</span></td>";
 

								$desc_start_no=$desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							//echo "<tr><td colspan='6'>검색 결과없음</td></tr>";
						}
					?>		
					</tbody>
					
				</table>
 
				<? $itemsPerPage = $itemsPerPage."&h_loc_code=".$h_loc_code."&h_location=".$h_location; ?>

				<!-- <div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div> -->		 
				<div style="width:100%;text-align:center"><?  echo paginate_addpara($totalItems, $itemsPerPage, $currentPage, $url, $addpara);	?></div>		 
			
			 

							</div>
						 </div><!--  class="col-sm-12" -->
					  </div><!--  class="row" -->
				   </div><!--  class="x_content" -->
                </div><!-- x_panel -->
              </div><!-- class="col-md-12 col-sm-12 -->
			</div><!--  class="row" -->
		  </div><!--  class="" -->
	    </div><!--  class="right_col" role="main"> -->		  
        <!-- /page content -->


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/foot.php'); ?>