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
		$add_condition = " and ".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 	
	$list_condition = " wms_user where 1=1 ".$add_condition; // 개발모드 10 미만 전체조회

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
                    <h2>운영자관리 > 사용자 목록 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  사용자 목록을 조회하실 수 있습니다.  
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
											<option value="user_id" <? if ($search=="user_id") { echo "selected"; }?> >아이디</option>
											<option value="user_name" <? if ($search=="user_name") { echo "selected"; }?>  >사용자 이름</option>
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
				$users = getwms_users2($start_record_number,$itemsPerPage,$search,$SearchString);
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
							<!-- <th>분류</th> -->
							<th>이용상태</th>
							<th>아이디</th>
							<th>이름</th>
							<th>등록일</th>
							<th>관리</th>
						</tr>
					</thead>		
					<tbody>
					<?
 
						if ($users) {
							foreach ($users as $user) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$user['user_use']}</td>";
								echo "<td>{$user['user_id']}</td>";
								echo "<td>{$user['user_name']}</td>";
								echo "<td>{$user['user_rdate']}</td>";
								echo "<td>";
								echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('user_state_change','{$user['user_id']}','{$user['user_use']}') style='padding:2'>계정</button> ";	
								
								if ($_SESSION['admin_role'] >= 90 ) {
									echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('user_pw_change','{$user['user_id']}') style='padding:2'>비번변경</button> ";								
								}								
 
								echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('user_pw_reset','{$user['user_id']}') style='padding:2'>비번초기화</button> ";			
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
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win400_400('user2')"  style='<?echo $permission_W_button?>margin-top:60px;padding:12;width:100%;height:50px;font-weight:bold' value="등 록">사용자 등록</button>	
				</div>				
				
				<!-- 엑셀업로드 st -->
				<? $this_f_name = "s01_user"; $this_f_txt = "사용자 등록 (엑셀 업로드)";// 분류 ?>
				<div style="width:98%;text-align:right;background-color:#cfe9da">
					<form action="/excel/upload_<?echo $this_f_name;?>.php" method="post" enctype="multipart/form-data" target="upload_iframe" onsubmit="showPopup();">
						<label for="file"><u><a href="/excel/sample_excel/sample_<?echo $this_f_name;?>.xlsx">샘플다운로드</a></u> &nbsp;| &nbsp;엑셀 파일 선택:</label>
						<input type="file" name="file" id="file" required>
						<button type="submit" class='btn btn-secondary btn-sm'  style='<?echo $permission_W_button?>padding:12;margin-right:0px;margin-bottom:0px;width:60%;height:50px;font-weight:bold;background-color:#3c8259'> <?echo $this_f_txt;?></button>
					</form>
					<iframe name="upload_iframe" style="display:none;"></iframe>	
					
					<script>
						function showPopup() {
							// 파일이 업로드된 후 아이프레임이 로드될 때 실행되는 함수를 설정
							document.querySelector('iframe[name="upload_iframe"]').onload = function() {
								// 부모 창을 리로드
								parent.location.reload();
							};
						}
					</script>					
					
				</div>				
				<!-- 엑셀업로드 end -->				
		 

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