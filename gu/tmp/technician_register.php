<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

$tid = $_GET['id'] ?? null;
$technician = null;

// 기존 담당자 정보를 가져오기
if ($tid) {
    try {
        $sql = "SELECT * FROM RTU_Technician WHERE tid = :tid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tid', $tid, PDO::PARAM_INT);
        $stmt->execute();
        $technician = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "오류 발생: " . $e->getMessage();
        exit;
    }
}

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $technician_id = $_POST['technician_id'];
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    try {
        if ($tid) {
            // 기존 담당자 정보 수정
            $sql = "UPDATE RTU_Technician 
                    SET technician_id = :technician_id, name = :name, contact_number = :contact_number, email = :email, updated_at = NOW() 
                    WHERE tid = :tid";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tid', $tid, PDO::PARAM_INT);
            echo "담당자가 성공적으로 수정되었습니다.";
        } else {
            // 새로운 담당자 등록
            $sql = "INSERT INTO RTU_Technician (technician_id, name, contact_number, email, created_at, updated_at)
                    VALUES (:technician_id, :name, :contact_number, :email, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            echo "담당자가 성공적으로 등록되었습니다.";
        }

        $stmt->bindParam(':technician_id', $technician_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

    } catch (PDOException $e) {
        echo "오류 발생: " . $e->getMessage();
        exit;
    }
} else {
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>담당자 <?= $tid ? '수정' : '등록' ?></title>
</head>
<body>
    <h2>담당자 <?= $tid ? '수정' : '등록' ?></h2>
    <form method="post" action="">
        <label for="technician_id">담당자 ID:</label>
        <input type="text" id="technician_id" name="technician_id" value="<?= htmlspecialchars($technician['technician_id'] ?? '') ?>" required <? if ($tid!="") {
        echo "readonly";  } ?> ><br>

        <label for="name">담당자 이름:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($technician['name'] ?? '') ?>" required><br>

        <label for="contact_number">연락처:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($technician['contact_number'] ?? '') ?>"><br>

        <label for="email">이메일:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($technician['email'] ?? '') ?>"><br>

        <button type="submit"><?= $tid ? '수정하기' : '등록하기' ?></button>
    </form>
</body>
</html>

<?php } ?>
