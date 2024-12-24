<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php'); // 데이터베이스 연결

session_start();

if (isset($_POST['partner_id'])) {
    $partner_id = $_POST['partner_id'];
    $partner_id_length = strlen($partner_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_id = $_POST['partner_id'];
    $admin_id = $_POST['admin_id'];
    $admin_pw = $_POST['admin_pw'];
	
	
	// 지자체 로그인이면, 파트너 코드  수정
	if ($partner_id_length==7) {
		$spartner_id = $_POST['partner_id'];  // spartner 값으로 구분저장
		$partner_id =  mb_substr($_POST['partner_id'],0,4);
	}else{
		$spartner_id = 0; // 지자체 아닌, 상위 파트너 접속시, spartner_id 값  명시 저장.
	}
	

    try {
        // 관리자 정보 확인 쿼리 (파트너 ID 포함)
        $stmt = $conn->prepare("
            SELECT a.*, c.subscription_key 
            FROM RTU_admin a
            JOIN RTU_Configuration c ON a.partner_id = c.partner_id
            WHERE a.partner_id = :partner_id and a.spartner_id = :spartner_id 
              AND a.admin_id = :admin_id 
              AND a.admin_use = 'Y' 
              AND a.delYN = 'N'
        ");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':spartner_id', $spartner_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($admin_pw, $admin['admin_pw'])) {
            // 로그인 성공 - 세션에 정보 저장
            $_SESSION['admin_idx'] = $admin['admin_idx'];
            $_SESSION['partner_id'] = $admin['partner_id'];
            $_SESSION['spartner_id'] = $admin['spartner_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_role'] = $admin['admin_role'];
            $_SESSION['subscription_key'] = $admin['subscription_key']; // subscription_key 추가

            // 대시보드로 리디렉션
            header("Location: index.html");
            exit();
        } else {
            // 로그인 실패
            header("Location: login.php?error=기관코드, 관리자 ID 또는 비밀번호가 잘못되었습니다.");
            exit();
        }
    } catch (PDOException $e) {
        echo "로그인 처리 중 오류가 발생했습니다: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit();
}
