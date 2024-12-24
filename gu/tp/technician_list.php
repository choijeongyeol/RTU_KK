<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
?>

<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>

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

		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	