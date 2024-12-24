<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>

	
    <?  /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R2 = permission_ck('창고','R',$_SESSION['admin_role']);
	 if ($pm_rst_R2 == 'F') {	 echo "<script>alert('창고조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }
 	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W2 = permission_ck('창고','W',$_SESSION['admin_role']); if ($pm_rst_W2 == 'F') {  $permission_W2_button = "display:none;"; $permission_W2_txt = "창고등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U2 = permission_ck('창고','U',$_SESSION['admin_role']); if ($pm_rst_U2 == 'F') {  $permission_U2_button = "!"; $permission_U2_txt = "창고수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D2 = permission_ck('창고','D',$_SESSION['admin_role']); if ($pm_rst_D2 == 'F') {  $permission_D2_button = "display:none;"; $permission_D2_txt = "<BR>창고삭제권한없음"; }
	   
      /// 권한 체크 : 조회권한 - 메인화면 리다이렉트 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('앵글','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('앵글조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }
	   
       /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('앵글','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "앵글등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('앵글','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = ""; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('앵글','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>앵글삭제권한없음"; }
	 
	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W3 = permission_ck('제품','W',$_SESSION['admin_role']); if ($pm_rst_W3 == 'F') {  $permission_W3_button = "display:none;"; $permission_W3_txt = "제품등록권한없음"; }

	 
 	  
	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1
	   
	?>	
	
	
	<!-- 게시판 리스트 계산 start -->
	<?
  
	// 검색결과 추가조건 sql
	if ($_POST['SearchString']!="") {
		$add_condition = " and ".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 
	$list_condition = "wms_warehouses where partner_id = ".$_SESSION['partner_id']." and warehouse_id <> 0 and delYN = 'N' ".$add_condition;
	$new_itemsPerPage = 5; // 페이지당 레코드 수 지정
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트	
	
    if (($_GET['angle_id']!="")&&($_GET['order']!="")) {
		update_angle_order($_GET['angle_id'],$_GET['order']);
    }	

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
                    <h2>창고관리 > 창고목록 <small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  관리창고목록을 조회하실 수 있습니다.
								</p>
								
									
								<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" name="search" onsubmit="return ;">
								<input type="hidden" name="itemsPerPage" value="10">									
									<table width="100%">
									<tr>
										<td width="50%">
										<div class="dataTables_length" id="datatable-buttons_length" style="width:100%"> Total : <? echo $totalcount?> 건&nbsp;&nbsp;
										<select name="datatable-buttons_length" aria-controls="datatable-buttons" class="form-control input-sm"   onchange="location.href=this.value;" style="width:120px;font-size:13px">
										<?
										if ($new_itemsPerPage!=0) {
										 ?>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=<?echo $new_itemsPerPage;?>&search=&SearchString=&gubun=free"  <? if ($itemsPerPage==$new_itemsPerPage){ echo "selected"; }?>><?echo $new_itemsPerPage;?>개 보기</option>		
										<?
										 }	
										?>									
										
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=10&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="10"){ echo "selected"; }?>>10개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=20&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="20"){ echo "selected"; }?>>20개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=50&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="50"){ echo "selected"; }?>>50개 보기</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=100&search=&SearchString=&gubun=free"  <? if ($itemsPerPage=="100"){ echo "selected"; }?>>100개 보기</option>
										</select>		
										

										 </div>											
										</td>
										<td width="40%" align="right" style="text-align:right">
										<input type="text" name="SearchString" size="20" class="form-control col-md-3" style="width:180px;height:30px;margin-top:-4px;margin-left:5px;float:right" required  <? if ($_POST['SearchString']!="") { echo 'value="'.$_POST['SearchString'].'"'; }?> >	
										
										<select name="search" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;">
											<!-- <option value="warehouse_code" <? if ($search=="warehouse_code") { echo "selected"; }?> >창고코드명</option> -->
											<option value="warehouse_name" <? if ($search=="warehouse_name") { echo "selected"; }?>  >창고명</option>
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
				// 창고 목록 가져오기
				$warehouses = getwms_warehouses($start_record_number,$itemsPerPage,$search,$SearchString);
				?>				
				<table id="tb_border" class="dataCustomTable" >
					<thead>
						<tr>
							<th width="12%">NO</th>
							<!-- <th>창고ID</th>
							<th>코드명</th> -->
							<th width="28%">창고</th>
							<!-- <th>앵글목록</th>
							<th>등록자</th> -->
							<th width="28%">앵글</th>
							<!-- <th width="22%">앵글조정</th> -->
							<th width="10%">제품수량</th>
							<th width="22%">제품목록</th>
						</tr>
					</thead>
					<tbody>
					<?  
                        $now_wid =""; $cg_wid = ""; $now_bg= "style='background:#d7deea'";$cg_count=0;
						if ($warehouses) {
							//$start_record_number = $start_record_number + 1;
							foreach ($warehouses as $warehouse) {
								
								if($cg_count==0){
									$now_bg= "style='background:#d7deea'";	
									$now_wid = "{$warehouse['warehouse_id']}"; 
								}else{
									$cg_wid = "{$warehouse['warehouse_id']}"; 
									if ($now_wid==$cg_wid) {
										if ($now_bg== "style='background:#d7deea'") {
											$now_bg = "style='background:#d7deea'";
										}else{
											$now_bg = "style='background:#fae6d6'";
										}										
									}else{
										if ($now_bg== "style='background:#d7deea'") {
											$now_bg = "style='background:#eee'";
										}else{
											$now_bg = "style='background:#d7deea'";
										}
									}	
								}
								
								echo "<tr ".$now_bg.">";					
								echo "<td>".$desc_start_no."</td>";
								//echo "<td>{$warehouse['warehouse_id']}</td>";
								//echo "<td>{$warehouse['warehouse_code']}</td>";  
								
								if($warehouse['warehouse_name']=="미지정"){
									echo "<td >{$warehouse['warehouse_name']}";
								}else{
									echo "<td ><".$permission_U2_button."a onclick=popup_win_warehouse_update('warehouse_update','{$warehouse['warehouse_id']}',400,300) style='cursor:pointer'>{$warehouse['warehouse_name']}</a>";	
								}
 
								if (("{$warehouse['sum_quantity_warehouse']}"!="0")||("{$warehouse['warehouse_name']}"=="미지정")){ // 제품이 있는 경우, 또는 미지정인 경우 창고삭제 안됨
									//echo " 못지움";
								}else{
									// 제품이 없는데...........
									
									if ($result_setting[0]['set_state']=="Y") {  // 창고,앵글 일괄삭제 Y 이면,
										echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_warehouse_del('warehouse_del',{$warehouse['warehouse_id']},400,300) style='".$permission_D2_button."cursor:pointer".$xhidden."'>";											
									}else{   // 창고,앵글 일괄삭제 N 이면,
										
										if ("{$warehouse['angle_cnt']}"==0) {
											echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_warehouse_del('warehouse_del',{$warehouse['warehouse_id']},400,300) style='".$permission_D2_button."cursor:pointer".$xhidden."'>";												
										}
									}									
								}
								
								echo "</td>";
								echo "<td style='text-align:center'>";
								if ($permission_W_txt == "앵글등록권한없음") {
								}else{
									//echo "<br>";
								}
								
							    
								if($warehouse['warehouse_name']=="미지정"){
									echo "-";
								}else{
									echo "<button type='button' class='btn btn-secondary btn-sm' style='".$permission_W_button."padding:2;width:70px' value='앵글삽입'  onclick=popup_win_size('angle',{$warehouse['warehouse_id']},400,400) style='".$permission_W_button."cursor:pointer'>앵글추가</button>".$permission_W_txt;									
								}
								


								echo "</td>";

								echo "<td >  </td>";
								echo "<td >  </td>";
								echo "<td >  </td>";
								//$start_record_number = $start_record_number + 1;
							    echo "</tr>";
							
								$angle_lists = getwms_angle_namelist("{$warehouse['warehouse_id']}");
								if (!empty($angle_lists)) {	
										// 특정 데이터 1개 추출
										if (!empty($angle_lists)) {		
											$updown_i = 0;
											foreach($angle_lists as $row){
												echo "<tr  ".$now_bg." >";	// 앵글목록 start														
													//echo $row['angle_id'];  
													$angle_order = $row['angle_order'];

													echo "<td>ㄴ</td><td> </td>";
													
													
													echo "<td  style='font-weight:bold;text-align:center' >";
													
													if($row['angle_name']=="미지정"){
														echo "미지정";
													}else{
														echo "<".$permission_U_button."a onclick=popup_win_angle_update('angle_update',{$warehouse['warehouse_id']},".$row['angle_id'].",400,300)  style='cursor:pointer'><div style='width:100%;text-align:center'>".$row['angle_name']."</span></a>"; 

														if ($row['sum_quantity']==0) { // 앵글에 담긴 수량이 없으면, 삭제가능
															echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_angle_del('angle_del',{$warehouse['warehouse_id']},".$row['angle_id'].",400,300)  style='".$permission_D_button."cursor:pointer".$xhidden."'>";													
														}
																											
													}

													
													echo "</td><td style='display:none;'>";	
						
													if ($updown_i==0) {
													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363;cursor: auto;' title='$angle_order' >up ▲</button>";											
													}else{
													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363' onclick=order_up_angle({$warehouse['warehouse_id']},".$row['angle_id'].",".$angle_order.")  title='$angle_order'>up ▲</button> ";
													}

													echo "<button type='button' class='btn btn-secondary btn-sm'  style='background:#dedede;color:#636363' onclick=order_down_angle('{$warehouse['warehouse_id']}','".$row['angle_id']."','".$angle_order."') >▼ down</button>";
													
													
													echo "</td><td>".number_format($row['sum_quantity']);	
													echo "</TD>";
													
													echo "<td ><button type='button' class='btn btn-secondary btn-sm' style='".$permission_W3_button."padding:2;width:70px' value='상세보기'  onclick=popup_win_productlist_in_angle('productlist_in_angle',{$warehouse['warehouse_id']},".$row['angle_id'].",600,700) style='".$permission_W3_button."cursor:pointer'> 상세보기</button></td>";  	
												 
												echo "</tr>";	
												$updown_i =$updown_i + 1;
											}
										}else{
											    echo " ";
										}									
								}else{
									 
								}								
								$desc_start_no = $desc_start_no - 1;
								$cg_count=$cg_count+1;
							} // foreach ($warehouses as ...
							
						
						} else {
							echo "<tr><td  colspan='4' >등록된 창고 없음</td></tr>";
						}
							echo "</tr>";	// 앵글목록 end							
						
					?>					
 
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>		 
				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win_size_warehouse('warehouse','400','300')" style='<?echo $permission_W2_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="창고 등록">창고 등록</button>	
				</div>
				
				<!-- 엑셀업로드 st -->
				<? $this_f_name = "m02_warehouse_angle"; $this_f_txt = "창고&앵글등록 (엑셀 업로드)";// 분류 ?>
				<div style="width:98%;text-align:right;background-color:#cfe9da;<? echo $permission_W2_button;?>">
					<form action="/m_excel/upload_<?echo $this_f_name;?>.php" method="post" enctype="multipart/form-data" target="upload_iframe" onsubmit="showPopup();">
						<label for="file"><u><a href="/m_excel/sample_excel/sample_<?echo $this_f_name;?>.xlsx">엑셀샘플 다운로드</a></u> &nbsp;| &nbsp;엑셀 파일 선택:</label>
						<input type="file" name="file" id="file" required>
						<button type="submit" class='btn btn-secondary btn-sm'  style='<?echo $permission_W_button?>padding:12;margin-right:0px;margin-bottom:0px;width:60%;height:50px;font-weight:bold;background-color:#3c8259'> <?echo $this_f_txt;?></button>
					</form>
					 <iframe name="upload_iframe" style="display:none;"></iframe>
					<!--<iframe name="upload_iframe" style="width:300px;height:300px;"></iframe>	 -->	
					
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

 

<script>
    function handleFileChange(index) {
        const fileInput = document.getElementById('file_' + index);
        const fileLabel = document.getElementById('file_label_' + index);

        if (fileInput.files.length > 0) {
            fileLabel.innerHTML = '선택완료';
        } else {
            fileLabel.innerHTML = '찾아보기';
        }
    }
 
</script>


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/foot.php'); ?>