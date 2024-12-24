<?php  session_start();

// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// 이용자 데이터를 가져오는 SQL 쿼리
$sql = "SELECT user_id, user_name, user_addr FROM RTU_user where delYN = 'N' and partner_id='".$_SESSION['partner_id']."' ORDER BY user_idx DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이용자 선택목록</title>
    <style>
        table {
            width: 95%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        #searchID, #searchInstaller, #searchAddress {
            width: 95%;
            padding: 6px;
            margin-bottom: 12px;
        }
    </style>
	
    <script>
        // 이용자를 선택하면 부모 창에 전달하고 팝업 창을 닫음
        function selectInstaller(userId, installerName, userAddr) {
            // 부모 창의 setInstaller 함수를 호출하여 이용자 이름을 설정
            window.opener.setInstaller(userId, installerName, userAddr);
            // 팝업 창 닫기
            window.close();
        }
    </script>
</head>
<body>
    <h1>이용자 목록</h1>

    <!-- 검색 필드 -->
    <input type="text" id="searchID" placeholder="ID로 검색" onkeyup="filterTable()">
    <input type="text" id="searchInstaller" placeholder="이용자로 검색" onkeyup="filterTable()">
    <input type="text" id="searchAddress" placeholder="주소로 검색" onkeyup="filterTable()">
    
    <!-- 테이블 -->
    <table id="installerTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>이용자</th>
                <th>주소</th>
                <th>선택</th> <!-- 선택 버튼 열 추가 -->
            </tr>
        </thead>
        <tbody>
            <?php
            // 데이터 출력
            foreach ($installers as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_addr']) . "</td>";
                echo "<td><button onclick=\"selectInstaller('".htmlspecialchars($row['user_id'])."', '".htmlspecialchars($row['user_name'])."', '".htmlspecialchars($row['user_addr'])."')\">선택</button></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
    function filterTable() {
        // 입력 필드 값 가져오기
        var inputID = document.getElementById("searchID").value.toLowerCase();
        var inputInstaller = document.getElementById("searchInstaller").value.toLowerCase();
        var inputAddress = document.getElementById("searchAddress").value.toLowerCase();

        // 테이블과 행 가져오기
        var table = document.getElementById("installerTable");
        var rows = table.getElementsByTagName("tr");

        // 각 행을 순회하며 필터링
        for (var i = 1; i < rows.length; i++) {
            var idCell = rows[i].getElementsByTagName("td")[0];
            var installerCell = rows[i].getElementsByTagName("td")[1];
            var addressCell = rows[i].getElementsByTagName("td")[2];

            // 각 셀의 내용
            var idText = idCell.textContent.toLowerCase();
            var installerText = installerCell.textContent.toLowerCase();
            var addressText = addressCell.textContent.toLowerCase();

            // 필터에 따라 보여주기 또는 숨기기
            if (idText.indexOf(inputID) > -1 && installerText.indexOf(inputInstaller) > -1 && addressText.indexOf(inputAddress) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
    </script>

</body>
</html>
