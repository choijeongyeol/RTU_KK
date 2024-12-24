<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('운영자분류명관리','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('운영자분류명관리 조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('운영자분류명관리','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "운영자분류명관리 등록권한없음"; }
 	   
       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('운영자분류명관리','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "display:none;"; $permission_U_txt = "수정 권한없음"; }
 	   
	?>	
	
	<!-- 게시판 리스트 계산 start -->
	<?
	$cate_use=""; // 초기값
	
	if ($_GET['cate_use']!="") { $sql_cate_use = "='".$_GET['cate_use']."'";   $cate_use = "='".$_GET['cate_use']."'";	
	
	}elseif ($_POST['cate_use']!="") { $sql_cate_use = "='".$_POST['cate_use']."'"; $cate_use = "='".$_POST['cate_use']."'";	}
 
    if ($_GET['cate_use']=="") { $sql_cate_use = "<>'YN'"; $cate_use="";    }
	
	
	$list_condition = "wms_admin_cate where cate_admin_role <> 100 and  cate_use".$sql_cate_use;
	$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>		
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	
	<script>
		function Status(myform, sn, seq) {
			if(confirm("처리여부를 수정하시겠습니까?")) {
				myform.sn.value = sn;  // idx
				myform.Process.value = document.getElementById("Process"+seq).value;  // 완료, 대기 등등..
				myform.action="/gn/inc/process_admin.php";
				myform.submit();
			}
		}	
		function Status2(myform, sn, seq) {
			if(confirm("처리여부를 수정하시겠습니까?")) {
				myform.sn.value = sn;  // idx
				myform.Process.value = document.getElementById("Process_expose"+seq).value;  // 노출여부 등등..
				myform.location2.value = "m03_cate_expose";   		
				myform.action="/gn/inc/process_admin.php";
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
                    <h2>운영자관리 > 분류명 관리<small></small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
								<p class="text-muted font-13 m-b-30">
								  운영자 분류명을 관리하실 수 있습니다.
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
										<td width="50%" align="right" style="text-align:right">
										
										<!-- <input type="hidden" name="cate_use" value="Y"> -->
										
										<span style="font-size:14px">&nbsp;사용유무별 조회&nbsp;</span> <select name="cate_use" style="width:180px;height:30px;margin-top:-4px;float:right;border:1px solid #D8D8D8;" onchange="location.href=this.value;"  >
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=<?echo $itemsPerPage;?>&cate_use=Y" <? if ($sql_cate_use=="='Y'") { echo "selected"; }?> >사용</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=<?echo $itemsPerPage;?>&cate_use=N" <? if ($sql_cate_use=="='N'") { echo "selected"; }?> >중지</option>
											<option value="<?echo $_SERVER['PHP_SELF']?>?itemsPerPage=<?echo $itemsPerPage;?>&cate_use=" <? if ($sql_cate_use=="<>'YN'") { echo "selected"; }?> >전체</option>
										</select><? //echo $sql_cate_use;?>
										
										</td>
										<!-- <td  width="10%" > 
										<div style="text-align:right;width:100%;">
											 <button type='submit' class='btn btn-secondary btn-sm' style='padding:2;width:61px' value="검색">검색</button><button type='button' class='btn btn-info btn-sm' style='padding:2;width:61px' onClick="location.href='<?echo $_SERVER['PHP_SELF']?>'">초기화</button>
										</div>										
										</td>	 -->
									</tr>
									</table>
								</form>
			

				<?php
				// 운영자 목록 가져오기    cate_use
				$cates = get_admin_cate_add_cate_use($start_record_number,$itemsPerPage,$sql_cate_use);
				?>				
					
	
				<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info">
					<thead>
						<tr>
							<th rowspan="2">NO</th>
							<!-- <th>고유넘버</th>
							<th>노출여부</th> -->
							<th rowspan="2">분류</th>
							<th rowspan="2">분류명</th>
							<th rowspan="2">분류설명</th>
							<th rowspan="2">등록일자</th>
							<th colspan="2">운영자</th>
							<th rowspan="2">사용유무</th>							
						<? 	if ($_SESSION['admin_role'] >= 99) { ?>	
							<th rowspan="2">관리</th>
						<?  }?>	
						</tr>
						<tr>
							<th>이용 / 정지</th><th>이동</th>
						</tr>
					</thead>
					<tbody>
					<?
 
						if ($cates) {
							$i=1;
							foreach ($cates as $cate) {
								echo "<tr>";					
								echo "<td>".$desc_start_no."</td>";
								echo "<td>{$cate['cate_admin_role']}</td>";
								echo "<td>{$cate['cate_name']}</td>";
								echo "<td>{$cate['cate_comment']}</td>";
								echo "<td>{$cate['cate_rdate']}</td>";
								echo "<td>{$cate['use_admin_role_cnt']} / {$cate['notuse_admin_role_cnt']}</td>";
								echo "<td>이동</td>";
								
					         ?>	
							 <? if ("{$cate['use_admin_role_cnt']}" > 0 ) { ?>
										  <td> <?if ("{$cate['cate_use']}"=="Y"){ echo "사용상태"; }else{echo "중지상태";}?> 
										  <br><span style="color:red;font-size:11px;line-height:80%">분류카테고리의 사용유무 변경처리는,<br> 운영자를 먼저 비워야 가능함</span></td>								 
							 <?}else{
									if ("{$cate['cate_admin_role']}">=99) {
									echo "<td> 사용 (고정) </td>";
									}else{ ?>
										  <td> 
											<select name="Process<?echo $i?>" id="Process<?echo $i?>"  class="input-sm" style="width:90px;height:30px;font-size:14px;border:1px solid #ccc">
												<option value="Y" <? if ("{$cate['cate_use']}"=="Y") { echo "selected"; }  ?>>사용</option>
												<option value="N" <? if ("{$cate['cate_use']}"=="N") { echo "selected"; }  ?>>중지</option>

											</select> 
											<button type="button" class='btn btn-secondary btn-sm' onClick=Status(document.reservation,'<? echo "{$cate['cate_id']}"; ?>','<?echo $i?>')  style="<?echo $permission_U_button?>">수정 </button>
											<?echo $permission_U_txt?>
										   
										  </td>									
								   <?}
							 }	// if ("{$cate['use_admin_role_cnt']}" > 0 ) if 끝
							 
							    if ("{$cate['cate_admin_role']}"==99) {
								echo "<td> 해당없음 </td>";
							    }else{ ?>
									
								<?	
									if ($_SESSION['admin_role'] >= 99) {
									echo "<td><button type='button' onclick=popup_win_m01('cate_edit',{$cate['cate_id']},400,380) style='cursor:pointer' class='btn btn-secondary btn-sm'>수정</button></td>";
									}
								}	
								$desc_start_no = $desc_start_no - 1;
							$i=$i+1;	
							}
							echo "</tr>";
						} else {
							//echo "<tr><td colspan='5'>등록된 제품 없음</td></tr>";
						}
					?>		
					</tbody>
				</table>

				<div style="width:100%;text-align:center"><?  echo paginate($totalItems, $itemsPerPage, $currentPage, $url);	?></div>	
	
				<div style="width:98%;text-align:right">
					<button type='button' class='btn btn-secondary btn-sm'  onclick="popup_win_m01_reg('cate',400,400)" style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:100%;height:50px;font-weight:bold' value="분류 등록">분류 등록</button>	
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