<?php
// DB 연결 설정 (이전에 만든 fn_api_RTU.php 파일에서 연결을 가져옵니다)
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$search_keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

try {
    // 사용자 목록 검색 쿼리
    $user_sql = "SELECT user_idx, user_id, user_name, user_tel, user_addr  FROM RTU_user WHERE user_name LIKE :keyword OR user_id LIKE :keyword";
    $stmt_user = $conn->prepare($user_sql);
    $stmt_user->bindValue(':keyword', '%' . $search_keyword . '%');
    $stmt_user->execute();
    $users = $stmt_user->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>사용자 검색</title>
    <script>
        function selectUser(user_idx, user_id, user_name) {
            // 부모창의 selectUser 함수 호출하여 사용자 ID와 이름을 전달
            opener.selectUser(user_idx,user_id, user_name);
            window.close();
        }
    </script>
</head>
<body>

<h2>사용자 검색</h2>
<form method="get" action="user_search_popup.php">
    <input type="text" name="keyword" placeholder="사용자명 또는 ID 검색" value="<?= htmlspecialchars($search_keyword) ?>">
    <button type="submit">검색</button>
</form>

<table border="1" cellpadding="8">
    <tr>
        <th>사용자 ID</th>
        <th>사용자명</th>
        <th>사용자 TEL</th>
        <th>사용자 ADDR</th>
        <th>선택</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['user_name']) ?></td>
            <td><?= htmlspecialchars($user['user_tel']) ?></td>
            <td><?= htmlspecialchars($user['user_addr']) ?></td>
            <td><button type="button" onclick="selectUser('<?= $user['user_idx'] ?>','<?= $user['user_id'] ?>', '<?= htmlspecialchars($user['user_name']) ?>')">선택</button></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
