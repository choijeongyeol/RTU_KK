<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

// 삭제 요청이 있는 경우 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_idx'])) {
    $delete_user_idx = $_POST['delete_user_idx'];

    try {
        $sql = "UPDATE RTU_user SET delYN = 'Y' WHERE user_idx = :user_idx";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_idx', $delete_user_idx, PDO::PARAM_INT);
        $stmt->execute();
        echo "<script>alert('사용자가 삭제되었습니다.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('삭제 중 오류가 발생했습니다: " . $e->getMessage() . "');</script>";
    }
}

// 이용자 목록 조회 쿼리
try {
    $sql = "
        SELECT 
            user_idx,
            partner_id,
            user_id,
            user_name,
            user_tel,
            user_email,
            user_role,
            sms_receive,
            email_receive,
            user_rdate,
            user_use,
            delYN
        FROM RTU_user
        WHERE delYN = 'N'  -- 삭제되지 않은 사용자만 조회
        ORDER BY user_rdate DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>이용자 목록</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .delete-button { color: white; background-color: red; padding: 5px 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>이용자 목록</h2>

<table>
    <tr>
        <th>사용자 ID</th>
        <th>사용자 이름</th>
        <th>전화번호</th>
        <th>이메일</th>
        <!-- <th>역할</th> -->
        <th>SMS 수신</th>
        <th>이메일 수신</th>
        <th>등록 날짜</th>
        <th>사용 여부</th>
        <th>삭제</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['user_name']) ?></td>
            <td><?= htmlspecialchars($user['user_tel']) ?></td>
            <td><?= htmlspecialchars($user['user_email']) ?></td>
            <!-- <td><?= htmlspecialchars($user['user_role'] == 1 ? '관리자' : '일반 사용자') ?></td> -->
            <td><?= $user['sms_receive'] == 1 ? 'Y' : 'N' ?></td>
            <td><?= $user['email_receive'] == 1 ? 'Y' : 'N' ?></td>
            <td><?= htmlspecialchars($user['user_rdate']) ?></td>
            <td><?= $user['user_use'] == 'Y' ? '사용 중' : '사용 안 함' ?></td>
            <td>
                <form method="post" onsubmit="return confirm('정말 이 사용자를 삭제하시겠습니까?');">
                    <input type="hidden" name="delete_user_idx" value="<?= $user['user_idx'] ?>">
                    <button type="submit" class="delete-button">삭제</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
