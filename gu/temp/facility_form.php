<?php  session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// Check if it's an edit request
$cid = $_GET['cid'];
$facilityData = [];
$mode = "input";


if ($cid) {
    try {
        $sql = "SELECT * FROM RTU_facility WHERE cid = :cid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        $facilityData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching facility data: " . $e->getMessage();
    }
	
	$mode = "edit";
}


// Generate new CID and calculate next Multi if mode is 'copy'
if ($mode === 'copy') {
    $cid = generateCID();

    // Calculate next Multi
    if (!empty($facilityData['lora_id']) && !empty($facilityData['user_id'])) {
        try {
            $sql = "SELECT MAX(multi) + 1 AS next_multi FROM RTU_facility 
                    WHERE lora_id = :lora_id AND user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':lora_id', $facilityData['lora_id']);
            $stmt->bindParam(':user_id', $facilityData['user_id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $facilityData['multi'] = $result['next_multi'] ?? 1;
        } catch (PDOException $e) {
            echo "Error calculating next Multi: " . $e->getMessage();
        }
    }
}

// If not editing, generate a new CID
if (!$cid) {
    $cid = generateCID();
}

// Function to generate a CID
function generateCID() {
    $now = microtime(true);
    $datetime = new DateTime();
    $year = $datetime->format('y');
    $month = $datetime->format('m');
    $day = $datetime->format('d');
    $hours = $datetime->format('H');
    $minutes = $datetime->format('i');
    $seconds = $datetime->format('s');
    $milliseconds = substr(explode('.', $now)[1], 0, 1);
    return $year . $month . $day . $hours . $minutes . $seconds . $milliseconds;
}

 
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>설비 <?= $mode === 'edit' ? '수정' : ($mode === 'copy' ? '복사' : '등록') ?></title>
    <script>
        function openInstallerPopup() {
            window.open('installer_popup.php', '이용자 목록', 'width=600,height=400');
        }

        function setInstaller(user_id, user_name, user_addr) {
            document.getElementById('user_id').value = user_id;
            document.getElementById('user_name').value = user_name;
            document.getElementById('user_addr').value = user_addr;
        }
        function copyFacility() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('mode', 'copy');
            urlParams.set('cid', '<?=$cid ?>');
            urlParams.set('copycid', '<?= generateCID() ?>'); // Generate a new CID for the copy
            window.location.href = '?' + urlParams.toString();
			alert("CID 자동생성되며, Multi만 변경하여, 손쉽게 추가가능 합니다.\n Multi 변경시, 없는 번호로 선택바랍니다."); 
        }		
    </script>
    <style>
		.form-container {
			display: flex;
			flex-wrap: wrap;
			gap: 10px; /* 항목 간격 */
		}

		.form-group {
			width: calc(50% - 10px); /* 각 항목의 너비를 절반으로 설정 (간격 포함) */
		}

		label {
			display: inline-block; /* 인라인 요소를 블록 요소처럼 처리 */
			width: 150px; /* 고정된 너비 */
			text-align: right; /* 텍스트 오른쪽 정렬 */
			margin-right: 10px; /* 입력 필드와 간격 */
			font-weight: bold; /* 텍스트 강조 */
		}

		input, select, textarea {
			width: 100%;
			box-sizing: border-box; /* 패딩 포함 너비 계산 */
		}

		.form-group.full-width {
			width: 100%; /* 특이사항 등의 넓은 항목은 전체 너비로 표시 */
		}

		button {
			margin-top: 20px;
			padding: 10px 20px;
			font-size: 16px;
		}

        input, select, textarea {
            width: 200px;
        }

        .required_item {
            color: #ff0000;
        }

        th, td, label, input {
            font-size: 11px;
        }
		
		
    </style>
</head>
<body>
<div style="display: flex;">

	<div style="flex: 1; width: 650px; height: 90vh; border: 0px solid #ccc;">
		<?php require_once($_SERVER['DOCUMENT_ROOT'].'/gu/temp/facility_list.php'); ?>
	</div>

    <div style="flex: 1;">
		<? if ($_GET['copycid']) {?>
        <center><h1>설비 복사</h1></center>
		<form action="facility_input.php" method="post">
		<?}else{?>		
        <center><h1>설비 <?= $mode === 'edit' ? '수정' : ($mode === 'copy' ? '복사' : '등록') ?></h1></center>
		<form action="<?= $mode === 'copy' ? 'facility_input.php' : ($mode === 'edit' ? 'facility_edit.php' : 'facility_input.php') ?>" method="post">
		<?}?>
		     <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
            <input type="hidden" name="is_edit" value="<?= $facilityData ? 1 : 0 ?>">
			<div class="form-container">
				<div class="form-group">
					<label for="cid">CID:</label>
					<? if ($_GET['copycid']) {?>
					<input type="text" id="cid" name="cid" value="<?= htmlspecialchars($_GET['copycid']) ?>" readonly>
					<input type="hidden" id="copycid" name="copycid" value="<?= htmlspecialchars($_GET['copycid']) ?>" readonly>						
					<?}else{?>
					<input type="text" id="cid" name="cid" value="<?= htmlspecialchars($cid) ?>" readonly>					
					<?}?>
				</div>

				<div class="form-group">
					<label for="business_year">사업 연도:</label>
					<select id="business_year" name="business_year" required>
						<?php
						$currentYear = date('Y');
						for ($year = $currentYear + 1; $year >= $currentYear - 20; $year--) {
							$selected = ($facilityData['business_year'] ?? '') == $year ? 'selected' : '';
							echo "<option value=\"$year\" $selected>$year</option>";
						}
						?>
					</select>
				</div>

				<div class="form-group">
					<label for="business_type">사업 구분:</label>
					<select id="business_type" name="business_type" required>
						<option value="건물지원사업" <?= ($facilityData['business_type'] ?? '') == '건물지원사업' ? 'selected' : '' ?>>건물지원사업</option>
						<option value="설치의무화" <?= ($facilityData['business_type'] ?? '') == '설치의무화' ? 'selected' : '' ?>>설치의무화</option>
						<option value="공공기관 태양광" <?= ($facilityData['business_type'] ?? '') == '공공기관 태양광' ? 'selected' : '' ?>>공공기관 태양광</option>
					</select>
				</div>

				<input type="hidden" id="user_id" name="user_id" value="<?= htmlspecialchars($facilityData['user_id'] ?? '') ?>">
				<input type="hidden" id="user_addr" name="user_addr" value="<?= htmlspecialchars($facilityData['user_addr'] ?? '') ?>">

				<div class="form-group">
					<label for="user_name" class="required_item">이용자:</label>
					<input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($facilityData['user_name'] ?? '') ?>" required onclick="openInstallerPopup()">
				</div>

				<div class="form-group">
					<label for="install_confirm_date">설치 확인일:</label>
					<input type="date" id="install_confirm_date" name="install_confirm_date" value="<?= htmlspecialchars($facilityData['install_confirm_date'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="install_confirm_num">설치확인관리번호:</label>
					<input type="text" id="install_confirm_num" name="install_confirm_num" value="<?= htmlspecialchars($facilityData['install_confirm_num'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="Last_access_date">최종접속일시:</label>
					<input type="text" id="Last_access_date" name="Last_access_date" value="<?= htmlspecialchars($facilityData['Last_access_date'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="Last_reception_date">최종수신일시:</label>
					<input type="text" id="Last_reception_date" name="Last_reception_date" value="<?= htmlspecialchars($facilityData['Last_reception_date'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="rtu_company">RTU 관리업체:</label>
					<select id="rtu_company" name="rtu_company" required>
						<option value="본사" <?= ($facilityData['rtu_company'] ?? '') == '본사' ? 'selected' : '' ?>>본사</option>
					</select>
				</div>

				<div class="form-group">
					<label for="rtu_company_tel">RTU 업체연락처:</label>
					<input type="text" id="rtu_company_tel" name="rtu_company_tel" value="<?= htmlspecialchars($facilityData['rtu_company_tel'] ?? '') ?>">
				</div>

				<div class="form-group">			
					<label for="communication_type" class="required_item">통신 방식:</label>
					<? if ($mode=="input") { ?>						
					<select id="communication_type" name="communication_type" required>
						<option value="LoRa(SKT)" <?= ($facilityData['communication_type'] ?? '') == 'LoRa(SKT)' ? 'selected' : '' ?>>LoRa(SKT)</option>
						<option value="LTE" <?= ($facilityData['communication_type'] ?? '') == 'LTE' ? 'selected' : '' ?>>LTE</option>
					</select>
					<?}else{?>
					<input type="text" id="communication_type" name="communication_type" value="<?= htmlspecialchars($facilityData['communication_type'] ?? '') ?>" required <? if ($mode=="edit") { echo "readonly"; }?>>
					<?}?>					
					
				</div>

				<div class="form-group">
					<label for="lora_id" class="required_item">LoRa ID:</label>
					<input type="text" id="lora_id" name="lora_id" value="<?= htmlspecialchars($facilityData['lora_id'] ?? '') ?>" required >
				</div>

				<div class="form-group">
					<label for="multi" class="required_item">Multi:</label>
 				
					<? if (($mode=="input")|| ($_GET['copycid'])) { ?>
					<select id="multi" name="multi" required>
						<option value="0" <?= ($facilityData['multi'] ?? '') == '0' ? 'selected' : '' ?>>0</option>
						<option value="1" <?= ($facilityData['multi'] ?? '') == '1' ? 'selected' : '' ?>>1</option>
						<option value="2" <?= ($facilityData['multi'] ?? '') == '2' ? 'selected' : '' ?>>2</option>
					</select>
					<?}else{?>
					<input type="text" id="multi" name="multi" value="<?= htmlspecialchars($facilityData['multi'] ?? '') ?>" required <? if ($mode=="edit") { echo "readonly"; }?>>
					<?}?>
				</div>

				<div class="form-group">
					<label for="address">주소:</label>
					<input type="text" id="address" name="address" value="<?= htmlspecialchars($facilityData['address'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="latitude">위도:</label>
					<input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($facilityData['latitude'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="longitude">경도:</label>
					<input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($facilityData['longitude'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="install_type">설치 유형:</label>
					<select id="install_type" name="install_type" required>
						<option value="지상형" <?= ($facilityData['install_type'] ?? '') == '지상형' ? 'selected' : '' ?> >지상형</option>
						<option value="경사지붕형" <?= ($facilityData['install_type'] ?? '') == '경사지붕형' ? 'selected' : '' ?> >경사지붕형</option>
						<option value="평슬라브형" <?= ($facilityData['install_type'] ?? '') == '평슬라브형' ? 'selected' : '' ?> >평슬라브형</option>
						<option value="곡면형" <?= ($facilityData['install_type'] ?? '') == '곡면형' ? 'selected' : '' ?> >곡면형</option>
						<option value="건물일체형(BIPV)" <?= ($facilityData['install_type'] ?? '') == '건물일체형(BIPV)' ? 'selected' : '' ?> >건물일체형(BIPV)</option>
					</select>
				</div>

				<div class="form-group">
					<label for="module_class">모듈결정분류:</label>
					<select id="module_class" name="module_class" required>
						<option value="단결정"  <?= ($facilityData['module_class'] ?? '') == '단결정' ? 'selected' : '' ?>  >단결정</option>
						<option value="다결정"  <?= ($facilityData['module_class'] ?? '') == '다결정' ? 'selected' : '' ?>  >다결정</option>
					</select>
				</div>

				<div class="form-group">
					<label for="module_capacity">모듈당 용량(W):</label>
					<input type="text" id="module_capacity" name="module_capacity" value="<?= htmlspecialchars($facilityData['module_capacity'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="total_capacity">모듈 총 용량(W):</label>
					<input type="text" id="total_capacity" name="total_capacity" value="<?= htmlspecialchars($facilityData['total_capacity'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="module_manufacturer">모듈 제조사:</label>
					<select id="module_manufacturer" name="module_manufacturer" required>
						<option value="생략" <?= ($facilityData['module_manufacturer'] ?? '') == '생략' ? 'selected' : '' ?> >생략</option>
					</select>
				</div>

				<div class="form-group">
					<label for="module_model">모듈 모델명:</label>
					<input type="text" id="module_model" name="module_model" value="<?= htmlspecialchars($facilityData['module_model'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="module_azimuth" class="required_item">모듈 방위각:</label>
					<input type="text" id="module_azimuth" name="module_azimuth" required value="<?= htmlspecialchars($facilityData['module_azimuth'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="module_angle">모듈 경사각:</label>
					<input type="text" id="module_angle" name="module_angle" value="<?= htmlspecialchars($facilityData['module_angle'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="module_series" class="required_item">모듈 직렬개수:</label>
					<input type="number" id="module_series" name="module_series" required value="<?= htmlspecialchars($facilityData['module_series'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="module_parallel" class="required_item">모듈 병렬개수:</label>
					<input type="number" id="module_parallel" name="module_parallel" required value="<?= htmlspecialchars($facilityData['module_parallel'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="inverter_model">인버터 모델명:</label>
					<input type="text" id="inverter_model" name="inverter_model" value="<?= htmlspecialchars($facilityData['inverter_model'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="inverter_capacity" class="required_item">인버터 용량(kW):</label>
					<input type="text" id="inverter_capacity" name="inverter_capacity" required value="<?= htmlspecialchars($facilityData['inverter_capacity'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="phase_type" class="required_item">위상종류:</label>
					<select id="phase_type" name="phase_type" required>
						<option value="단상" <?= ($facilityData['phase_type'] ?? '') == '단상' ? 'selected' : '' ?> >단상</option>
						<option value="삼상" <?= ($facilityData['phase_type'] ?? '') == '삼상' ? 'selected' : '' ?> >삼상</option>
					</select>
				</div>

				<div class="form-group">
					<label for="tracker_system" class="required_item">추적 시스템 유형:</label>
					<select id="tracker_system" name="tracker_system" required>
						<option value="고정형" <?= ($facilityData['tracker_system'] ?? '') == '고정형' ? 'selected' : '' ?> >고정형</option>
						<option value="추적형" <?= ($facilityData['tracker_system'] ?? '') == '추적형' ? 'selected' : '' ?> >추적형</option>
					</select>
				</div>

				<div class="form-group">
					<label for="company_name">시공 업체:</label>
					<input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($facilityData['company_name'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="company_contact">시공 업체 연락처:</label>
					<input type="text" id="company_contact" name="company_contact" value="<?= htmlspecialchars($facilityData['company_contact'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="construction_date" class="required_item">시공일시 시작일시:</label>
					<input type="datetime-local" id="construction_date" name="construction_date" required value="<?= htmlspecialchars($facilityData['construction_date'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="monitor_date" class="required_item">모니터링 시작일시:</label>
					<input type="datetime-local" id="monitor_date" name="monitor_date"  value="<?= htmlspecialchars($facilityData['monitor_date'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="as_request_enddate" class="required_item">AS 만료일:</label>
					<input type="date" id="as_request_enddate" name="as_request_enddate" value="<?= htmlspecialchars($facilityData['as_request_enddate'] ?? '') ?>">
				</div>

				<div class="form-group">
					<label for="grid_connection" class="required_item">계통접속:</label>
					<select id="grid_connection" name="grid_connection" required>
						<option value="저압연계" <?= ($facilityData['grid_connection'] ?? '') == '저압연계' ? 'selected' : '' ?> >저압연계</option>
						<option value="고압연계" <?= ($facilityData['grid_connection'] ?? '') == '고압연계' ? 'selected' : '' ?> >고압연계</option>
					</select>
				</div>

				<div class="form-group">
					<label for="building_use" class="required_item">건물용도:</label>
					<select id="building_use" name="building_use" required>
						<option value="아파트(공동주택)" <?= ($facilityData['building_use'] ?? '') == '아파트(공동주택)' ? 'selected' : '' ?> >아파트(공동주택)</option>
						<option value="주택(단독)" <?= ($facilityData['building_use'] ?? '') == '주택(단독)' ? 'selected' : '' ?> >주택(단독)</option>
						<option value="빌라(다세대,다가구,연립)" <?= ($facilityData['building_use'] ?? '') == '빌라(다세대,다가구,연립)' ? 'selected' : '' ?> >빌라(다세대,다가구,연립)</option>
						<option value="일반건물" <?= ($facilityData['building_use'] ?? '') == '일반건물' ? 'selected' : '' ?> >일반건물</option>
						<option value="공공건물" <?= ($facilityData['building_use'] ?? '') == '공공건물' ? 'selected' : '' ?> >공공건물</option>
						<option value="공장" <?= ($facilityData['building_use'] ?? '') == '공장' ? 'selected' : '' ?> >공장</option>
						<option value="복지시설" <?= ($facilityData['building_use'] ?? '') == '복지시설' ? 'selected' : '' ?> >복지시설</option>
						<option value="미분류" <?= ($facilityData['building_use'] ?? '') == '미분류' ? 'selected' : '' ?> >미분류</option>
					</select>
				</div>

					<div class="form-group">
						<label for="special_note">특이사항:</label>
						<textarea id="special_note" name="special_note"><?= htmlspecialchars($facilityData['special_note'] ?? '') ?></textarea>
					</div>
			</div>
        <center>					
                <?php if ($mode === 'edit'): ?>
					<? if($_GET['copycid']) { ?>
                    <button type="submit"  style="cursor: pointer;">복사완료</button>					
					<?}else{?>
                     <button type="submit"  style="cursor: pointer;">수정</button>
                   <button type="button" onclick="copyFacility()"  style="cursor: pointer;">복사추가</button>
				    <?}?>
                <?php elseif ($mode === 'copy'): ?>
                    <button type="submit"  style="cursor: pointer;">복사완료</button>
                <?php else: ?>
                    <button type="submit"  style="cursor: pointer;">등록</button>
                <?php endif; ?>		
 
		</center>

    </form>
	
	
    </div>

</div>	
	
</body>
</html>
