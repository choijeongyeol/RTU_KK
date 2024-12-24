<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>
	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/sidebar_menu.php'); ?>	
	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/top_navigation.php'); ?>
	
    <?  /// 권한 체크 : 조회권한 ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_R = permission_ck('출고지시관리','R',$_SESSION['admin_role']);
	 if ($pm_rst_R == 'F') {	 echo "<script>alert('출고지시관리조회 권한이 없습니다.');location.href='/gn/home/dashboard.php'</script>"; exit();	 }

	   /// 권한 체크 : 등록권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_W = permission_ck('출고지시관리','W',$_SESSION['admin_role']); if ($pm_rst_W == 'F') {  $permission_W_button = "display:none;"; $permission_W_txt = "출고지시관리등록권한없음"; }

       /// 권한 체크 : 수정권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_U = permission_ck('출고지시관리','U',$_SESSION['admin_role']); if ($pm_rst_U == 'F') {  $permission_U_button = "!"; $permission_U_txt = "출고지시관리수정권한없음"; }
 	   
       /// 권한 체크 : 삭제권한 - display:none  ///////////////////////////////////////////////////////////////////////////////////////////////
	 $pm_rst_D = permission_ck('출고지시관리','D',$_SESSION['admin_role']); if ($pm_rst_D == 'F') {  $permission_D_button = "display:none;"; $permission_D_txt = "<BR>출고지시관리삭제권한없음"; }


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
 
 <style>
	body {
		font-family: Arial, sans-serif;
		background-color: #f2f2f2;
		margin: 0;
		padding: 0;
	}
	h1 {
		text-align: center;
		margin-top: 20px;
		color: #333;
	}
	#add_button , #reset {
		display: inline;
		margin: 20px auto;
		padding: 5px 10px;
		background-color: #007bff;
		color: #fff;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	}
	#reset{
		background-color: #9a36b4;
	}
	#field_container {
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-start;
		margin-top: 10px;
	}
	.field_group {
		flex-basis: 100%; /* 각 항목이 한 줄에 하나씩 표시되도록 설정 */
		background-color: #fff;
		padding: 5px;
		border-radius: 6px;
		box-shadow: 0 0 1px rgba(0, 0, 0, 0.1);
		margin: 5px;
	}
	select, input {
		width: 200px;
		margin: 5px;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
		font-size: 16px;
	}
	.add_padding{
		padding: 10px;
	}
	.delete-btn {
		color: red;
		cursor: pointer;
	}
	#save_button {
		display: block;
 		background-color: #28a745;
		color: #fff;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	}
	.stockQuantity {
		width: 200px;
		padding: 10px;
		margin: 5px;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
		font-size: 16px;
	}	
</style>

 <script>
	function reload(){
		location.href="/gn/m06/write.php";
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
                    <h2>출고지시관리 > 출고지시 생성<small></small></h2> 
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                     <div class="row">
						<div class="col-sm-12">
							<div class="card-box table-responsive">
							 <form id="outbound_form">
							 <input type="hidden" name="page_name" value="outbound_write" alt="출고지시 신규등록">
								<p class="text-muted font-13 m-b-30">
								  출고지시를 관리하실 수 있습니다.
								</p>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
								<p class="text-muted font-13 m-b-30">
								  <b>출고지시</b>
								</p>								
									<table id="tb_border" class="table-striped  dataTable dataCustomTable"   aria-describedby="datatable_info" style="border:0px">	
									<thead>								
									<tr>
										<th style="width:150px">출고예정일</th>
										<td style="text-align:left;background:#eee"><input  class='date' type="date" name="plan_date" required  style="width:150px;height:100%;border:1px solid #D8D8D8;text-align:center"></td>
									</tr>
									</thead>									
									</table>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
									<br><br> 
									 
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
								<p class="text-muted font-13 m-b-30" style="text-align: left;">
								  <b>출고지시 상세&nbsp; <button type='button' id="add_button"> 추 가 </button> <button type='button'   id="reset"  onclick="reload()"> 초 기 화</button>
								  </b> 
								</p>
								
								<div id="detail_box" style="display:none">
								<p class="text-muted font-13 m-b-30">
								 상세)
								</p>									
								<table  id="tb_border2"  style="border:1px ">	
									 							
									<!-- <tr>
										<th style="width:150px">출고예정일</th>
										<td style="text-align:left;background:#eee"><input  class='date' type="date" name="plan_date"   style="width:150px;height:100%;border:1px solid #D8D8D8;align:left"></td>
									</tr> -->
								 									
								</table>
								</div>
								<!-- ////////////////////////////////////////////////////////////////////////////////// -->
 
 
	<div id="field_container">
		<!-- 초기 입력항목 레코드 -->
		<div class="field_group">

			<select  name="company_id[]" class="companySelect add_padding" style="display: none;">
				<option value="">거래처 선택</option>
			</select>
			
			<select name="product_id[]" class="itemSelect add_padding" style="display: none;">
				<option value="">제품 선택</option>
			</select>

			<select name="warehouse_id[]" class="warehouseSelect add_padding" style="display: none;">
				<option value="">창고 선택</option>
			</select>

			<select name="angle_id[]"  class="angleSelect add_padding" style="display: none;">
				<option value="">앵글 선택</option>
			</select>

			<span class="stockQuantity" style="display: none;"></span>

			<input type="number" name="planned_quantity[]"  class="planned_quantity add_padding" style="display: none;" min="1" placeholder='출고예정수량' required>
			<input type="number" name="outbound_quantity[]" class="outbound_quantity add_padding" style="display: none;" min="1" placeholder='출고수량'>
			<span class="delete-btn deleteRealStockCnt add_padding" style="display: none;">X</span> <!-- 삭제 버튼 -->
		</div>
	</div>

    <!-- 저장 버튼 -->
    <!-- <button id="save_button">저장</button>
 -->
    <script>
        $(document).ready(function() {
			 // 저장 버튼 초기 숨김
			$("#save_button").hide();
			
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

                var newRecord = $("#field_container .field_group").first().clone(); // 초기 입력항목 레코드 복제
                $("#field_container").append(newRecord); // 새로운 입력항목 레코드 추가

				// 새로운 입력항목이 추가되었으므로 저장 버튼을 숨기기 설정
				$("#save_button").hide();


                // 새로 추가된 레코드의 내용 초기화
                newRecord.find("select, input").val(""); // 모든 선택 요소의 값을 초기화
                newRecord.find("select, input").hide(); // 모든 선택 요소 숨기기
                newRecord.find(".companySelect").show(); // 제품 선택 selectbox는 항목 추가 시 보이도록 함
                 newRecord.find(".itemSelect").show(); // 제품 선택 selectbox는 항목 추가 시 보이도록 함
               newRecord.find(".deleteRealStockCnt").hide(); // 삭제 버튼 숨기기
                newRecord.find(".stockQuantity").hide().text(""); // 보유수량 숨기고 내용 초기화
            });

            // 페이지 로드 시 거래처 목록을 불러옵니다.
            $.get("1_get_companies.php", function(data) {
                $(".companySelect").append(data);
            });

            // 거래처 선택 시 제품 목록을 불러옵니다.
            $(document).on("change", ".companySelect", function() {
                var company_id = $(this).val();
                var itemSelect = $(this).siblings(".itemSelect");
                $.post("1_get_items.php", { company_id: company_id }, function(data) {
                    itemSelect.show().html(data);
                    //itemSelect.siblings(".warehouseSelect, .angleSelect, .stockQuantity, .planned_quantity, .outbound_quantity, .deleteRealStockCnt").hide().find("option").remove();
                });
            });

            // 아이템 선택 시 해당 범위 내의 창고 목록을 불러옵니다.
            $(document).on("change", ".itemSelect", function() {
                var company_id = $(this).siblings(".companySelect").val();
                var item_id = $(this).val();
                var warehouseSelect = $(this).siblings(".warehouseSelect");
                $.post("1_get_warehouses.php", { item_id: item_id, company_id: company_id }, function(data) {
                    warehouseSelect.show().html(data);
                    warehouseSelect.siblings(".angleSelect, .stockQuantity, .planned_quantity, .outbound_quantity, .deleteRealStockCnt").hide().find("option").remove();
                });
            });

            // 창고 선택 시 앵글 목록을 불러옵니다.
            $(document).on("change", ".warehouseSelect", function() {
                var item_id = $(this).siblings(".itemSelect").val();
                var company_id = $(this).siblings(".companySelect").val();
                var warehouse_id = $(this).val();
                var angleSelect = $(this).siblings(".angleSelect");
                $.post("1_get_angles.php", { item_id: item_id, company_id: company_id, warehouse_id: warehouse_id}, function(data) {
                    angleSelect.show().html(data);
                    angleSelect.siblings(".stockQuantity, .planned_quantity, .outbound_quantity, .deleteRealStockCnt").hide(); // 앵글을 선택하면 보유수량 표시 숨김
                });
            });

            // 앵글 선택 시 해당 앵글의 보유수량을 표시하고 입력 폼을 표시합니다.
            $(document).on("change", ".angleSelect", function() {
                var item_id = $(this).siblings(".itemSelect").val();
                var company_id = $(this).siblings(".companySelect").val();
                var warehouse_id = $(this).siblings(".warehouseSelect").val();
                var angle_id = $(this).val();
                var stockQuantity = $(this).siblings(".stockQuantity");
                var planned_quantity = $(this).siblings(".planned_quantity");
                var outbound_quantity = $(this).siblings(".outbound_quantity");
                var deleteRealStockCnt = $(this).siblings(".deleteRealStockCnt");

                $.post("1_get_stock_quantity.php", {  item_id: item_id, company_id: company_id, warehouse_id: warehouse_id, angle_id: angle_id }, function(data) {
                    stockQuantity.show().html("보유수량: " + data);
                    planned_quantity.show().attr("min", 0).val(""); // 보유수량 입력 폼을 표시하고 초기화
                    outbound_quantity.show().attr("min", 0).val(""); // 보유수량 입력 폼을 표시하고 초기화
                    deleteRealStockCnt.show(); // 삭제 버튼 표시
                });
            });

            // 삭제 버튼 클릭 시 해당 입력항목 레코드 삭제
            $(document).on("click", ".deleteRealStockCnt", function() {
                $(this).closest(".field_group").remove();
				
				//alert($("#field_container .field_group").length);
				// 모든 입력항목이 제거되면 저장 버튼을 다시 숨김
				if ($("#field_container .field_group").length === 1) {
					$("#save_button").hide();
				}				
				
            });

            // 출고예정 입력 시 입력값을 확인하여 제약 조건을 적용합니다.
            $(document).on("input", ".planned_quantity", function() {
                var inputVal = parseInt($(this).val());
                var stockQuantity = parseInt($(this).siblings(".stockQuantity").text().split(": ")[1]);
                if (inputVal <= 0) {
                    alert("1 이상의 값을 입력하세요.");
                    $(this).val("");
                } else if (inputVal > stockQuantity) {
                    alert("보유수량보다 작거나 같은 값을 입력하세요.");
                    $(this).val("");
                }
                $(this).siblings(".outbound_quantity").attr("max", inputVal); // 출고수량 입력칸의 최대값 설정
            });

            // 출고수량 입력 시 입력값을 확인하여 제약 조건을 적용합니다.
            $(document).on("input", ".outbound_quantity", function() {
                var inputVal2 = parseInt($(this).val());
                var stockQuantity = parseInt($(this).siblings(".stockQuantity").text().split(": ")[1]);
                if (inputVal2 <= 0) {
                    alert("1 이상의 값을 입력하세요.");
                    $(this).val("");
                } else if (inputVal2 > stockQuantity) {
                    alert("보유수량보다 작거나 같은 값을 입력하세요.");
                    $(this).val("");
                }
                $(this).attr("max", inputVal2); // 출고수량 입력칸의 최대값 설정
            });

            // 저장 버튼 클릭 시 처리
            $("#save_button").click(function() {

				// 출고예정일 선택여부 확인
				var planDateInput = $('[name="plan_date"]');
				var isValid = true;
				if (planDateInput.val() === "") {
					isValid = false;
					alert("출고예정일을 선택하세요.");
				}else{
					
					// planned_quantityInputs의 각 요소에 대해 값이 비어 있는지 확인
 
					
					// 오늘 날짜를 가져옵니다.
					var today = new Date();
					today.setHours(0, 0, 0, 0);
					// 입력된 날짜를 가져옵니다.
					var planDate = new Date(planDateInput.val());
					// 입력된 날짜가 오늘 날짜보다 이전인지 확인합니다.
					if (planDate < today) {
						isValid = false;
						alert("출고예정일은 오늘 날짜보다 이전일 수 없습니다.");
					}			
				}

				if (!isValid) {
					return; // 저장 중단
				}		
								
				var formData = $('#outbound_form').serialize(); // 폼 데이터 시리얼라이즈
				$.post("../inc/fn.php", formData, function(response) {
				var cleanedString = response.replace(/[\t→]/g, '');  //  탭과 화살표가 제거된 문자열이 저장됩니다.		
				alert(cleanedString);  // 응답 메시지 출력
					location.href = "/gn/m06/list.php"; // 목록 페이지로 이동
				});
            });
        });
    </script> 
 
 
 
 
 <!-- 
								<div id="field_container">
				  
								</div>
											 -->		
	 
									
									<br><br><!-- <br><br> -->
									
				
				<div style="width:98%;text-align:right">
				<table align="center" width="400px" >
				<tr>
					<td>					<button type='button' class='btn btn-info btn-sm'  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:90%;height:50px;font-weight:bold' value="대상 등록" id="save_button">저장</button>	
					</td>
					<td>					<a href="/gn/m06/list.php"><button type='button' class='btn btn-secondary btn-sm'  style='<?echo $permission_W_button?>margin-top:120px;padding:12;width:90%;height:50px;font-weight:bold' value="대상 등록">목록</button></a>	
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