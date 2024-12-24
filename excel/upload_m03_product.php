<?php
// 에러 표시 설정
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$this_f_name = "m03_product"; // 분류

// Composer autoload 파일을 include
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// MySQL 연결 설정
include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/fn.php');

$conn = getDbConnection();

clean_folder(); // /excel/data_excel/ 폴더에 들어있는, 엑셀업로드처리 되는 임시 엑셀파일들을 삭제함.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 업로드 디렉토리 설정
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/excel/data_excel/';

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

		$up_YN = "Y";  // 업로드할지, 하지말아야할지 제품 이름중복 발견시 N, 중단..
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
			$code_plus = 0; // 코드값의 중복방지위해, 값 증가분 넣음.
			
            foreach ($rows as $index => $row) {
                if ($header) {
                    $header = false;
                    $row[] = "Insert Result"; // 헤더에 결과 열 추가
                    $results[] = $row;
                    continue;
                }

                // 엑셀로부터 제품명과 분류명 읽기
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$product_name   = $row[0];
				$item_cate_name = $row[1];
				
				$up_YN = duplicate_name2($product_name); // 중복검사
				
				if ($up_YN=="N") {//foreach 빠져 나가기
				   logAndShowErrorPopup($e, $row, $index,$this_f_name);    	
				   break;
				}
				
				
				// 자동 생성되는 값들
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$date = date("Y-m-d H:i:s");
				$code = make_product_code($code_plus);   
				$item_cate_num = cate_name_to_cate_id($item_cate_name);  

				// 데이터베이스에 데이터 삽입
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$sql = "INSERT INTO wms_items (item_code, item_name, item_rdate, item_cate) VALUES (:code, :name, :date, :item_cate)";			
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $product_name);
				$stmt->bindParam(':date', $date);
				$stmt->bindParam(':item_cate', $item_cate_num);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					try {
						if ($stmt->execute()) {
							$row[] = "Success";
						} else {
							$row[] = "Fail";
							throw new Exception('데이터베이스 삽입 중 오류 발생');
						}
					} catch (Exception $e) {

						// 처리 중 오류 발생 시 로그 기록 및 팝업 출력
						logAndShowErrorPopup($e, $row, $index,$this_f_name);
						exit; // 프로그램 종료
					}
					
                    $results[] = $row;
                    $code_plus++;
            }

			if ($up_YN=="Y") {	// foreach 에 N 이 없으면,
				// 데이터베이스 작업 커밋
				$conn->commit();            
				echo "<script> alert('엑셀 업로드완료'); </script>";
			}
			
        } catch (Exception $e) {
            // 롤백
            $conn->rollBack();
			// 처리 중 오류 발생 시 로그 기록 및 팝업 출력
			logAndShowErrorPopup($e, $row, $index,$this_f_name);    
        }           
              
    } else {
        echo "<script>alert('파일 업로드 실패.');</script>";
    }

}

$conn = null;

// 오류 로그 기록 및 팝업 출력 함수
function logAndShowErrorPopup($e, $row, $index,$this_f_name) {
    // 로그 파일 초기화
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/excel/data_logs/error_'.$this_f_name.'.log';
    //file_put_contents($logFile, "");  // 기존 로그 파일 내용을 비웁니다.

    // 예외 메시지를 한글 메시지로 변환
    $errorMessage = $e->getMessage();
    if ((strpos($errorMessage, 'item_name') !== false) &&  (strpos($errorMessage, 'Duplicate entry') !== false)) {
        $translatedErrorMessage = '제품명 중복등록';
    }elseif ( (strpos($errorMessage, 'Duplicate entry')!== false) && (strpos($errorMessage, 'unique_item_delYN') !== false) ) {
        $translatedErrorMessage = '제품명 중복등록';
    }elseif ( (strpos($errorMessage, 'item_cate')!== false) && (strpos($errorMessage, 'cannot be null') !== false) ) {
        $translatedErrorMessage = '해당분류명 없음';
    } else {
       $translatedErrorMessage = '알 수 없는 오류 발생';
       //  $translatedErrorMessage = $errorMessage;
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
    echo "<script>var popupUrl = '/excel/upload_result_log.php?logfile=".$this_f_name."'; window.open(popupUrl, 'ErrorLogPopup', 'width=400,height=300');</script>";  
}


function clean_folder(){
	// 시작 시 $_SERVER['DOCUMENT_ROOT'].'/excel/data_excel/' 폴더의 모든 파일 삭제
	$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/excel/data_excel/';
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
