<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>담당자 목록</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<h2>담당자 목록</h2>
<a href="technician_register.php" class="btn">새 담당자 등록</a>
<br><br>

<table>
    <tr>
        <th>담당자 ID</th>
        <th>이름</th>
        <th>연락처</th>
        <th>이메일</th>
        <th>생성일</th>
        <th>수정일</th>
        <th>수정</th>
    </tr>

    <?php
    $sql = "SELECT * FROM RTU_Technician";
    $stmt = $conn->query($sql);
    $technicians = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($technicians as $technician) {
        echo "<tr>
                <td>{$technician['technician_id']}</td>
                <td>{$technician['name']}</td>
                <td>{$technician['contact_number']}</td>
                <td>{$technician['email']}</td>
                <td>{$technician['created_at']}</td>
                <td>{$technician['updated_at']}</td>
                <td><a href='technician_register.php?id={$technician['tid']}' class='btn'>수정</a></td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
