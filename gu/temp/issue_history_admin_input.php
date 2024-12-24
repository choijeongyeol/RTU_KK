<?php
// DB 연결 설정
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');

try {
    // 장애 유형 목록을 가져오는 쿼리
    $issue_type_sql = "SELECT issue_type_id, issue_name FROM RTU_issue_type";
    $stmt_issue_type = $conn->prepare($issue_type_sql);
    $stmt_issue_type->execute();
    $issue_types = $stmt_issue_type->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>장애 이력 입력 - 관리자 모드</title>
    <style>
        label, select, input[type="text"], input[type="datetime-local"] {
            display: block;
            margin: 8px 0;
            width: 80%;
            padding: 8px;
        }
    </style>
    <script>
        function openUserSearchPopup() {
            window.open("user_search_popup.php", "userSearch", "width=600,height=400,scrollbars=yes");
        }

        function selectUser(user_idx, user_id, user_name) {
            // 사용자명과 ID를 메인 화면에 표시하고, 숨겨진 필드에 user_idx와 user_id를 저장
            document.getElementById("user_idx").value = user_idx;
            document.getElementById("user_id").value = user_id;
            document.getElementById("user_name_display").value = user_name;

            // 로라 장비 목록 업데이트
            fetchLoraOptions(user_id);
        }

		function fetchLoraOptions(user_id) {
			const xhr = new XMLHttpRequest();
			xhr.open("GET", "get_lora_options.php?user_id=" + user_id, true);
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4 && xhr.status === 200) {
					const response = JSON.parse(xhr.responseText);
					const loraSelect = document.getElementById("lora_idx");
					loraSelect.innerHTML = '';

					if (response.loras.length === 1) {
						// 로라 장비가 1개일 경우 바로 선택
						const lora = response.loras[0];
						loraSelect.innerHTML = `<option value="${lora.lora_idx}">${lora.short_powerstation} (${lora.lora_id})</option>`;
						loraSelect.disabled = false;
						fetchInverterOptions(lora.lora_idx); // lora_idx 전달
					} else if (response.loras.length > 1) {
						// 로라 장비가 여러 개일 경우 "선택하세요" 옵션 추가
						loraSelect.innerHTML = '<option value="">선택하세요</option>';
						response.loras.forEach(lora => {
							loraSelect.innerHTML += `<option value="${lora.lora_idx}">${lora.short_powerstation} (${lora.lora_id})</option>`;
						});
						loraSelect.disabled = false;
					}
				}
			};
			xhr.send();
		}


        function fetchInverterOptions(lora_idx) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_inverter_options.php?lora_idx=" + lora_idx, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const inverterSelect = document.getElementById("facility_id");
                    inverterSelect.innerHTML = '';

                    if (response.inverters.length === 1) {
                        // 인버터가 1개일 경우 바로 선택
                        const inverter = response.inverters[0];
                        inverterSelect.innerHTML = `<option value="${inverter.cid}">${inverter.cid}</option>`;
                        inverterSelect.disabled = false;
                    } else if (response.inverters.length > 1) {
                        // 인버터가 여러 개일 경우 "선택하세요" 옵션 추가
                        inverterSelect.innerHTML = '<option value="">선택하세요</option>';
                        response.inverters.forEach(inverter => {
                            inverterSelect.innerHTML += `<option value="${inverter.cid}">${inverter.cid}</option>`;
                        });
                        inverterSelect.disabled = false;
                    }
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>

<h2>장애 이력 입력 - 관리자 모드</h2>

<form action="issue_history_store.php" method="post">
    <label for="user_name_display">사용자명:</label>
    <input type="text" id="user_name_display" name="user_name_display" readonly required>
    <button type="button" onclick="openUserSearchPopup()">사용자 검색</button>
    <input type="hidden" id="user_idx" name="user_idx" required> <!-- 실제 사용자 ID를 저장할 숨겨진 필드 -->
    <input type="hidden" id="user_id" name="user_id" required> <!-- 실제 user_id를 저장할 숨겨진 필드 -->

    <label for="issue_name">장애명:</label>
    <select id="issue_name" name="issue_name" required>
        <?php foreach ($issue_types as $type): ?>
            <option value="<?= $type['issue_type_id'] ?>"><?= htmlspecialchars($type['issue_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="issue_date">장애 발생일시:</label>
    <input type="datetime-local" id="issue_date" name="issue_date" required>

    <label for="lora_idx">로라 장비 (발전소명):</label>
    <select id="lora_idx" name="lora_idx" required onchange="fetchInverterOptions(this.value)" disabled>
        <!-- 로라 장비 옵션은 AJAX로 로드됩니다 -->
    </select>

    <label for="facility_id">인버터 정보 (CID):</label>
    <select id="facility_id" name="facility_id" required disabled>
        <!-- 인버터 정보는 AJAX로 로드됩니다 -->
    </select>

    <button type="submit">장애 이력 추가</button>
</form>

</body>
</html>
