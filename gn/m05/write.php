<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>
	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('입고지시관리','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('입고지시관리조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('입고지시관리','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "입고지시관리등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('입고지시관리','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "입고지시관리수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('입고지시관리','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>입고지시관리삭제권한없음"; }


	   $result_setting = getwms_setting_state('1'); // 창고앵글 일괄삭제  set_id 값 1	   
	?>	
	
		
	<!-- 게시판 리스트 계산 start -->
	<?
	// 검색결과 추가조건 sql
	if ($_POST['search']!="") {
		$add_condition = " and  ".$_POST['search']." like '%".$_POST['SearchString']."%'";
	}else{
		$add_condition = "";
	}
 
	//$list_condition = "wms_plan_stock where delYN = 'N' ".$add_condition;
	//$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
	?>	
	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/paging_cnt.php'); ?>
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>			
	<!-- 게시판 리스트 계산 end -->



<!-- <button onclick="addNewTag()">새로운 태그 추가하기</button> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    var i=0; 
	var max_num = 0;
	var flag_del_click = 0;
	var del_never = 1; // 삭제버튼을 누른적이 없음 = 1, 있음 = 0
	var add_cnt = 0;
    // 추가 버튼 클릭 시 동적 필드 생성  
    $('#add_button').click(function(){
 
        if (del_never==1)  // 초기, 삭제누른적 없음
        {
			i = i+1;
        }else if(max_num==1){
			add_cnt = max_num+1;
			i = add_cnt;
			max_num = add_cnt;					
        }else{   // 삭제 누른적있음
			add_cnt = max_num+1;
			i = add_cnt;
			max_num = add_cnt;
        } 
 
        var html = '<div class="field_group">';
        html += '<div class="div_Num">상세입력</div>';        
        html += '<div class="newDT_OUT"><div  class="myClass_lable ess">제품명</div>';
        html += '<div class="myClass_data"><select name="product_id[]" class="product_select selectboxClass"></select></div></div>';
        
        //html += '<div class="newDT_OUT"><div  class="myClass_lable">업체명</div>';
        //html += '<div class="myClass_data"><select name="company_id[]" class="company_select selectboxClass"></select></div></div>';
        
		html += '<input type="hidden" name="company_id[]" value=0 >';
		
        html += '<div class="newDT_OUT"><div  class="myClass_lable ess">창고명</div>';
        html += '<div class="myClass_data"><select name="warehouse_id[]" class="warehouse_select selectboxClass"></select></div></div>';
 
        html += '<div class="newDT_OUT angle_section" ><div  class="myClass_lable angle_label ess">앵글명</div>';
        html += '<div class="myClass_data angle_data"><select name="angle_id[]" class="angle_select selectboxClass"><option value="">창고선택후,택1</option></select></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable ess">예정수량</div>';
        html += '<div class="myClass_data"><input type="number" name="planned_quantity[]" class="tboxClass planned_quantity" required></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable">입고수량</div>';
        html += '<div class="myClass_data"><input type="number" name="inbound_quantity[]" class="tboxClass"></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_data2"><button type="button" class="remove_button buttonClass">삭제</button></div></div>';
        html += '</div>';
        $('#field_container').append(html);

        // 동적으로 추가된 필드에 데이터 가져오기
        $.get("get_products.php", function(data){
           var productSelect = $('.product_select:last');		
			     data = "<option>제품 택1</option>"+data;
            productSelect.html(data);	   
            //$('.product_select:last').html(data);
        });

        //$.get("get_companies.php", function(data){
			   //  data = "<option value=''>미선택</option>"+data;			
         //   $('.company_select:last').html(data);
        //});

        $.get("get_warehouses.php", function(data){
            var warehouseSelect = $('.warehouse_select:last');
			     data = "<option>창고 택1</option>"+data;
            warehouseSelect.html(data);

            // 창고가 변경될 때마다 해당 창고의 앵글 목록을 가져와서 업데이트
            warehouseSelect.change(function(){
                var selectedWarehouse = $(this).val();
                var angleSection = $(this).closest('.field_group').find('.angle_section');
                if (selectedWarehouse !== "") {
                    angleSection.show();
                    var angleSelect = angleSection.find('.angle_select');
                    angleSelect.empty(); // 기존의 옵션들 제거

                    // 선택된 창고에 해당하는 앵글 목록 가져오기
                    $.get("get_angles.php?warehouse_id=" + selectedWarehouse, function(data){
                        // JSON 데이터 파싱
                        var angles = JSON.parse(data);

                        // 파싱된 데이터로부터 옵션 추가
                        $.each(angles, function(index, angle) {
                            //angleSelect.append('<option value="' + angle.angle_name + '">' + angle.angle_name + '</option>');
                            angleSelect.append('<option value="' + angle.angle_id + '">' + angle.angle_name + '</option>');
                        });
                    });
                } else {
                    angleSection.hide();
                }
            });
        });
    });

    // 삭제 버튼 클릭 시 해당 필드 삭제
    $(document).on('click', '.remove_button', function(){
        $(this).closest('.field_group').remove();
        // 상세정보 번호 재설정
        $('.field_group').each(function(index) {
            $(this).find('.div_Num').text('상세입력');
			max_num = index+1; 
			flag_del_click = 1; // 삭제버튼이 눌러졌음을 의미
			del_never = 0;
        });     
    });

	// 저장 버튼 클릭 시 데이터 전송
	$('#save_button').click(function(){
 	
		// 입고예정일 선택여부 확인
		var planDateInput = $('[name="plan_date"]');
		var isValid = true;
		if (planDateInput.val() === "") {
			isValid = false;
			alert("입고예정일을 선택하세요.");
		}else{
			// 오늘 날짜를 가져옵니다.
			var today = new Date();
			today.setHours(0, 0, 0, 0);
			// 입력된 날짜를 가져옵니다.
			var planDate = new Date(planDateInput.val());
			// 입력된 날짜가 오늘 날짜보다 이전인지 확인합니다.
			if (planDate < today) {
				isValid = false;
				alert("입고예정일은 오늘 날짜보다 이전일 수 없습니다.");
			}			
		}

		if (!isValid) {
			return; // 저장 중단
		}		
 		
 
		
		// 모든 필드가 비어 있는지 확인
		var isEmpty = true;
		$('.field_group').each(function() {
			var productSelect = $(this).find('.product_select');
			var warehouseSelect = $(this).find('.warehouse_select');
			var angleSelect = $(this).find('.angle_select');
			var plannedQuantityInput = $(this).find('[name="planned_quantity[]"]');
			
			// 필수 입력 필드가 비어 있는지 확인
			if (productSelect.val() !== "" || warehouseSelect.val() !== "" || angleSelect.val() !== "" || plannedQuantityInput.val() !== "") {
				isEmpty = false;
				return false; // 입력된 필드가 하나라도 있으면 중단
			}
		});		
			
		// 필드가 비어 있는 경우 알림 표시
		if (isEmpty) {
			alert("입력된 내용이 없습니다. 추가 버튼을 눌러주세요.");
			return; // 저장 중단
		}		
			

		
		
		// 모든 창고와 앵글 선택여부 확인
		var isValid = true;
		$('.field_group').each(function() {
			var productSelect = $(this).find('.product_select');
			var warehouseSelect = $(this).find('.warehouse_select');
			var angleSelect = $(this).find('.angle_select');
			var plannedQuantityInput = $(this).find('[name="planned_quantity[]"]');
			
 
			// 필수 입력 필드가 비어 있는지 확인
			if (productSelect.val() === "") {
				isValid = false;
				// 사용자에게 경고 표시
				alert("제품명을 선택하세요.");
				return false; // 중단
			}else if (warehouseSelect.val() === "" || angleSelect.val() === "") {
				isValid = false;
				// 사용자에게 경고 표시
				alert("창고명을 선택하세요.");
				return false; // 중단
			}else if(plannedQuantityInput.val() === ""){
				isValid = false;
				// 사용자에게 경고 표시
				alert("예정수량을 입력하세요.");
				return false; // 중단				
			}
		});

		if (!isValid) {
			return; // 저장 중단
		}

		var formData = $('#inbound_form').serialize();
		$.post("../inc/fn.php", formData, function(response){		
			var cleanedString = response.replace(/[\t→]/g, '');  //  탭과 화살표가 제거된 문자열이 저장됩니다.		
			alert(cleanedString); location.href="/gn/m05/list.php";
			// 추가 작업 수행 (예: 페이지 새로고침)
		});
	});
});

function reload(){
	location.href="write.php";
}
</script>

<style>
#field_container{display:inline-block}
.div_Num{font-size:14px;font-weight: bold;margin-top:10px;}
.newDT_OUT{ display: inline-block; border: 1px solid #EBEBF2; /* 요소를 인라인처럼 배치 */ }
.myClass_lable{
 border-bottom: 2px solid #dee2e6;  width: 90px;  height: 40px;    vertical-align: middle;    color: #000;    background:#F9F9F9;display: table-cell;text-align:center;  border-bottom: 0px; font-size:12px; 
}

.myClass_lable.ess:after {content:""; color:#ff0000; margin-left:5px; width:10px; height:10px; background:transparent url('/gn/images/ess.svg') left center no-repeat; display:inline-block;}
.myClass_lable.ess:after {background-size:8px;}


.label_class{width:100%;text-align:center}

.myClass_data{
 border-bottom: 2px solid #dee2e6;  width: 150px;  height: 40px;    vertical-align: middle;    color: #000;    background: rgba(52, 73, 94, 0.94);font-weight: bold;display: table-cell;background:#fff;;text-align:center;  border-bottom: 0px;  

}

.myClass_data2{
 border-bottom: 2px solid #dee2e6;  width: 240px;  height: 40px;    vertical-align: middle;    color: #000;    background: rgba(52, 73, 94, 0.94);font-weight: bold;display: table-cell;background:#fff;;text-align:center;  border-bottom: 0px;  

}


.tboxClass{ width:130px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#606070;}
.selectboxClass{ width:130px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#606070;}
.buttonClass{ width:220px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#fff;background:#ff0000;margin-top:5px}

</style>							
     <script>
        $(document).ready(function() {
			 // 저장 버튼 초기 숨김
			$("#save_button").hide();
			
			// 출고예정 입력 시 입력값을 확인하여 저장 버튼을 표시합니다.
			$(document).on("input", ".planned_quantity", function() {
				var inputVal = parseInt($(this).val());
				if (inputVal > 0) {
					$("#save_button").show();
				}
			});		
	
			// 출고예정 입력 시 입력값을 확인하여 저장 버튼을 표시합니다.
			$(document).on("input", ".planned_quantity", function() {
				var inputVal = parseInt($(this).val());
				if (inputVal > 0) {
					$("#save_button").show();
					$("#add_button").show();					
				}else{
					$("#save_button").hide();
					$("#add_button").hide();					
				}
			});		
			
            // 항목 추가 버튼 클릭 시 새로운 입력항목 레코드 생성
            $("#add_button").click(function() {
				$("#add_button").hide();				
				// 새로운 입력항목이 추가되었으므로 저장 버튼을 숨기기 설정
				$("#save_button").hide();
            });			
			
        });
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
                    <h2>입고지시관리 > 입고지시 생성<small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
							 <form id="inbound_form">
							 <input type="hidden" name="page_name" value="inbound_write" alt="입고지시 신규등록">
								<p class="text-muted font-13 m-b-30">
								  입고지시를 관리하실 수 있습니다.
								</p>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
								<p class="text-muted font-13 m-b-30">
								  <b>입고지시</b>
								</p>								
									<table id="tb_border" class="table-striped  dataTable dataCustomTable"   aria-describedby="datatable_info" style="border:0px">	
									<thead>								
									<tr>
										<th style="width:150px">입고예정일</th>
										<td style="text-align:left;background:#eee"><input  class='date' type="date" name="plan_date" required  style="width:150px;height:100%;border:1px solid #D8D8D8;text-align:center"></td>
									</tr>
									</thead>									
									</table>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
									<br><br> 
									
									
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
								<p class="text-muted font-13 m-b-30">
								  <b>입고지시 상세&nbsp; <button type='button' class='btn btn-info btn-sm  font-13 m-b-30' style='padding:2;width:61px;color:#fff' id="add_button"> 추 가 </button> <button type='button' class='btn btn-info btn-sm  font-13 m-b-30' style='padding:2;width:81px;height:33px;color:#fff;background:#a91fa6;border:0;border-color:#970d94;'  onclick="reload()"> 초 기 화</button></b> (추가버튼 누른후,  입고지시할 대상품목이 여기 안보이거나, 신규제품은 먼저 제품관리에서 등록 후 이용바랍니다.)
								</p>
								
								<div id="detail_box" style="display:none">
								<p class="text-muted font-13 m-b-30">
								 상세)
								</p>									
								<table  id="tb_border2"  style="border:1px ">	
									 							
									<!-- <tr>
										<th style="width:150px">입고예정일</th>
										<td style="text-align:left;background:#eee"><input  class='date' type="date" name="plan_date"   style="width:150px;height:100%;border:1px solid #D8D8D8;align:left"></td>
									</tr> -->
								 									
								</table>
								</div>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
 
 
								<div id="field_container">
								  <!-- 클릭할 때마다 여기에 새로운 태그가 추가됩니다. -->
								</div>
													
	 
									
									<br><br><!-- <br><br> -->
									
				
				<div style="width:98%;text-align:right">
				<table align="center" width="400px">
				<tr>
					<td>					<button type='button' class='btn btn-info btn-sm'  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:90%;height:50px;font-weight:bold' value="대상 등록" id="save_button">저장</button>	
					</td>
					<td>					<a href="/gn/m05/list.php"><button type='button' class='btn btn-secondary btn-sm'  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:90%;height:50px;font-weight:bold' value="대상 등록">목록</button></a>	
					</td>
				</tr>
				</table>
				</div>
 							</form>	 
  
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