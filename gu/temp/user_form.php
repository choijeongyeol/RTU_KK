<?php  session_start();
// 데이터베이스 연결
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// 지자체 데이터 가져오기
try {
    $sql = "SELECT spartner_id, spartner_name FROM RTU_spartner where partner_id = '".$_SESSION['partner_id']."'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $spartner_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
    // 지자체 데이터가 있는지 확인
    $hasSpartner = !empty($spartner_list);
} catch (PDOException $e) {
    echo "지자체 데이터를 불러오는 중 오류가 발생했습니다: " . $e->getMessage();
    exit();
}

// UID 생성 함수
function generateUID($conn) {
    // 접두어 설정
    $prefix = "BN";

    // 현재 날짜 (YYMMDD)
    $now = new DateTime();
    $datePart = $now->format('ymd');

    // 현재 날짜로 등록된 UID가 있는지 확인
    $sql = "SELECT user_id FROM RTU_user WHERE user_id LIKE ? ORDER BY user_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$prefix . $datePart . '%']);
    $lastUID = $stmt->fetchColumn();

    // 마지막 4자리 숫자를 추출하고, 없으면 0001로 시작
    if ($lastUID) {
        $lastNumber = intval(substr($lastUID, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '0001';
    }

    return $prefix . $datePart . "-" . $newNumber;
}

// UID 생성 호출
$generatedUID = generateUID($conn);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 등록</title>
<script>
function legalcode1() {
     const popup = window.open("legalcode_search.php", "legalcodeSearch", "width=800,height=600");
    if (!popup || popup.closed || typeof popup.closed === 'undefined') {
        alert("팝업 차단이 활성화되어 있습니다. 팝업 차단을 해제해주세요.");
    }
	
	fm.user_addr2.focus();
}
</script>
</head>
<body>
    <h1>사용자 등록</h1>
    <form action="user_input.php" method="post" name="fm">
        <label for="user_id">아이디 (ID):</label>
        <input type="text" name="user_id" readonly value="<?php echo $generatedUID; ?>" id="UID"><br><br>

        <label for="user_name">이용자 이름:</label>
        <input type="text" name="user_name" required><br><br>

        <label for="user_pw">패스워드:</label>
        <input type="password" name="user_pw" required><br><br>

        <label for="user_phone">연락처:</label>
        <input type="text" name="user_phone" required><br><br>

        <label for="user_addr">주소 :</label>
        <input type="hidden" name="legalcode" required id="legalcode"> 
        <input type="text" name="user_addr" required id="legaldong" readonly onclick="legalcode1()" placeholder="클릭하여 검색"><br><br>
        <label for="user_addr2">상세주소 :</label>
        <input type="text" name="user_addr2" required id="user_addr2"><br><br>

        <label for="user_email">이메일:</label>
        <input type="email" name="user_email" required><br><br>

        <label for="spartner_id">소속 지자체:</label>
        <select name="spartner_id" required>
            <option value="">-- 지자체를 선택하세요 --</option>
            <?php if ($hasSpartner): ?>
                <?php foreach ($spartner_list as $spartner): ?>
                    <option value="<?php echo $spartner['spartner_id']; ?>">
                        <?php echo htmlspecialchars($spartner['spartner_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">지자체등록 후 이용가능</option>
            <?php endif; ?>
        </select><br><br>

        <button type="submit" <?php echo $hasSpartner ? '' : 'disabled'; ?>>등록</button>
    </form>
</body>
</body>
</html>
