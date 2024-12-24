<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/top_navigation.php'); ?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('제품카테고리','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('제품카테고리 조회 권한이 없습니다.');location.href='/gm/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('제품카테고리','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "제품카테고리 등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('제품카테고리','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "수정 권한없음"; }
	 
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('제품카테고리','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>삭제권한없음"; }
 	   
	?>	

	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_cate where  partner_id = ".$_SESSION['partner_id']." and delYN = 'N' and cate_id <> 0 ";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/paging_cnt.php'); ?>
	
	<script>
		function Status(myform, sn, seq) {
			if(confirm("처리여부를 수정하시겠습니까?")) {
				myform.sn.value = sn;  // idx
				myform.Process.value = document.getElementById("Process"+seq).value;  // 입고완료, 입고대기 등등..
				myform.action="/gm/inc/process.php";
				myform.submit();
			}
		}	
		function Status2(myform, sn, seq) {
			if(confirm("처리여부를 수정하시겠습니까?")) {
				myform.sn.value = sn;  // idx
				myform.Process.value = document.getElementById("Process_expose"+seq).value;  // 노출여부 등등..
				myform.location2.value = "m03_cate_expose";   		
				myform.action="/gm/inc/process.php";
				myform.submit();
			}
		}	
	</script>


	<iframe width='0' height='0' frameborder="0" marginwidth="0" marginheight="0" name="inquery"></iframe>
	<form name="reservation" method="post" target="inquery" style="display:none">
	<input type="hidden" name="sn" value="">
	<input type="hidden" name="Process" value="">
	<input type="hidden" name="location2" value="m03_cate_list">
	</form>

	
	
	
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
                    <h2>제품관리 > 제품분류관리<small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  제품분류관리로 카테고리를 관리하실 수 있습니다.
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
										<!-- <input type="text" name="SearchString" size="20" class="form-control col-md-3" style="width:180px;height:30px;margin-top:-4px;margin-left:5px;float:right" required>	
										
										<select name="search" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;">
											<option value="item_code" <? if ($search=="item_code") { echo "selected"; }?> >제품코드명</option>
											<option value="item_name" <? if ($search=="item_name") { echo "selected"; }?>  >제품명</option>
										</select> -->
										
										</td>
										<td  width="10%" > 
										<!-- <div style="text-align:right;width:100%;">
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>	 -->										
										</td>
									</tr>
									</table>
								</form>
			

				<?php
				// 분류 목록 가져오기
				$cates = getwms_cate($start_record_number,$itemsPerPage);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th>NO</th>
							<th>고유넘버</th>
							<th>카테고리명</th>
							<th>카테고리 이용제품</th>
							<!-- <th>노출여부</th> -->
							<th>등록일자</th>
							<!-- <th>관리</th> -->
						</tr>
					</thead>
					<tbody>
					<?
 
						if ($cates) {
							$i=1;
							foreach ($cates as $cate) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$cate['cate_id']}</td>";
								echo "<td><".$permission_U_button."a onclick=popup_win3('cate_edit','{$cate['cate_id']}') style='cursor:pointer'>{$cate['cate_name']}</a>";
								
 
								if ("{$cate['cnt_item']}"!="0"){ //  있는 경우,  삭제 안됨
									 echo "&nbsp;&nbsp"; // 못지움
								}else{
									// ....이 없는데...........
									echo "&nbsp;&nbsp;<img src='../images/x.png' width='13px' onclick=popup_win_cate_del('cate_del',{$cate['cate_id']},400,300) style='".$permission_D_button."cursor:pointer".$xhidden."'>";											
																	
								}
								echo "</td>";
								echo "<td>{$cate['cnt_item']}";
								echo "<td>{$cate['cate_rdate']}</td>";

							
								$desc_start_no = $desc_start_no - 1;
							$i=$i+1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='5'>등록 카테고리 없음</td></tr>";
						}
					?>		
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>	
				
				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win('cate')" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="분류 등록">분류 등록</button>	
				</div>
 	 
				<!-- 엑셀업로드 st -->
				<? $this_f_name = "m03_cate"; $this_f_txt = "분류 등록 (엑셀 업로드)";// 분류 ?>
				<div style="width:98%;text-align:right;background-color:#cfe9da;<? echo $permission_W_button;?>">
					<form action="/m_excel/upload_<?echo $this_f_name;?>.php" method="post" enctype="multipart/form-data" target="upload_iframe" onsubmit="showPopup();">
						<label for="file"><u><a href="/m_excel/sample_excel/sample_<?echo $this_f_name;?>.xlsx">분류샘플 다운로드</a></u> &nbsp;| &nbsp;엑셀 파일 선택:</label>
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


<? include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/foot.php'); ?>