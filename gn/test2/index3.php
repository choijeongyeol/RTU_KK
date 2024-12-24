<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>동적 필드 추가 및 저장</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    var maxFields = 6; // 각 그룹당 최대 필드 수
    var addButton = $('.add_button');
    var wrapper = $('.field_wrapper');
    var fieldHTML = '<div class="field_group"><label>제품명</label><input type="text" name="product_name[]" class="form-control" required/><br><label>업체명</label><input type="text" name="company_name[]" class="form-control" required/><br><label>창고명</label><input type="text" name="warehouse_name[]" class="form-control" required/><br><label>앵글명</label><input type="text" name="angle_name[]" class="form-control" required/><br><label>예정수량</label><input type="text" name="planned_quantity[]" class="form-control" required/><br><label>입고수량</label><input type="text" name="incoming_quantity[]" class="form-control" required/><br><a href="javascript:void(0);" class="remove_button">Remove</a></div>'; // 필드 HTML
    
    $(addButton).click(function(){
        var fieldCount = $('.field_group').length;
        if(fieldCount < maxFields){ 
            $(wrapper).append(fieldHTML); // 필드 추가
        }
    });
    
    $(wrapper).on('click', '.remove_button', function(e){ // 필드 제거
        e.preventDefault();
        $(this).parent('.field_group').remove();
    });

    $('#save_button').click(function(){
        var formData = $('#myForm').serialize();
        $.ajax({
            url: 'save3.php',
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
</script>
</head>
<body>

<form id="myForm">
    <div class="field_wrapper"></div>
    <a href="javascript:void(0);" class="add_button" title="Add field">추가</a>
    <button type="button" id="save_button">저장</button>
</form>

</body>
</html>
