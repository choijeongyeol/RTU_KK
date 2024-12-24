<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php
// 로그인 여부 확인
if (!isset($_SESSION['admin_idx'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>안녕하세요, " . htmlspecialchars($_SESSION['admin_name']) . " 관리자님!</h1>";
echo "<p>파트너 ID: " . htmlspecialchars($_SESSION['partner_id']) . "</p>";
echo "<p>관리자 역할: " . htmlspecialchars($_SESSION['admin_role']) . "</p>";
echo '<a href="logout.php">로그아웃</a>';
?>