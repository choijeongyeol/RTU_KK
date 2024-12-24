<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');

// POST 요청 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 입력 데이터 가져오기
    $partner_id = $_SESSION['partner_id'] ?? null; // 세션에서 partner_id 가져오기
    $admin_id = $_POST['admin_id'] ?? null;
    $admin_name = $_POST['admin_name'] ?? null;
    $admin_pw = $_POST['admin_pw'] ?? null;
    $admin_role = $_POST['admin_role'] ?? null;
    $admin_use = $_POST['admin_use'] ?? 'Y';
    $admin_rdate = date('Y-m-d H:i:s'); // 현재 날짜 및 시간

    // 필수값 확인
    if (empty($partner_id) || empty($admin_id) || empty($admin_name) || empty($admin_pw) || empty($admin_role)) {
        die("필수 입력값이 누락되었습니다.");
    }

    try {
        // 비밀번호 암호화
        $hashed_pw = password_hash($admin_pw, PASSWORD_DEFAULT);

        // 데이터 삽입 쿼리
        $sql = "INSERT INTO RTU_admin (
                    partner_id, admin_id, admin_name, admin_pw, admin_role, admin_use, delYN, admin_rdate
                ) VALUES (
                    :partner_id, :admin_id, :admin_name, :admin_pw, :admin_role, :admin_use, 'N', :admin_rdate
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':admin_name', $admin_name);
        $stmt->bindParam(':admin_pw', $hashed_pw);
        $stmt->bindParam(':admin_role', $admin_role);
        $stmt->bindParam(':admin_use', $admin_use);
        $stmt->bindParam(':admin_rdate', $admin_rdate); // 등록 날짜

        // 쿼리 실행
        $stmt->execute();

        // 성공 메시지 및 리다이렉트
        echo "<script>
                alert('관리자가 성공적으로 등록되었습니다.');
                window.location.href = 'partner_list.php';
              </script>";
    } catch (PDOException $e) {
        echo "데이터 저장 중 오류가 발생했습니다: " . $e->getMessage();
    }
} else {
    echo "잘못된 요청입니다.";
}
?>
