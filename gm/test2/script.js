$(document).ready(function(){
    var maxFields = 16; // 각 그룹당 최대 필드 수
    var addButton = $('.add_button');
    var nextButton = $('.next_button');
    var wrapper = $('.field_wrapper');
    var fieldHTML = '<div class="field_group"><label>제품명</label><input type="text" name="product_name[]" class="form-control" required/>';  
    var fieldHTML = fieldHTML+'<label>업체명</label><select name="company_name[]" class="form-control" required/><option value="">업체선택</option></select>'; 
    var fieldHTML = fieldHTML+'<label>창고명</label><select name="warehouse_name[]" class="form-control warehouse_select" required ><option value="">창고선택</option></select>';
   // var fieldHTML = fieldHTML+' <a href="javascript:void(0);" class="next_button">창고선택완료</a> ';
    var fieldHTML = fieldHTML+'<label>앵글명</label><select name="angle_name[]" class="form-control angle_select" required ><option value="">앵글선택</option></select>';
    var fieldHTML = fieldHTML+'<label>예정수량</label><input type="text" name="planned_quantity[]" class="form-control" required/>';
    var fieldHTML = fieldHTML+'<label>입고수량</label><input type="text" name="incoming_quantity[]" class="form-control" required/>';
    var fieldHTML = fieldHTML+' <a href="javascript:void(0);" class="remove_button">Remove</a></div>';
    
    
		
    $(addButton).click(function(){

        var fieldCount = $('.field_group').length;

         if(fieldCount < maxFields){ 
            $(wrapper).append(fieldHTML); // 필드 추가
            
			
            // 업체명 select box 업데이트
            $.ajax({
                url: 'get_company.php',
                type: 'GET',
                success: function(response){
                    $('select[name="company_name[]"]').last().html(response);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.error('Error: ' + textStatus + ' - ' + errorThrown);
                }
            });			
			
            // 창고명 select box 업데이트
            $.ajax({
                url: 'get_warehouses.php',
                type: 'GET',
                success: function(response){
                    $('select[name="warehouse_name[]"]').last().html(response);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.error('Error: ' + textStatus + ' - ' + errorThrown);
                }
            });
				
            // 앵글명 select box 업데이트
            $.ajax({
                url: 'get_angles.php',
                type: 'GET',
                success: function(response){
                    $('select[name="angle_name[]"]').last().html(response);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.error('Error: ' + textStatus + ' - ' + errorThrown);
                }
            });

        }
    });
 

 
	
	$(document).on('change', '.warehouse_select', function(){
		var warehouse_id = $(this).val();
        var $angle_select = $(this).closest('.field_group').find('.angle_select');
		
        // AJAX 요청을 통해 선택한 창고와 관련된 앵글 정보 가져오기
        $.ajax({
            url: 'get_angle_by_warehouse.php',
            type: 'GET',
            data: { warehouse_id: warehouse_id },
            success: function(response){
                // 받아온 데이터를 셀렉트 박스로 업데이트
                $angle_select.empty(); // 기존 옵션 삭제
                $.each(response, function(index, angle){
                    $angle_select.append('<option value="' + angle.id + '">' + angle.angle_name + '</option>');
                });
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.error('Error: ' + textStatus + ' - ' + errorThrown);
            }
        });
	
	});
 

 
    $(wrapper).on('click', '.remove_button', function(e){ // 필드 제거
        e.preventDefault();
        $(this).parent('.field_group').remove();
    });

    $('#save_button').click(function(){
        var formData = $('#myForm').serialize();
        $.ajax({
            url: 'save4.php',
            type: 'POST',
            data: formData,
            success: function(response){
                alert(response);
                // 저장 성공 후 추가 동작 수행
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert('Error: ' + textStatus + ' - ' + errorThrown);
            }
        });
    });
});
