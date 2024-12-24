<?php require_once('./inc/setting_info.php'); // 세션start,  // $root_dir 지정  // $db_conn 경로를 변수로 만듦. ?>
<?php  
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

$get_spartner_id = isset($_GET['spartner_id']) ? $_GET['spartner_id'] : null;

if (!$get_spartner_id) {
    echo "Error: spartner_id가 제공되지 않았습니다.";
    exit();
}


// 코드의 앞 3자리(대분류+소분류)를 반환하는 함수
function get_front3code($conn, $get_spartner_id) {
    try {
        $sql = "
            SELECT 
                sub.code_id AS code_id, 
                CONCAT(main.company_code, '', sub.company_code) AS front3code
            FROM 
                RTU_partner AS main
            JOIN 
                RTU_partner AS sub 
            ON 
                LEFT(sub.code_id, 4) = main.code_id 
            WHERE 
                main.code_type = 'M' AND sub.code_type = 'S' AND sub.code_id = :id
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $get_spartner_id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['front3code'] : null;

    } catch (PDOException $e) {
        echo "데이터를 가져오는 중 오류가 발생했습니다: " . $e->getMessage();
        return null;
    }
}

// UID 생성 함수
function generateUID($conn, $front3code) {
    // 접두어 설정
    $prefix = $front3code;

    // 현재 날짜 (YYMMDD)
    $now = new DateTime();
    $datePart = $now->format('y'); // 연도 마지막 2자리

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

    return $prefix . $datePart . $newNumber;
}

// 함수 호출
$front3code = get_front3code($conn, $get_spartner_id);

if (!$front3code) {
    echo "Error: Front 3 Code를 가져올 수 없습니다.";
    exit();
}

// UID 생성 호출
$generatedUID = generateUID($conn, $front3code);

//echo "Generated UID: " . $generatedUID;
//exit();

?>

<?php require_once($root_dir.'inc/from_html_to_head.php'); ?>
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
<?php require_once($root_dir.'inc/header_and_top_menu.php'); ?>
		 <!-- 본문 내용 시작 -->
		<main>
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

        <input type="hidden" name="spartner_id" value="<?echo $get_spartner_id;?>">
		
        <button type="submit" <?php echo $hasSpartner ? '' : 'disabled'; ?>>등록</button>
    </form>
 
		</main>
		 <!-- 본문 내용 끝. -->
<?php require_once($root_dir.'inc/footer.php'); ?>	