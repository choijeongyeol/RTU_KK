<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('시스템설정','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('시스템설정 조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('시스템설정','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "시스템설정 등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('시스템설정','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_U_txt = "수정 권한없음"; }
 	   
 	   
       /// 권한 체크 : 삭제권한 - SYSID  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('시스템설정','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_SYSID = "!"; $permission_D_txt = "권한없음"; }
 	   
	?>	

	<!-- 게시판 리스트 계산 start -->
	<?
	$list_condition = "wms_setting";
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	
	<script>
		function Status(myform, sn, seq) {
			if(confirm("처리여부를 수정하시겠습니까?")) {
				myform.sn.value = sn;  // idx
				myform.Process.value = document.getElementById("Process"+seq).value;  // 입고완료, 입고대기 등등..
				myform.action="/gn/inc/process.php";
				myform.submit();
			}
		}	
 
	</script>


	<iframe width='0' height='0' frameborder="0" marginwidth="0" marginheight="0" name="inquery"></iframe>
	<form name="reservation" method="post" target="inquery" style="display:none">
	<input type="hidden" name="sn" value="">
	<input type="hidden" name="Process" value="">
	<input type="hidden" name="location2" value="s03_status_list">
	</form>

	
	
	
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
                    <h2>시스템관리 > 시스템설정<small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  시스템설정 사용유무 상태값을 변경하여, 운영관리하실 수 있습니다.
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
				// 제품 목록 가져오기
				$sys_status = getwms_sys_status($start_record_number,$itemsPerPage);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th>NO</th>
							<th>고유번호</th>
							<th>기능항목</th>
							<th>사용유무</th>
							<th>설명</th>
							<th>등록일자</th>
							<!-- <th>관리</th> -->
						</tr>
					</thead>
					<tbody>
					<?
 
						if ($sys_status) {
							$i=1;
							foreach ($sys_status as $status) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$status['set_id']}</td>";
								echo "<td><".$permission_SYSID."a onclick=popup_win_sys_update('sys_update','{$status['set_id']}',400,300,'set_name') style='cursor:pointer'>{$status['set_name']}</a></td>";
							?>
									  <td> 
										<select name="Process<?echo $i?>" id="Process<?echo $i?>"  class="input-sm" style="width:90px;height:30px;font-size:14px;border:1px solid #ccc">
											<option value="Y" <? if ("{$status['set_state']}"=="Y") { echo "selected"; }  ?>>사용</option>
											<option value="N" <? if ("{$status['set_state']}"=="N") { echo "selected"; }  ?>>중지</option>

										</select> 
										 <? echo $permission_U_txt;?>
										<button type="button" class='btn btn-secondary btn-sm' style='<?echo $permission_U_button?>'  onClick=Status(document.reservation,'<? echo "{$status['set_id']}"; ?>','<?echo $i?>')>수정 </button>
									   
									  </td>								
									  <td> 
										 <? echo "<".$permission_SYSID."a onclick=popup_win_sys_update('sys_update','{$status['set_id']}',400,300,'set_comment') style='cursor:pointer'>{$status['set_comment']}</a>";?>
									  </td>								
							<?
								echo "<td>{$status['set_rdate']}</td>";

								//echo "<td><button type='button' onclick=popup_win('status_edit',{$status['cate_id']}) style='".$permission_W_button."cursor:pointer' class='btn btn-secondary btn-sm'>수정</button>$permission_U_txt</td>";
								$desc_start_no = $desc_start_no - 1;
							$i=$i+1;	
							}
							echo "</tr>";
						} else {
							echo "<tr><td colspan='4'>없음</td></tr>";
						}
					?>		
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>	
				
				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win400_400('sys')" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="분류 등록">시스템설정 항목등록</button>	
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