<?
	// 페이징 계산
	$totalItems = $totalcount; // 전체 레코드 수
	
	//$itemsPerPage = 10; // 페이지당 레코드 수
	$itemsPerPage = isset($new_itemsPerPage) ? $new_itemsPerPage : 10; // 재지정하는 페이지당 레코드 수	
	$itemsPerPage = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : 10; // 페이지당 레코드 수	
	
	
	$currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // 현재 페이지
	$url = $_SERVER['PHP_SELF']; // 페이지 링크에 사용할 기본 URL
	
	//시작레코드 공식 :  페이지번호 X 페이지당 레코드 수 - 페이지당 레코드 수	
	$start_record_number =   $currentPage * $itemsPerPage - $itemsPerPage;
	
	// 출력 NO desc 
	$desc_start_no = $totalcount - ($currentPage - 1) * $itemsPerPage;
	
	
	// 검색
	$search = isset($_POST['search']) ? $_POST['search'] : "";  
	$SearchString = isset($_POST['SearchString']) ? $_POST['SearchString'] : "";  
?>	