<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php'); // 데이터베이스 연결

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_id = $_POST['partner_id'];
    $admin_id = $_POST['admin_id'];
    $admin_pw = $_POST['admin_pw'];

    try {
        // 관리자 정보 확인 쿼리 (파트너 ID 포함)
        $stmt = $conn->prepare("SELECT * FROM RTU_admin WHERE partner_id = :partner_id AND admin_id = :admin_id AND admin_use = 'Y' AND delYN = 'N'");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($admin_pw, $admin['admin_pw'])) {
            // 로그인 성공
            $_SESSION['admin_idx'] = $admin['admin_idx'];
            $_SESSION['partner_id'] = $admin['partner_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_role'] = $admin['admin_role'];
            //header("Location: admin_dashboard.php"); // 대시보드로 리디렉션
            header("Location: index.php"); // 대시보드로 리디렉션
            exit();
        } else {
            // 로그인 실패
            header("Location: login.php?error=파트너 ID, 관리자 ID 또는 비밀번호가 잘못되었습니다.");
            exit();
        }
    } catch (PDOException $e) {
        echo "로그인 처리 중 오류가 발생했습니다: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit();
}
