<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php'); // PhpSpreadsheet 라이브러리 사용

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 엑셀 파일 생성
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("사용자 등록 샘플");

// 헤더 작성
$sheet->setCellValue('A1', '사용자 이름');
$sheet->setCellValue('B1', '비밀번호');
$sheet->setCellValue('C1', '연락처');
$sheet->setCellValue('D1', '주소');
$sheet->setCellValue('E1', '상세주소');
$sheet->setCellValue('F1', '이메일');
$sheet->setCellValue('G1', '법정코드');

// 샘플 데이터 추가
$sheet->setCellValue('A2', '홍길동');
$sheet->setCellValue('B2', 'password123');
$sheet->setCellValue('C2', '010-1234-5678');
$sheet->setCellValue('D2', '서울특별시 종로구');
$sheet->setCellValue('E2', '1층');
$sheet->setCellValue('F2', 'example@email.com');
$sheet->setCellValue('G2', '11110101');

// HTTP 응답 설정
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="user_upload_sample.xlsx"');
header('Cache-Control: max-age=0');

// 엑셀 파일 출력
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
