<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/topmenu.php'); ?>

<?php 

   if ($_SESSION['partner_id'] == "1111") {
	include_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/sys_sidebar_menu.php');     
  }else{
	include_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/sidebar_menu.php');     
   }
?>  

	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/top_navigation.php'); ?>
	
	
<?
   
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
		$list_condition = " RTU_admin a join RTU_admin_cate c on a.admin_role = c.cate_admin_role and a.partner_id = c.partner_id and a.partner_id = ".$_SESSION['partner_id']." and a.admin_id = '".$_SESSION['admin_id']."' and a.admin_role < 100 ".$add_condition; // 개발모드 10 미만 전체조회
 	}else{
		$list_condition = " RTU_admin a join RTU_admin_cate c on a.admin_role = c.cate_admin_role and a.partner_id = c.partner_id  and  a.partner_id = ".$_SESSION['partner_id'].$add_condition; // 개발모드 10 미만 전체조회
	}

	
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/search.php'); ?>			
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
                    <h2>HOME > 본인정보수정 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  본인정보조회 및 수정을 할 수 있습니다.  
								</p>
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
								</form>
			

				<?php
				// 제품 목록 가져오기
				$users = getRTU_users($start_record_number,$itemsPerPage,$search,$SearchString);
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
								if($_SESSION['admin_id'] == "{$user['admin_id']}" ){
								echo "<tr>";					
								echo "<td>{$user['admin_role']}</td>";
								echo "<td>{$user['cate_name']}</td>";
								echo "<td>{$user['admin_id']}</td>";
								echo "<td>{$user['admin_name']}</td>";
								echo "<td>{$user['admin_rdate']}</td>";
								echo "<td>";
								echo "<button type='button' class='btn btn-secondary btn-sm' onclick=popup_win('pw_change','{$user['admin_id']}') style='padding:2'>변경</button> ";								
								echo "</td>";
								}
							}
							echo "</tr>";
						} else {

						}
					?>		
					</tbody>
				</table>

				<!-- <div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>	

				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win400_400('user')"  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="등 록">운영자 등록</button>	
				</div>		 --> 			
				
		 

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


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/foot.php'); ?>