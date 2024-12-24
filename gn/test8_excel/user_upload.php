<?php
// 에러 표시 설정
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// MySQL 연결 설정
include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/db_connection.php');

$conn = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 업로드 디렉토리 설정
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/gn/test8_excel/uploads/';

    // 파일명에 현재 날짜와 시간을 추가
    $originalFileName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $dateTimeSuffix = date('YmdHis');
    $newFileName = $originalFileName . '_' . $dateTimeSuffix . '.' . $fileExtension;
    $uploadFile = $uploadDir . $newFileName;

    // 디렉토리가 존재하는지 확인하고, 존재하지 않으면 생성
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
		//echo "<script>alert('파일이 업로드되었습니다.');</script>";

        try {
            // 데이터베이스 트랜잭션 시작
            $conn->beginTransaction();
			
			$spreadsheet = IOFactory::load($uploadFile);
			$worksheet = $spreadsheet->getActiveSheet();
			$rows = $worksheet->toArray();

			// 결과를 저장할 배열 초기화
			$results = [];

			// 첫 번째 행은 헤더로 간주하고 건너뜁니다.
			$header = true;
			foreach ($rows as $row) {
				if ($header) {
					$header = false;
					$row[] = "Insert Result"; // 헤더에 결과 열 추가
					$results[] = $row;
					continue;
				}

				// 엑셀로부터 user_id와 user_name 읽기
				$user_id = $row[0];
				$user_name = $row[1];

				// 자동 생성되는 값들
				$user_pw = password_hash('1234', PASSWORD_DEFAULT);
				$user_rdate = date("Y-m-d H:i:s");
				$user_token = strrev(substr(password_hash(date("Y-m-d H:i:s"), PASSWORD_DEFAULT), -32));
				$user_role = 1;  // 기본값
				$user_use = 'Y';  // 기본값
				$delYN = 'N';  // 기본값

				// 데이터베이스에 데이터 삽입
				$sql = "INSERT INTO wms_user (user_id, user_name, user_pw, user_role, user_rdate, user_token, user_use, delYN) VALUES (:user_id, :user_name, :user_pw, :user_role, :user_rdate, :user_token, :user_use, :delYN)";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':user_name', $user_name);
				$stmt->bindParam(':user_pw', $user_pw);
				$stmt->bindParam(':user_role', $user_role);
				$stmt->bindParam(':user_rdate', $user_rdate);
				$stmt->bindParam(':user_token', $user_token);
				$stmt->bindParam(':user_use', $user_use);
				$stmt->bindParam(':delYN', $delYN);

				if ($stmt->execute()) {
					$row[] = "Success";
				} else {
					$row[] = "Fail";
                    // 롤백
                    $conn->rollBack();
                    echo "<script>alert('데이터베이스 삽입 중 오류가 발생했습니다.');</script>";
                    exit; // 프로그램 종료					
				}

				$results[] = $row;
			}

			// 결과를 포함한 새로운 엑셀 파일 생성
			$resultSpreadsheet = new Spreadsheet();
			$resultSheet = $resultSpreadsheet->getActiveSheet();
			$resultSheet->fromArray($results, NULL, 'A1');
			$resultFileName = $originalFileName . '_result_' . $dateTimeSuffix . '.' . $fileExtension;
			$resultFilePath = $uploadDir . $resultFileName;
			$writer = new Xlsx($resultSpreadsheet);
			$writer->save($resultFilePath);
			
            // 데이터베이스 작업 커밋
            $conn->commit();			

			// 업로드 결과 팝업 띄우기
			echo "<script> var resultFileName = '" . urlencode($resultFileName) . "'; var popupUrl = 'result_popup.php?file=' + resultFileName; window.open(popupUrl, 'ResultPopup', 'width=600,height=400'); </script>";
        } catch (Exception $e) {
            // 롤백
            $conn->rollBack();
            echo "<script>alert('엑셀처리 중, 오류가 발생하여. 등록하지 않았습니다. 오류사항은 로그를 참조하세요');</script>";
            // 예외 처리 로깅
            $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . 'Exception caught: ' . $e->getMessage() . PHP_EOL;
            error_log($logMessage, 3, $_SERVER['DOCUMENT_ROOT'] . '/gn/logs/error.log');
            // 에러 로그를 팝업으로 보여주기
            echo "<script>  var popupUrl = 'result_log.php'; window.open(popupUrl, 'ErrorLogPopup', 'width=800,height=600'); </script>";		
        }			
			  
	} else {
		echo "<script>alert('파일 업로드 실패.');</script>";
	}

}

$conn = null;
?>
