<?php
// 데이터베이스 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');


// UID 생성 함수
function generateUID($conn) {
    // 접두어 설정
    $prefix = "BN";

    // 현재 날짜 (YYMMDD)
    $now = new DateTime();
    $datePart = $now->format('ymd');

    // 현재 날짜로 등록된 UID가 있는지 확인 (년월일이 같은 UID를 검색)
    $sql = "SELECT user_id FROM RTU_user WHERE user_id LIKE :uid_date ORDER BY user_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':uid_date', $prefix . $datePart . '%');
    $stmt->execute();
    $lastUID = $stmt->fetchColumn();

    // 마지막 4자리 숫자를 추출하고, 없으면 001로 시작
    if ($lastUID) {
        // 마지막 UID의 마지막 3자리 숫자를 추출
        $lastNumber = intval(substr($lastUID, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        // 해당 날짜로 등록된 UID가 없으면 001로 시작
        $newNumber = '0001';
    }

    // 최종 UID 생성 (BN + YYMMDD + 4자리 숫자)
    return $prefix . $datePart ."-". $newNumber;
}

// UID 생성 호출
$generatedUID = generateUID($conn);

// 결과 출력 또는 사용할 수 있도록 반환
//echo $generatedUID;
 
?>
 
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 입력 화면</title>
</head>
<body>


<!-- <input type="text" id="sample6_postcode" placeholder="우편번호"> -->
<!-- <input type="button" onclick="sample6_execDaumPostcode()" value="기본주소입력"><br>
<input type="text" id="sample6_address" placeholder="주소"><br> -->
<!-- <input type="text" id="sample6_detailAddress" placeholder="상세주소">
<input type="text" id="sample6_extraAddress" placeholder="참고항목"> -->

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    function sample6_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수
                //var extraAddr = ''; // 참고항목 변수

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    //if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    //    extraAddr += data.bname;
                    //}
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    //if(data.buildingName !== '' && data.apartment === 'Y'){
                    //    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    //}
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    //if(extraAddr !== ''){
                    //    extraAddr = ' (' + extraAddr + ')';
                   // }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    //document.getElementById("sample6_extraAddress").value = extraAddr;
                
                } else {
                   // document.getElementById("sample6_extraAddress").value = '';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                //document.getElementById('sample6_postcode').value = data.zonecode;
                document.getElementById("sample6_address").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("user_email").focus();
            }
        }).open();
    }
</script>




 
 

    <h1>사용자 등록</h1>
    <form action="user_input.php" method="post">
        <label for="user_id">아이디 (ID):</label>
        <input type="text" name="user_id" readonly value="<?php echo $generatedUID; ?>" id="UID"><br><br>

        <label for="user_name">이용자 (이름):</label>
        <input type="text" name="user_name" required><br><br>

        <label for="user_pw">패스워드:</label>
        <input type="password" name="user_pw" required><br><br>

        <label for="user_phone">연락처:</label>
        <input type="text" name="user_phone" required><br><br>

        <label for="user_addr">주소:</label>
        <input type="text" name="user_addr" required id="sample6_address"   onclick="sample6_execDaumPostcode()" ><br><br>

        <label for="user_add">이메일:</label>
        <input type="email" name="user_email" required id="user_email"><br><br>

        <label for="sms_receive">SMS 수신 여부:</label>
        <select name="sms_receive">
            <option value="1">수신</option>
            <option value="0">미수신</option>
        </select><br><br>

        <label for="email_receive">EMAIL 수신 여부:</label>
        <select name="email_receive">
            <option value="1">수신</option>
            <option value="0">미수신</option>
        </select><br><br>

        <button type="submit">등록</button>
    </form>
</body>
</html>
