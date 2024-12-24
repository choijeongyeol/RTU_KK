<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Box 이벤트 처리</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    // select box가 변경됐을 때 이벤트 처리
    $('#mySelectBox').change(function(){
        var selectedValue = $(this).val();
        alert("선택한 값: " + selectedValue);
        // 추가로 필요한 작업 수행
    });
});
</script>
</head>
<body>

<select id="mySelectBox">
    <option value="">선택하세요.</option>
    <option value="option1">옵션 1</option>
    <option value="option2">옵션 2</option>
    <option value="option3">옵션 3</option>
</select>

</body>
</html>