<!DOCTYPE html>
<html>
<head>
    <title>데이터베이스에 데이터 추가하기</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<form id="myForm">
    이름: <input type="text" name="name" id="nameField"><br>
    이메일: <input type="email" name="email" id="emailField"><br>
    <button type="button" onclick="insertData()">데이터 추가</button>
</form>

<div id="result"></div>

<script>
function insertData() {
    var name = document.getElementById("nameField").value;
    var email = document.getElementById("emailField").value;
    
    $.ajax({
        url: "insert.php",
        type: "POST",
        data: { name: name, email: email },
        success: function(response) {
            $("#result").html(response);
        }
    });
}
</script>

</body>
</html>
