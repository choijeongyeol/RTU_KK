<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>동적 필드 추가 및 저장</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<form id="dynamicForm">
    <div id="dynamicFields">
        <!-- 동적 필드가 여기에 추가됩니다. -->
    </div>
    <button type="button" id="addDynamicField">동적 필드 추가</button>
    <button type="submit" id="saveData">저장</button>
</form>

<script>
$(document).ready(function(){
    var counter = 1;

    // 동적 필드 추가
    $("#addDynamicField").click(function(){
        var newField = '<input type="text" name="field[]" id="field' + counter + '" required><br>';
        $("#dynamicFields").append(newField);
        counter++;
    });

    // 폼 제출 시 데이터 삽입
    $("#dynamicForm").submit(function(event){
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'save_data.php',
            type: 'POST',
            data: formData,
            success: function(response){
                alert(response);
                // 성공적으로 삽입된 경우, 필드 초기화
                $("#dynamicFields").empty();
                counter = 1;
            },
            error: function(xhr, status, error){
                alert("오류 발생: " + error);
            }
        });
    });
});
</script>
</body>
</html>
