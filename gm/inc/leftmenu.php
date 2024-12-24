<?
$last_dir = mb_substr(getcwd(),-3);  // m01 / m02 / m03 
	//echo $last_dir;
	// left_top_title ="운영관리";
	// left_sub_menu ="설정관리 / ID관리 등등"
	
 $previousPageURL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';	
 
 if ("https://devbine.cafe24.com/gm/login.php" == $previousPageURL ) {  $_SESSION['left_location'] = 1; }
 
 $cURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

 if(($cURL=="https://devbine.cafe24.com/gm/m01/list.php")||($cURL=="https://devbine.cafe24.com/gm/m02/list.php")) {	 $_SESSION['left_location'] = 1;}
 
 
	
	if ($_GET['left_location']!="") {
		$_SESSION['left_location'] = $_GET['left_location'];	
	} 
	
	switch ($last_dir) {
		case "m01" : $left_top_title = "운영관리"; 
					 $left_sub_cnt = 2;
					 $left_menu[0] = "운영자목록";
					 $left_link[0] = "/gm/m01/list.php?left_location=1";
					 
					 $left_menu[1] = "권한설정";
					 $left_link[1] = "/gm/m01/list.php?left_location=2";
					 break;			
					 
		case "m02" : $left_top_title = "창고관리"; 
					 $left_sub_cnt = 1;
					 $left_menu[0] = "창고목록";
					 break;
					 
		case "m03" : $left_top_title = "제품관리"; 
					 $left_sub_cnt = 3;
					 $left_menu[0] = "제품목록";
					 $left_link[0] = "/gm/m03/list.php?left_location=1";	
					 
					 $left_menu[1] = "제품입고 history";
					 $left_link[1] = "/gm/m03/list_history.php?left_location=2";	
					 
					 $left_menu[2] = "제품분류 관리";
					 $left_link[2] = "/gm/m03/cate_list.php?left_location=3";					 
					 break;
  
		case "m04" : $left_top_title = "재고관리"; 
					 $left_sub_cnt = 2;
					 $left_menu[0] = "재고목록";
					 $left_link[0] = "/gm/m04/list.php?left_location=1";	
					 $left_menu[1] = "제품이동 history";
					 $left_link[1] = "/gm/m04/list_history.php?left_location=2";					 
					 break;
  
		case "m05" : $left_top_title = "입고지시관리"; 
					 $left_sub_cnt = 1;
					 $left_menu[0] = "입고지시 목록";
					 $left_link[0] = "/gm/m05/list.php?left_location=1";	
					 //$left_menu[1] = "입고지시 등록";
					 //$left_link[1] = "/gm/m05/write.php?left_location=2";					 
					 break;
		
		case "m06" : $left_top_title = "ㅇㅇ관리"; break;
	}
?>
 

	<!-- <div class="lnav">
	<h2><?echo $left_top_title?></h2>
		<ul class="dep1">
		<?  $i = 0;
			while($i < $left_sub_cnt){	
				if ($i==($_SESSION['left_location']-1)) {
					echo "<li class='on'><a href='".$left_link[$i]."'>".$left_menu[$i]."</a></li>";					
				}else{
					echo "<li ><a href='".$left_link[$i]."'>".$left_menu[$i]."</a></li>";					
				}
				$i=$i+1;	
			}
		?>
		</ul>
 
	</div> -->

	
				<!-- <li ><a href="/gm/m1/teacher_list.php">설정관리</a></li>
				<li class="on"><a href="/gm/m1/account_list.php">직원 ID관리</a></li> -->


