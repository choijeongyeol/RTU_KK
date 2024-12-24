<?php
	// CID 자동 생성 예시 (년월일 + 시분 + 초 + 마지막 1자리 밀리초)
	function generateCID() {
		// 현재 시간을 가져오기 (년월일 + 시분 + 초 + 밀리초)
		$now = microtime(true); // 현재 시간을 초 단위로 가져오기 (float형)
		$datetime = new DateTime();
		$year = $datetime->format('y'); // 연도 마지막 두 자리 (예: 2024 -> 24)
		$month = $datetime->format('m'); // 월
		$day = $datetime->format('d'); // 일
		$hours = $datetime->format('H'); // 시
		$minutes = $datetime->format('i'); // 분
		$seconds = $datetime->format('s'); // 초
		$milliseconds = substr(explode('.', $now)[1], 0, 1); // 밀리초 첫 번째 자리

		// CID 생성 (년월일 + 시분 + 초 + 밀리초 1자리)
		$cid = $year . $month . $day . $hours . $minutes . $seconds . $milliseconds;

		return $cid;
	}

	// CID 생성 함수 호출
	$cid = generateCID();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 등록</title>
	
    <script>
        // 팝업 창 열기
        function openInstallerPopup() {
            window.open('installer_popup.php', '이용자 목록', 'width=600,height=400');
        }

        // 팝업 창에서 선택한 설치자를 메인 화면에 설정
        function setInstaller(user_id,user_name,user_addr) {
            document.getElementById('user_id').value = user_id;
            document.getElementById('user_name').value = user_name;
            document.getElementById('user_addr').value = user_addr;
        }
    </script>
	
    <style>
        .form-group {
            margin-bottom: 10px;
        }

        label {
            display: inline-block;
            width: 150px; /* 라벨의 고정된 너비를 설정 */
            text-align: right;
            margin-right: 10px;
        }

        input, select, textarea {
            width: 200px;
        }

        .required_item {
            color: #ff0000;
        }
    </style>
</head>
<body>
<div style="display: flex;">

    <div style="flex: 1;">	
        <!-- 등록된 설비 목록을 표시 -->
        <? require_once($_SERVER['DOCUMENT_ROOT'].'/gu/temp/facility_list.php');  ?>
    </div>

    <div style="flex: 1;">
    <h1>설비 등록</h1>
    <form action="facility_input.php" method="post">
        <div class="form-group">
            <label for="cid">CID:</label>
            <input type="text" id="cid" name="cid" value="<?php echo $cid; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="business_year">사업 연도:</label>
            <select id="business_year" name="business_year" required>
                <?php
                    // 현재 연도 계산
                    $currentYear = date('Y');

                    // 최근 20년간의 연도를 select box에 추가
                    for ($year = $currentYear + 1; $year >= $currentYear - 20; $year--) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="business_type">사업 구분:</label>
            <select id="business_type" name="business_type" required>
                <option value="건물지원사업">건물지원사업</option>
                <option value="설치의무화">설치의무화</option>
                <option value="공공기관 태양광">공공기관 태양광</option>
            </select>
        </div>

        <input type="hidden" id="user_id" name="user_id">
        <input type="hidden" id="user_addr" name="user_addr">

        <div class="form-group">
            <label for="user_name" class="required_item">이용자:</label>
            <input type="text" id="user_name" name="user_name" required onclick="openInstallerPopup()">
        </div>

        <div class="form-group">
            <label for="install_confirm_date">설치 확인일:</label>
            <input type="date" id="install_confirm_date" name="install_confirm_date">
        </div>

        <div class="form-group">
            <label for="install_confirm_num">설치확인관리번호:</label>
            <input type="text" id="install_confirm_num" name="install_confirm_num">
        </div>

        <div class="form-group">
            <label for="Last_access_date">최종접속일시:</label>
            <input type="text" id="Last_access_date" name="Last_access_date">
        </div>

        <div class="form-group">
            <label for="Last_reception_date">최종수신일시:</label>
            <input type="text" id="Last_reception_date" name="Last_reception_date">
        </div>

        <div class="form-group">
            <label for="rtu_company">RTU 업체:</label>
            <select id="rtu_company" name="rtu_company" required>
                <option value="본사">본사</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rtu_company_tel">RTU 업체연락처:</label>
            <input type="text" id="rtu_company_tel" name="rtu_company_tel">
        </div>

        <div class="form-group">
            <label for="communication_type" class="required_item">통신 방식:</label>
            <select id="communication_type" name="communication_type" required>
                <option value="LoRa(SKT)">LoRa(SKT)</option>
                <option value="LTE">LTE</option>
            </select>
        </div>

        <div class="form-group">
            <label for="lora_id" class="required_item">LoRa ID:</label>
            <input type="text" id="lora_id" name="lora_id" required>
        </div>
		
        <div class="form-group">
            <label for="multi" class="required_item">Multi:</label>
            <select id="multi" name="multi" required>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
        </div>

        <div class="form-group">
            <label for="address">주소:</label>
            <input type="text" id="address" name="address">
        </div>

        <div class="form-group">
            <label for="latitude">위도:</label>
            <input type="text" id="latitude" name="latitude">
        </div>

        <div class="form-group">
            <label for="longitude">경도:</label>
            <input type="text" id="longitude" name="longitude">
        </div>

        <div class="form-group">
            <label for="install_type">설치 유형:</label>
            <select id="install_type" name="install_type" required>
                <option value="지상형">지상형</option>
                <option value="경사지붕형">경사지붕형</option>
                <option value="평슬라브형">평슬라브형</option>
                <option value="곡면형">곡면형</option>
                <option value="건물일체형(BIPV)">건물일체형(BIPV)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="module_class">모듈결정분류:</label>
            <select id="module_class" name="module_class" required>
                <option value="단결정">단결정</option>
                <option value="다결정">다결정</option>
            </select>
        </div>

        <div class="form-group">
            <label for="module_capacity">모듈당 용량(W):</label>
            <input type="text" id="module_capacity" name="module_capacity">
        </div>

        <div class="form-group">
            <label for="total_capacity">모듈 총 용량(W):</label>
            <input type="text" id="total_capacity" name="total_capacity">
        </div>

        <div class="form-group">
            <label for="module_manufacturer">모듈 제조사:</label>
            <select id="module_manufacturer" name="module_manufacturer" required>
                <option value="생략">생략</option>
            </select>
        </div>

        <div class="form-group">
            <label for="module_model">모듈 모델명:</label>
            <input type="text" id="module_model" name="module_model">
        </div>

        <div class="form-group">
            <label for="module_azimuth" class="required_item">모듈 방위각:</label>
            <input type="text" id="module_azimuth" name="module_azimuth" required>
        </div>

        <div class="form-group">
            <label for="module_angle">모듈 경사각:</label>
            <input type="text" id="module_angle" name="module_angle">
        </div>

        <div class="form-group">
            <label for="module_series" class="required_item">모듈 직렬개수:</label>
            <input type="number" id="module_series" name="module_series" required>
        </div>

        <div class="form-group">
            <label for="module_parallel" class="required_item">모듈 병렬개수:</label>
            <input type="number" id="module_parallel" name="module_parallel" required>
        </div>

        <div class="form-group">
            <label for="inverter_model">인버터 모델명:</label>
            <input type="text" id="inverter_model" name="inverter_model">
        </div>

        <div class="form-group">
            <label for="inverter_capacity" class="required_item">인버터 용량(kW):</label>
            <input type="text" id="inverter_capacity" name="inverter_capacity" required>
        </div>

        <div class="form-group">
            <label for="phase_type" class="required_item">위상종류:</label>
            <select id="phase_type" name="phase_type" required>
                <option value="단상">단상</option>
                <option value="삼상">삼상</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tracker_system" class="required_item">추적 시스템 유형:</label>
            <select id="tracker_system" name="tracker_system" required>
                <option value="고정형">고정형</option>
                <option value="추적형">추적형</option>
            </select>
        </div>

        <div class="form-group">
            <label for="company_name">시공 업체:</label>
            <input type="text" id="company_name" name="company_name">
        </div>

        <div class="form-group">
            <label for="company_contact">시공 업체 연락처:</label>
            <input type="text" id="company_contact" name="company_contact">
        </div>

        <div class="form-group">
            <label for="construction_date" class="required_item">시공일시 시작일시:</label>
            <input type="datetime-local" id="construction_date" name="construction_date" required>
        </div>

        <div class="form-group">
            <label for="monitor_date" class="required_item">모니터링 시작일시:</label>
            <input type="datetime-local" id="monitor_date" name="monitor_date">
        </div>

        <div class="form-group">
            <label for="as_request_enddate" class="required_item">AS 만료일:</label>
            <input type="date" id="as_request_enddate" name="as_request_enddate">
        </div>

        <div class="form-group">
            <label for="grid_connection" class="required_item">계통접속:</label>
            <select id="grid_connection" name="grid_connection" required>
                <option value="저압연계">저압연계</option>
                <option value="고압연계">고압연계</option>
            </select>
        </div>

        <div class="form-group">
            <label for="building_use" class="required_item">건물용도:</label>
            <select id="building_use" name="building_use" required>
                <option value="아파트(공동주택)">아파트(공동주택)</option>
                <option value="주택(단독)">주택(단독)</option>
                <option value="빌라(다세대,다가구,연립)">빌라(다세대,다가구,연립)</option>
                <option value="일반건물">일반건물</option>
                <option value="공공건물">공공건물</option>
                <option value="공장">공장</option>
                <option value="복지시설">복지시설</option>
                <option value="미분류">미분류</option>
            </select>
        </div>

        <div class="form-group">
            <label for="special_note">특이사항:</label>
            <textarea id="special_note" name="special_note"></textarea>
        </div>

        <button type="submit">등록</button>
    </form>
	
	
    </div>

</div>	
	
</body>
</html>
