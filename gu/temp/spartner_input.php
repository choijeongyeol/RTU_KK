<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>지자체 입력</title>
</head>
<body>
    <h1>지자체 입력</h1>
    <form action="insert_spartner.php" method="post">
        <label for="spartner_name">지자체 이름:</label>
        <input type="text" id="spartner_name" name="spartner_name" required><br><br>

        <label for="spartner_tel">전화번호:</label>
        <input type="text" id="spartner_tel" name="spartner_tel" required><br><br>

        <label for="spartner_addr">주소:</label>
        <input type="text" id="spartner_addr" name="spartner_addr" required><br><br>

        <label for="spartner_addr2">상세 주소:</label>
        <input type="text" id="spartner_addr2" name="spartner_addr2"><br><br>

        <label for="spartner_email">이메일:</label>
        <input type="email" id="spartner_email" name="spartner_email" required><br><br>

        <label for="spartner_role">지자체 역할:</label>
        <select id="spartner_role" name="spartner_role">
            <option value="1" selected>기본 역할</option>
            <option value="2">특별 역할</option>
        </select><br><br>

        <label for="spartner_use">사용 여부:</label>
        <select id="spartner_use" name="spartner_use">
            <option value="Y" selected>사용</option>
            <option value="N">사용 안 함</option>
        </select><br><br>

        <button type="submit">등록</button>
    </form>
</body>
</html>
