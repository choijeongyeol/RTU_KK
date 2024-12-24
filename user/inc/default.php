<?  // require_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/fn_api.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');


	//#### 로그인 정보 ####
	$User_id			= $_COOKIE['guser']['User_id'];   //request.cookies("guser")("User_id")
	$User_name			= $_COOKIE['guser']['User_name'];    //request.cookies("guser")("User_name")
	$User_zizum1		= $_COOKIE['guser']['User_zizum1'];   //request.cookies("guser")("User_zizum1")	'지점코드
	$User_zizum2		= $_COOKIE['guser']['User_zizum2'];   //request.cookies("guser")("User_zizum2")	'지점명
 

	//#### 글로벌 변수 선언 ####
	$ $Imagename = [];					//'첨부파일명

	//#### 잘못된 경로로 들어올 경우 ##
	$IP				= $_SERVER['REMOTE_ADDR'];


	// 현재 실행 중인 스크립트 파일의 경로와 이름 가져오기
	$currentScript = $_SERVER['PHP_SELF'];
	
	if ((substr($currentScript, -9) == "login.php") || (substr($currentScript, -12) == "login_ok.php")){
		
	}else{
		
		if ($_SESSION['user_id'] == "") {
		?>
			<SCRIPT type='text/javascript'>
			alert('인증정보가 유효하지 않습니다. 다시 로그온 하시기 바랍니다.....');
			top.location.href='/user/login.php';
			</SCRIPT>
		<?
			exit();	
		}
	}
?>
  