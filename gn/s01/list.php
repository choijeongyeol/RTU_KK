<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
	
<?
	   
      /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('운영자목록','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('운영자목록조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }
	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('운영자목록','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "운영자목록 등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('운영자목록','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_U_txt = "운영자목록 수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('운영자목록','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>운영자목록 삭제권한없음"; }
?> 	   	   
	
	
	<!-- 게시판 리스트 계산 start -->
	<?
	// 검색결과 추가조건 sql
	$add_condition = "";
	if ($_POST['SearchString']!="") {
		$add_condition = " and a.".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 	
	if ($_SESSION['admin_role'] < 100) {
		$list_condition = " wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role and a.admin_role < 100 ".$add_condition; // 개발모드 10 미만 전체조회
 	}else{
		$list_condition = " wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role ".$add_condition; // 개발모드 10 미만 전체조회
	}
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->


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
                    <h2>운영자관리 > 운영자 목록 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  운영자 목록을 조회하실 수 있습니다.  
								</p>
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
									<table width="100%">
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=10&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=20&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=50&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=100&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>									

										 </div>											
										</td>
										<td width="40%" align="right" style="text-align:right">
										<input type="text" name="SearchString" size="20" class="form-control col-md-3" style="width:180px;height:30px;margin-top:-4px;margin-left:5px;float:right" required <? if ($_POST['SearchString']!="") { echo 'value="'.$_POST['SearchString'].'"'; }?> >	
										
										<select name="search" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;">
											<option value="admin_id" <? if ($search=="admin_id") { echo "selected"; }?> >아이디</option>
											<option value="admin_name" <? if ($search=="admin_name") { echo "selected"; }?>  >관리자 이름</option>
										</select>
										
										</td>
										<td  width="10%" > 
										<div style="text-align:right;width:100%;">
											 
											 
											 
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>											
										</td>
									</tr>
									</table>
								</form>
			

				<?php
				// 제품 목록 가져오기
				$users = getwms_users($start_record_number,$itemsPerPage,$search,$SearchString);
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
							<th>분류</th>
							<th>분류명</th>
							<th>아이디</th>
							<th>이름</th>
							<th>등록일</th>
							<th>비밀번호 관리</th>
						</tr>
					</thead>		
					<tbody>
					<?
 
						if ($users) {
							foreach ($users as $user) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$user['admin_role']}</td>";
								echo "<td>{$user['cate_name']}</td>";
								echo "<td>{$user['admin_id']}</td>";
								echo "<td>{$user['admin_name']}</td>";
								echo "<td>{$user['admin_rdate']}</td>";
								echo "<td>";
								if ($_SESSION['admin_role'] == 100 ) {
									echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_change','{$user['admin_id']}') style='padding:2'>변경</button> ";								
								}elseif($_SESSION['admin_id'] == "{$user['admin_id']}" ) {
									echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_change','{$user['admin_id']}') style='padding:2'>변경</button> ";								
								}else{
									echo "권한없음";											
								}
								
								if (($_SESSION['admin_role'] == 100 )||($_SESSION['admin_role'] == 99 )||($pm_rst_U == 'T')) {							
									
									if (($_SESSION['admin_role'] == 100 )||($_SESSION['admin_role'] == 99 )) {
										echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_reset','{$user['admin_id']}') style='padding:2'>초기화 1234</button> ";							
									}elseif($_SESSION['admin_id'] == "{$user['admin_id']}" ) {
										echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_reset','{$user['admin_id']}') style='padding:2'>초기화 1234</button> ";								
									}elseif(($pm_rst_U == 'T')&&($_SESSION['admin_role'] > "{$user['admin_role']}" )) {
										echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_reset','{$user['admin_id']}') style='padding:2'>초기화 1234</button> ";								
									}else{
										echo "";											
									}									
									
								}
								echo "</td>";
								$desc_start_no=$desc_start_no - 1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='8'>검색 결과없음</td></tr>";
						}
					?>		
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>		 

				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win400_400('user')"  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="등 록">운영자 등록</button>	
				</div>				
				
		 

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