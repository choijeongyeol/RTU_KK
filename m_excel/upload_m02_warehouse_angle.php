<?php
session_start();
$partner_id = $_SESSION['partner_id'];

if($partner_id==""){ echo "<script>alert('세션끊김.재로그인바랍니다.');</script>";  exit(); }

// 에러 표시 설정
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$this_f_name = "warehouse_angle_".$partner_id; // 분류

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

        $up_YN = "Y";  // 업로드할지, 하지말아야할지 제품 이름중복 발견시 N, 중단..
        try {
            // 데이터베이스 트랜잭션 시작
            $conn->beginTransaction();
            
            $spreadsheet = IOFactory::load($uploadFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // 첫 번째 행은 헤더로 간주하고 건너뜁니다.
            $header = true;
            $existingWarehouses = []; // 이미 존재하는 창고명 배열

            foreach ($rows as $rowIndex => $row) {
                if ($header) {
                    $header = false;
                    continue;
                }

                // 엑셀로부터 데이터 읽기
                $warehouseName = $row[0];
                $angleName = isset($row[1]) ? $row[1] : null;

                // 창고명 중복 검사
                if (!in_array($warehouseName, $existingWarehouses)) {
                    $up_YN = check_duplicate_warehouse_name($partner_id, $warehouseName); // 창고 중복검사
                    if ($up_YN == "N") {
                        // 이미 존재하는 창고명이므로 창고 ID를 가져와 앵글 삽입 처리
                        $sql = "SELECT warehouse_id FROM wms_warehouses WHERE partner_id = :partner_id and warehouse_name = :name and delYN='N'";
                        //$sql = "SELECT warehouse_id FROM wms_warehouses WHERE  warehouse_name = :name and delYN='N'";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':partner_id', $partner_id);
                        $stmt->bindParam(':name', $warehouseName);
                        $stmt->execute();
                        $warehouseId = $stmt->fetch(PDO::FETCH_ASSOC)['warehouse_id'];
                        
                        // 앵글이 있을 경우 앵글 중복 검사
                        if ($angleName !== null && !check_duplicate_angle($partner_id, $angleName, $warehouseId)) {
                            // 앵글 데이터베이스에 데이터 삽입
                            $date = date("Y-m-d H:i:s");

                            $sql = "INSERT INTO wms_angle (partner_id, angle_name, warehouse_id, angle_rdate) VALUES (:partner_id, :name, :id, :date)";
                           // $sql = "INSERT INTO wms_angle ( angle_name, warehouse_id, angle_rdate) VALUES ( :name, :id, :date)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':partner_id', $partner_id);
                            $stmt->bindParam(':name', $angleName);
                            $stmt->bindParam(':id', $warehouseId);
                            $stmt->bindParam(':date', $date);

                            if (!$stmt->execute()) {
                                throw new Exception('앵글 데이터베이스 삽입 중 오류 발생');
                            }
                        } elseif ($angleName !== null) {
                            throw new Exception('앵글 중복 발생');
                        }

                        // 창고명 중복이고 앵글이 없는 경우 예외 발생
                        if ($angleName === null) {
                            throw new Exception('창고명 중복 및 앵글 없음');
                        }

                        // 다음 행으로 넘어감
                        continue;
                    }

                    // 데이터베이스에 창고 삽입
                    $date = date("Y-m-d H:i:s");
                    $code = generate_warehouse_code();

                   $sql = "INSERT INTO wms_warehouses (partner_id, warehouse_code, warehouse_name, warehouse_rdate) VALUES (:partner_id, :code, :name, :date)";
                    // $sql = "INSERT INTO wms_warehouses ( warehouse_code, warehouse_name, warehouse_rdate) VALUES ( :code, :name, :date)";
                    $stmt = $conn->prepare($sql);
					$stmt->bindParam(':partner_id', $partner_id);
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':name', $warehouseName);
                    $stmt->bindParam(':date', $date);

                    if (!$stmt->execute()) {
                        throw new Exception('데이터베이스 삽입 중 오류 발생');
                    }

                    // 새로 삽입된 창고 ID 가져오기
                    $warehouseId = $conn->lastInsertId();
                    $existingWarehouses[] = $warehouseName; // 배열에 추가

                    // 앵글이 있을 경우 앵글 중복 검사
                    if ($angleName !== null && !check_duplicate_angle($partner_id, $angleName, $warehouseId)) {
                        // 앵글 데이터베이스에 데이터 삽입
                       $sql = "INSERT INTO wms_angle (partner_id, angle_name, warehouse_id, angle_rdate) VALUES (:partner_id, :name, :id, :date)";
                        // $sql = "INSERT INTO wms_angle (angle_name, warehouse_id, angle_rdate) VALUES (:name, :id, :date)";
                        $stmt = $conn->prepare($sql);
						$stmt->bindParam(':partner_id', $partner_id);						
                        $stmt->bindParam(':name', $angleName);
                        $stmt->bindParam(':id', $warehouseId);
                        $stmt->bindParam(':date', $date);

                        if (!$stmt->execute()) {
                            throw new Exception('앵글 데이터베이스 삽입 중 오류 발생');
                        }
                    } elseif ($angleName !== null) {
                        throw new Exception('앵글 중복 발생');
                    }
                } else {
                    // 이미 존재하는 창고명이므로 앵글 중복 검사
                   $sql = "SELECT warehouse_id FROM wms_warehouses WHERE partner_id = :partner_id and  warehouse_name = :name and delYN='N'";
                     //$sql = "SELECT warehouse_id FROM wms_warehouses WHERE warehouse_name = :name and delYN='N'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':partner_id', $partner_id);
                    $stmt->bindParam(':name', $warehouseName);
                    $stmt->execute();
                    $warehouseId = $stmt->fetch(PDO::FETCH_ASSOC)['warehouse_id'];
                    
                    if ($angleName !== null && !check_duplicate_angle($partner_id, $angleName, $warehouseId)) {
                        // 앵글 데이터베이스에 데이터 삽입
                        $date = date("Y-m-d H:i:s");

                        $sql = "INSERT INTO wms_angle (partner_id, angle_name, warehouse_id, angle_rdate) VALUES (:partner_id, :name, :id, :date)";
                        //$sql = "INSERT INTO wms_angle (angle_name, warehouse_id, angle_rdate) VALUES (:name, :id, :date)";
                        $stmt = $conn->prepare($sql);
						$stmt->bindParam(':partner_id', $partner_id);						
                        $stmt->bindParam(':name', $angleName);
                        $stmt->bindParam(':id', $warehouseId);
                        $stmt->bindParam(':date', $date);

                        if (!$stmt->execute()) {
                            throw new Exception('앵글 데이터베이스 삽입 중 오류 발생');
                        }
                    } elseif ($angleName !== null) {
                        throw new Exception('앵글 중복 발생');
                    } else {
                        // 창고명 중복이고 앵글이 없는 경우 예외 발생
                        throw new Exception('창고명 중복 및 앵글 없음');
                    }
                }
            }

            // 모든 행이 정상 처리되면 커밋
            $conn->commit();
            echo "<script> alert('엑셀 업로드 완료'); </script>";

        } catch (Exception $e) {
            // 롤백
            $conn->rollBack();
            logAndShowErrorPopup($e, $row, $rowIndex, $this_f_name);
        }
    } else {
        echo "<script>alert('파일 업로드 실패.');</script>";
    }
}

$conn = null;

// 오류 로그 기록 및 팝업 출력 함수
function logAndShowErrorPopup($e, $row, $index, $this_f_name) {
    // 로그 파일 초기화
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/m_excel/data_logs/error_'.$this_f_name.'.log';
    //file_put_contents($logFile, "");  // 기존 로그 파일 내용을 비웁니다.

    // 예외 메시지를 한글 메시지로 변환
    $errorMessage = $e->getMessage();
    if (strpos($errorMessage, 'Duplicate entry') !== false) {
        $translatedErrorMessage = 'ID 중복 등록';
    } else if (strpos($errorMessage, '앵글 중복 발생') !== false) {
        $translatedErrorMessage = '앵글 중복 등록';
    } else if (strpos($errorMessage, '창고명 중복 및 앵글 없음') !== false) {
        $translatedErrorMessage = '창고명 중복 및 앵글 없음';
    } else {
       // $translatedErrorMessage = '알 수 없는 오류 발생';
        $translatedErrorMessage = $errorMessage;
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
    echo "<script>var popupUrl = '/m_excel/upload_result_log.php?logfile=".$this_f_name."'; window.open(popupUrl, 'ErrorLogPopup', 'width=400,height=300');</script>";  
}

function clean_folder(){
    // 시작 시 $_SERVER['DOCUMENT_ROOT'].'/m_excel/data_excel/' 폴더의 모든 파일 삭제
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/m_excel/data_excel/';
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . '*'); // 폴더 내의 모든 파일 가져오기
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // 파일 삭제
            }
        }
    }   
}
 
function check_duplicate_warehouse_name(int $partner_id, string $name): string {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_warehouses WHERE partner_id = :partner_id AND warehouse_name = :name AND delYN='N'");
    //$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_warehouses WHERE warehouse_name = :name AND delYN='N'");
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] != 0) {
        return "N";
    } else {
        return "Y";
    }
}
 

function check_duplicate_angle(int $partner_id, string $angleName, int $warehouseId): bool {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_angle WHERE partner_id = :partner_id AND angle_name = :angleName AND warehouse_id = :warehouseId AND delYN='N'");
   // $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wms_angle WHERE  angle_name = :angleName AND warehouse_id = :warehouseId AND delYN='N'");
    $stmt->bindParam(':partner_id', $partner_id);
    $stmt->bindParam(':angleName', $angleName);
    $stmt->bindParam(':warehouseId', $warehouseId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'] != 0;
}

function generate_warehouse_code() {
    $result = getwms_warehouse_last1();

    // 특정 데이터 1개 추출
    if (!empty($result)) {
        $specificData = $result[0]['warehouse_id'];  
        $specificData = $specificData + 1000;
    }
    $specificData = "W" . $specificData;
    return $specificData;
}

//function get_last_warehouse_id() {
 //   global $conn;
 //   $stmt = $conn->prepare("SELECT warehouse_id FROM wms_warehouses where partner_id = :partner_id  ORDER BY warehouse_rdate DESC LIMIT 1");
//	$stmt->bindParam(':partner_id', $partner_id);
//    $stmt->execute();
//    $result = $stmt->get_result();
//    $row = $result->fetch_assoc();
//    return $row['warehouse_id'];
//}
?>
