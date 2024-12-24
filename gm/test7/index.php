<?php
// GD 라이브러리가 설치되어 있는지 확인
if (!function_exists('imagecreate')) {
    die("GD 라이브러리가 설치되어 있지 않습니다.");
}

// 바코드를 생성할 데이터
$product_code = "123456789"; // 제품 코드

// 바코드 이미지 생성
$barcode = imagecreatetruecolor(400, 200); // 바코드 이미지 크기 지정

// 흰색 배경
$bg_color = imagecolorallocate($barcode, 255, 255, 255);
imagefill($barcode, 0, 0, $bg_color);

// 검은색 바코드
$barcode_color = imagecolorallocate($barcode, 0, 0, 0);
imageline($barcode, 10, 100, 390, 100, $barcode_color); // 바코드 라인 그리기

// 제품 코드를 바코드에 출력
$text_color = imagecolorallocate($barcode, 0, 0, 0);
imagestring($barcode, 5, 150, 160, $product_code, $text_color); // 바코드에 텍스트 출력

// 바코드 이미지 출력
header('Content-type: image/png');
imagepng($barcode);

// 바코드 이미지 메모리 해제
imagedestroy($barcode);
?>
