<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 등록</title>
</head>
<body>
<h1>관리자 등록</h1>
<form action="process_partner.php" method="post">
    <!-- <label for="partner_id">파트너 ID:</label>
    <input type="text" id="partner_id" name="partner_id" required><br><br> -->

    <label for="admin_id">관리자 ID:</label>
    <input type="text" id="admin_id" name="admin_id" required><br><br>

    <label for="admin_name">관리자 이름:</label>
    <input type="text" id="admin_name" name="admin_name" required><br><br>

    <label for="admin_pw">비밀번호:</label>
    <input type="password" id="admin_pw" name="admin_pw" required><br><br>

    <label for="admin_role">관리자 역할:</label>
    <select id="admin_role" name="admin_role">
        <option value="100">관리자</option>
        <option value="9">일반 관리자</option>
    </select><br><br>

    <label for="admin_use">사용 여부:</label>
    <select id="admin_use" name="admin_use">
        <option value="Y">사용</option>
        <option value="N">사용 안 함</option>
    </select><br><br>

    <button type="submit">등록</button>
</form>
</body>
</html>
