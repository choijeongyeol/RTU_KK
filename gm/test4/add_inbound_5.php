<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>입고지시 추가</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    var i=0; 
    var max_num = 0;
    var flag_del_click = 0;
    // 추가 버튼 클릭 시 동적 필드 생성  
    $('#add_button').click(function(){
        if ((max_num!=0)&&(flag_del_click==1))  // 삭제버튼이 눌려진후, 추가버튼 누를시 1번만 max 카운트+2 하여, 증가함.
        {
            i = max_num + 2; flag_del_click = 0;
        }else{
            i = i+1;            
        }
        
        var html = '<div class="field_group">';
        html += '<div class="div_Num">상세정보'+i+'</div>';        
        html += '<div class="newDT_OUT"><div  class="myClass_lable"><label>제품명</label></div>';
        html += '<div class="myClass_data"><select name="product_name[]" class="product_select selectboxClass"></select></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable"><label>업체명</label></div>';
        html += '<div class="myClass_data"><select name="company_name[]" class="company_select selectboxClass"></select></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable"><label>창고명</label></div>';
        html += '<div class="myClass_data"><select name="warehouse_name[]" class="warehouse_select selectboxClass"><option value="" selected>선택하세요</option></select></div></div>';
        
        // 앵글 라벨과 선택박스를 감춤
        html += '<div class="newDT_OUT angle_section" ><div  class="myClass_lable angle_label"><label>앵글명</label></div>';
        html += '<div class="myClass_data angle_data"><select name="angle_name[]" class="angle_select selectboxClass"><option value="">창고선택후,보세요</option></select></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable"><label>예정수량</label></div>';
        html += '<div class="myClass_data"><input type="number" name="planned_quantity[]" class="tboxClass"></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_lable"><label>입고수량</label></div>';
        html += '<div class="myClass_data"><input type="number" name="inbound_quantity[]" class="tboxClass"></div></div>';
        
        html += '<div class="newDT_OUT"><div  class="myClass_data2"><button type="button" class="remove_button buttonClass">삭제</button></div></div>';
        html += '</div>';
        $('#field_container').append(html);

        // 동적으로 추가된 필드에 데이터 가져오기
        $.get("get_products.php", function(data){
            $('.product_select:last').html(data);
        });

        $.get("get_companies.php", function(data){
            $('.company_select:last').html(data);
        });

        $.get("get_warehouses.php", function(data){
            var warehouseSelect = $('.warehouse_select:last');
			data = "<option>창고를 선택하세요</option>"+data;
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
                            angleSelect.append('<option value="' + angle.angle_name + '">' + angle.angle_name + '</option>');
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
             $(this).find('.div_Num').text('상세정보' + (index + 1));
            max_num = index; 
            flag_del_click = 1; // 삭제버튼이 눌러졌음을 의미
        });     
    });

    // 저장 버튼 클릭 시 데이터 전송
    $('#save_button').click(function(){
        // 모든 창고와 앵글 선택여부 확인
        var isValid = true;
        $('.field_group').each(function() {
            var warehouseSelect = $(this).find('.warehouse_select');
            var angleSelect = $(this).find('.angle_select');
            if (warehouseSelect.val() === "" || angleSelect.val() === "") {
                isValid = false;
                return false; // 중단
            }
        });

        if (!isValid) {
            alert("선택하지 않은 필드가 있습니다. 선택하세요.");
            return; // 저장 중단
        }

        var formData = $('#inbound_form').serialize();
        $.post("save_inbound.php", formData, function(response){
            alert(response);
            // 추가 작업 수행 (예: 페이지 새로고침)
        });
    });
});
</script>
</head>

<style>
#field_container{display:inline-block}
.div_Num{font-size:14px;font-weight: bold;margin-top:10px;}
.newDT_OUT{ display: inline-block; border: 1px solid #EBEBF2; /* 요소를 인라인
처 배치 */ }
.myClass_lable{
 border-bottom: 2px solid #dee2e6;  width: 110px;  height: 40px;    vertical-align: middle;    color: #000;    background:#F9F9F9;display: table-cell;text-align:center;  border-bottom: 0px; font-size:14px; 
}

.label_class{width:100%;text-align:center}

.myClass_data{
 border-bottom: 2px solid #dee2e6;  width: 150px;  height: 40px;    vertical-align: middle;    color: #000;    background: rgba(52, 73, 94, 0.94);font-weight: bold;display: table-cell;background:#fff;;text-align:center;  border-bottom: 0px;  

}

.myClass_data2{
 border-bottom: 2px solid #dee2e6;  width: 260px;  height: 40px;    vertical-align: middle;    color: #000;    background: rgba(52, 73, 94, 0.94);font-weight: bold;display: table-cell;background:#fff;;text-align:center;  border-bottom: 0px;  

}


.tboxClass{ width:130px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#606070;}
.selectboxClass{ width:130px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#606070;}
.buttonClass{ width:240px;height:30px;text-align:center;border: 1px solid #EBEBF2;color:#fff;background:#ff0000;margin-top:5px}

</style>
<body>

<h2>입고지시 추가</h2>

<form id="inbound_form">
    <button type="button" id="add_button">추가</button>
    <button type="button" id="save_button">저장</button><br><br>

    <div id="field_container">
        <!-- 동적으로 생성될 필드들이 여기에 추가됩니다. -->
    </div>
</form>

</body>
</html>
