<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/db_connection.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/gu/inc/fn_api_RTU.php');
 

// 현재 접속자의 partner_id 가져오기
if (!isset($_SESSION['partner_id'])) {
    die("Error: Partner ID not found in session.");
}

$partner_id = $_SESSION['partner_id'];
$config = get_RTU_Config($partner_id);

if (!$config) {
    die("Error: No configuration found for partner ID: $partner_id");
}

// 변수 설정
$appEUI = $config['app_eui'];
$uKey = $config['u_key'];
$subscription_key = $config['subscription_key'];
$rtu_companyname = $config['partner_name'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoRa 정보 입력</title>
</head>
<body>
    <h1>LoRa 정보 입력</h1>
    <form action="lora_input.php" method="post">
        <!-- RTU 업체 -->
        <input type="hidden" id="rtu_companyname" name="rtu_companyname" value="<?= htmlspecialchars($rtu_companyname) ?>"><br><br>
        <input type="hidden" id="rtu_code" name="rtu_code" value="<?= htmlspecialchars($subscription_key) ?>"><br><br>

        <!-- LoRa ID -->
        <label for="lora_id">LoRa ID:</label>
        <input type="text" id="lora_id" name="lora_id" required><br><br>

        <!-- appEUI -->
        <label for="app_eui">appEUI:</label>
        <input type="text" id="app_eui" name="app_eui" value="<?= htmlspecialchars($appEUI) ?>" required><br><br>

        <!-- uKey -->
        <label for="ukey">uKey:</label>
        <input type="text" id="ukey" name="ukey" value="<?= htmlspecialchars($uKey) ?>" required><br><br>

        <!-- 저장 버튼 -->
        <button type="submit">저장</button>
    </form>
</body>
</html>
