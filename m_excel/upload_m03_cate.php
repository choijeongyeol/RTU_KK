<?php
session_start();
$partner_id = $_SESSION['partner_id'];

if (empty($partner_id)) {
    echo "<script>alert('세션 끊김. 재로그인 바랍니다.');</script>";
    exit();
}

// 에러 표시 설정
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$this_f_name = "m03_cate_".$partner_id; // 분류

// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// MySQL 연결 설정
include_once($_SERVER['DOCUMENT_ROOT'].'/gm/inc/fn.php');

$conn = getDbConnection();

clean_folder(); // /m_excel/data_excel/ 폴더에 들어있는, 엑셀업로드처리 되는 임시 엑셀파일들을 삭제함.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 업로드 디렉토리 설정
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/m_excel/data_excel/';

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
            foreach ($rows as $index => $row) {
                if ($header) {
                    $header = false;
                    $row[] = "Insert Result"; // 헤더에 결과 열 추가
                    $results[] = $row;
                    continue;
                }

                // 엑셀로부터 name 읽기
                $name = $row[0];
                $date = date("Y-m-d H:i:s");

                // 동일한 카테고리가 존재하는지 확인
                $sqlCheck = "SELECT COUNT(*) FROM wms_cate WHERE partner_id = :partner_id AND cate_name = :name AND cate_use = 'Y'";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bindParam(':partner_id', $partner_id);
                $stmtCheck->bindParam(':name', $name);
                $stmtCheck->execute();
                $count = $stmtCheck->fetchColumn();

                if ($count > 0) {
                    throw new Exception('중복된 카테고리입니다.');
                }

                // 데이터베이스에 데이터 삽입
                $sql = "INSERT INTO wms_cate (partner_id, cate_name, cate_rdate) VALUES (:partner_id, :name, :date)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':partner_id', $partner_id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':date', $date);

                try {
                    if ($stmt->execute()) {
                        $row[] = "Success";
                    } else {
                        $row[] = "Fail";
                        throw new Exception('데이터베이스 삽입 중 오류 발생');
                    }
                } catch (Exception $e) {
                    logAndShowErrorPopup($e, $row, $index, $this_f_name);
                    exit;
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

            echo "<script> alert('엑셀 업로드 완료'); </script>";
        } catch (Exception $e) {
            // 롤백
            $conn->rollBack();
            logAndShowErrorPopup($e, $row, $index, $this_f_name);
        }
    } else {
        echo "<script>alert('파일 업로드 실패.');</script>";
    }
}

$conn = null;

// 오류 로그 기록 및 팝업 출력 함수
function logAndShowErrorPopup($e, $row, $index, $this_f_name) {
    // 로그 파일 초기화
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/m_excel/data_logs/error_' . $this_f_name . '.log';

    // 예외 메시지를 한글 메시지로 변환
    $errorMessage = $e->getMessage();
    if (strpos($errorMessage, 'Duplicate entry') !== false || $errorMessage == '중복된 카테고리입니다.') {
        $translatedErrorMessage = '중복된 카테고리입니다';
    } else {
        $translatedErrorMessage = '알 수 없는 오류 발생';
    }

    // 로그 메시지 작성
    $logMessage = '' . PHP_EOL . 
                  '시각 : [' . date('Y-m-d H:i:s') . ']' . PHP_EOL . 
                  '위치 : 엑셀 라인번호 ' . ($index + 1) . PHP_EOL .  
                  '항목 : ' . json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL . 
                  '내용 : ' . $translatedErrorMessage . PHP_EOL;

    // 기존 로그 파일의 내용을 읽어옵니다.
    $existingLogs = file_get_contents($logFile);

    // 새로운 로그를 기존 로그의 앞에 추가하여 씁니다.
    file_put_contents($logFile, $logMessage . $existingLogs);

    // 팝업 메시지
    echo "<script>var popupUrl = '/m_excel/upload_result_log.php?logfile=" . $this_f_name . "'; window.open(popupUrl, 'ErrorLogPopup', 'width=400,height=300');</script>";
}

function clean_folder() {
    // 시작 시 $_SERVER['DOCUMENT_ROOT'].'/m_excel/data_excel/' 폴더의 모든 파일 삭제
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/m_excel/data_excel/';
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . '*'); // 폴더 내의 모든 파일 가져오기
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // 파일 삭제
            }
        }
    }
}
?>
