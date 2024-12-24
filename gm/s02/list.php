<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>
	
<?
	   
      /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('권한관리목록','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('권한관리목록 조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }
	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('권한관리목록','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "<select class='form-control' style='margin:0% 5% 10% 5%;width:90%;border:0;font-size:12px;text-align:center;background:#828282;color:#FFF'><option>설정권한없음</option></select>"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('권한관리목록','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_U_txt = "권한관리목록 수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('권한관리목록','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>권한관리목록 삭제권한없음"; }
?> 	 	
	
	
	<?
		if ($_GET['up_sorting']!="") {
			list_up_sorting($_GET['up_sorting']);
		}
	
	?>
	
	
	
	<!-- 게시판 리스트 계산 start -->
	<?
	// 검색결과 추가조건 sql
	$add_condition = "";
	if ($_POST['SearchString']!="") {
		//$add_condition = " and a.".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 	
	if ($_SESSION['access_role'] < 10) {
		$list_condition = " wms_access_crud ".$add_condition; // 개발모드 10 미만 전체조회
 	}else{
		$list_condition = " wms_access_crud ".$add_condition; // 개발모드 10 미만 전체조회
	}
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/search.php'); ?>			
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
                    <h2>접근권한관리 > 관리목록 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  접근권한을 관리하실 수 있습니다. 
								</p>
								<br><br> 
									
 
			
		 
				<?php
				// 접근권한 목록 가져오기
				$users = getwms_access_crud($start_record_number,$itemsPerPage,$search,$SearchString);
				?>				
		 
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
 
					<thead>
						<tr>
							
							<th rowspan="2"  style="width:16%">대상</th>
							<th colspan="4"  style="width:84%">권한</th>
						</tr>
						<tr>
							<th style="width:21%;background:#385c94">조회</th>
							<th style="width:21%;background:#5c9474">등록</th>
							<th style="width:21%;background:#df9a26">수정(이동,실행)</th>
							<th style="width:21%;background:#791487">삭제</th>
						</tr>
					</thead>		
					<tbody>
					<?
 
						if ($users) {
							$i=1;
							foreach ($users as $user) {
								echo "<tr>";					
								echo "<td align='left' style='padding-left:20px'>".$i.") {$user['access_name']} ";
								
								//if ($i!=1) { echo "<a href='/gm/s02/list.php?left_location=1&up_sorting={$user['access_order']}'>△</a>"; }
								
								echo "</td>";

								////////////////////////////////////////////////////////////////////////////
								////////////////////////////////////////////////////////////////////////////
								
								$acc_type = "R";
								
								if ($acc_type == "R") {	$bg = "#385c94";
								}elseif($acc_type == "W") {$bg = "#df9a26";
								}elseif($acc_type == "U") {$bg = "#df9a26";
								}elseif($acc_type == "D") {$bg = "#791487";
								}
								 
								echo "<td style='position:relative;border:1px solid ".$bg.";padding-bottom:38px;'>";
					?>			
					            <!-- 시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br> -->
								<? 
								$cates = wms_access_crud("{$user['access_id']}",$acc_type);
									if ($cates) {
										foreach ($cates as $cate) {   
											if ("{$cate['cate_admin_role']}"=="99") {	$xhidden = ";display:none";
											}else{									$xhidden = "";
											}
											echo "{$cate['cate_name']} ({$cate['cate_admin_role']}) <img src='../images/x.png' width='13px' onclick=fn_user_cate_del('{$user['access_id']}','$acc_type','{$cate['cate_admin_role']}') style='".$permission_D_button."cursor:pointer".$xhidden."'><br>";	
										}
									} 	
								?>	
											<div style="position:absolute;bottom:0;width:100%;height:33px;">
											<select name="user_cate_<? echo $acc_type."{$user['access_id']}"?>" id="user_cate_<? echo $acc_type."{$user['access_id']}"?>" class="form-control" style="<?echo $permission_W_button?>margin:0% 5% 10% 5%;width:90%;border:1;border-color:#9c9c9c;font-size:12px;text-align:center;background:#dedede;color:#1a1a1a" onchange="fn_user_cate('<?echo $acc_type?>','<? echo "{$user['access_id']}"?>',this)">분류 선택
												<option value='X' selected> 담당 지정하기 </option> 											
											<?
											if (("{$user['access_name']}"=="운영자목록")||("{$user['access_name']}"=="운영자분류명관리")||("{$user['access_name']}"=="권한관리목록")||("{$user['access_name']}"=="시스템설정")) {
												$cates = wms_access_add_onlysystme("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 												
											}else{
												
											   echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//999'> 전체 일괄등록</option>";  
												
												$cates = wms_access_add("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 													
											}
	
											?>
											</select><?echo $permission_W_txt?>	
											</div>
					<?			
								echo "</td>";
								
								////////////////////////////////////////////////////////////////////////////
								////////////////////////////////////////////////////////////////////////////
								
								$acc_type = "W";
								
								if ($acc_type == "R") {	$bg = "#385c94";
								}elseif($acc_type == "W") {$bg = "#df9a26";
								}elseif($acc_type == "U") {$bg = "#df9a26";
								}elseif($acc_type == "D") {$bg = "#791487";
								}
								 
								echo "<td style='position:relative;border:1px solid ".$bg.";padding-bottom:38px;'>";
					?>			
					            <!-- 시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br> -->
								<? 
								$cates = wms_access_crud("{$user['access_id']}",$acc_type);
									if ($cates) {
										foreach ($cates as $cate) {   
											if ("{$cate['cate_admin_role']}"=="99") {	$xhidden = ";display:none";
											}else{									$xhidden = "";
											}
											echo "{$cate['cate_name']} ({$cate['cate_admin_role']}) <img src='../images/x.png' width='13px' onclick=fn_user_cate_del('{$user['access_id']}','$acc_type','{$cate['cate_admin_role']}') style='".$permission_D_button."cursor:pointer".$xhidden."'><br>";	
										}
									} 	
								?>	
											<div style="position:absolute;bottom:0;width:100%;height:33px;">
											<select name="user_cate_<? echo $acc_type."{$user['access_id']}"?>" id="user_cate_<? echo $acc_type."{$user['access_id']}"?>" class="form-control" style="<?echo $permission_W_button?>margin:0% 5% 10% 5%;width:90%;border:1;border-color:#9c9c9c;font-size:12px;text-align:center;background:#dedede;color:#1a1a1a" onchange="fn_user_cate('<?echo $acc_type?>','<? echo "{$user['access_id']}"?>',this)">분류 선택

												<option value='X' selected> 담당 지정하기 </option> 
											<?
											if (("{$user['access_name']}"=="운영자목록")||("{$user['access_name']}"=="운영자분류명관리")||("{$user['access_name']}"=="권한관리목록")||("{$user['access_name']}"=="시스템설정")) {
												$cates = wms_access_add_onlysystme("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 												
											}else{
												
											   echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//999'> 전체 일괄등록</option>";  
												
												$cates = wms_access_add("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 													
											}
	
											?>
											</select><?echo $permission_W_txt?>	
											</div>
					<?			
								echo "</td>";
								
								////////////////////////////////////////////////////////////////////////////
								////////////////////////////////////////////////////////////////////////////
								
								$acc_type = "U";
								
								if ($acc_type == "R") {	$bg = "#385c94";
								}elseif($acc_type == "W") {$bg = "#df9a26";
								}elseif($acc_type == "U") {$bg = "#df9a26";
								}elseif($acc_type == "D") {$bg = "#791487";
								}
								 
								echo "<td style='position:relative;border:1px solid ".$bg.";padding-bottom:38px;'>";
					?>			
					            <!-- 시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br> -->
								<? 
								$cates = wms_access_crud("{$user['access_id']}",$acc_type);
									if ($cates) {
										foreach ($cates as $cate) {   
											if ("{$cate['cate_admin_role']}"=="99") {	$xhidden = ";display:none";
											}else{									$xhidden = "";
											}
											echo "{$cate['cate_name']} ({$cate['cate_admin_role']}) <img src='../images/x.png' width='13px' onclick=fn_user_cate_del('{$user['access_id']}','$acc_type','{$cate['cate_admin_role']}') style='".$permission_D_button."cursor:pointer".$xhidden."'><br>";	
										}
									} 	
								?>	
											<div style="position:absolute;bottom:0;width:100%;height:33px;">
											<select name="user_cate_<? echo $acc_type."{$user['access_id']}"?>" id="user_cate_<? echo $acc_type."{$user['access_id']}"?>" class="form-control" style="<?echo $permission_W_button?>margin:0% 5% 10% 5%;width:90%;border:1;border-color:#9c9c9c;font-size:12px;text-align:center;background:#dedede;color:#1a1a1a" onchange="fn_user_cate('<?echo $acc_type?>','<? echo "{$user['access_id']}"?>',this)">분류 선택
												<option value='X' selected> 담당 지정하기 </option> 
											<?
											if (("{$user['access_name']}"=="운영자목록")||("{$user['access_name']}"=="운영자분류명관리")||("{$user['access_name']}"=="권한관리목록")||("{$user['access_name']}"=="시스템설정")) {
												$cates = wms_access_add_onlysystme("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 												
											}else{
												
											   echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//999'> 전체 일괄등록</option>";  
												
												$cates = wms_access_add("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 													
											}
	
											?>
											</select><?echo $permission_W_txt?>	
											</div>
					<?			
								echo "</td>";
								
								////////////////////////////////////////////////////////////////////////////								
								////////////////////////////////////////////////////////////////////////////
								
								$acc_type = "D";
								
								if ($acc_type == "R") {	$bg = "#385c94";
								}elseif($acc_type == "W") {$bg = "#df9a26";
								}elseif($acc_type == "U") {$bg = "#df9a26";
								}elseif($acc_type == "D") {$bg = "#791487";
								}
								 
								echo "<td style='position:relative;border:1px solid ".$bg.";padding-bottom:38px;'>";
					?>			
					            <!-- 시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br>
					            시스템관리자 <input type="checkbox" name=""><br> -->
								<? 
								$cates = wms_access_crud("{$user['access_id']}",$acc_type);
									if ($cates) {
										foreach ($cates as $cate) {   
											if ("{$cate['cate_admin_role']}"=="99") {	$xhidden = ";display:none";
											}else{									$xhidden = "";
											}
											echo "{$cate['cate_name']} ({$cate['cate_admin_role']}) <img src='../images/x.png' width='13px' onclick=fn_user_cate_del('{$user['access_id']}','$acc_type','{$cate['cate_admin_role']}') style='".$permission_D_button."cursor:pointer".$xhidden."'><br>";	
										}
									} 	
								?>	
											<div style="position:absolute;bottom:0;width:100%;height:33px;">
											<select name="user_cate_<? echo $acc_type."{$user['access_id']}"?>" id="user_cate_<? echo $acc_type."{$user['access_id']}"?>" class="form-control" style="<?echo $permission_W_button?>margin:0% 5% 10% 5%;width:90%;border:1;border-color:#9c9c9c;font-size:12px;text-align:center;background:#dedede;color:#1a1a1a" onchange="fn_user_cate('<?echo $acc_type?>','<? echo "{$user['access_id']}"?>',this)">분류 선택
												<option value='X' selected> 담당 지정하기 </option> 
											<?
											if (("{$user['access_name']}"=="운영자목록")||("{$user['access_name']}"=="운영자분류명관리")||("{$user['access_name']}"=="권한관리목록")||("{$user['access_name']}"=="시스템설정")) {
												$cates = wms_access_add_onlysystme("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 												
											}else{
												
											   echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//999'> 전체 일괄등록</option>";  
												
												$cates = wms_access_add("{$user['access_id']}",$acc_type);
													if ($cates) {
														foreach ($cates as $cate) {
															echo "<option value='{$user['access_id']}//{$user['access_name']}//".$acc_type."//{$cate['cate_admin_role']}'> {$cate['cate_name']} ({$cate['cate_admin_role']})</option>";	
														}
													} 													
											}
	
											?>
											</select><?echo $permission_W_txt?>	
											</div>
					<?			
								echo "</td>";
								
								////////////////////////////////////////////////////////////////////////////

								//$desc_start_no=$desc_start_no - 1;	
								$i=$i+1;
							}
							echo "</tr>";
						} else {
							//echo "<tr><td colspan='8'>검색 결과없음</td></tr>";
						}
					?>		
					</tbody>
				</table>
 
 			 <? if ($_SESSION['admin_role']=="100") { ?>

				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win400_260('user')" style='margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="대상 등록">대상 등록</button>	
				</div>
		     <? }else{?>
					
			     <br><br><br>
		      <?}?>			 

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
		
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/foot.php'); ?>