	<!-- </head>
<body>
<div class="wrap">
<div id="header">
		<h1><b>WMS</b> 관리자모드</h1>
		<div class="gnav">
			<ul>
				<li><a href="/gm/m01/list.php">운영관리</a></li>
				<li><a href="/gm/m02/list.php">창고관리</a></li>
				<li><a href="/gm/m03/list.php?left_location=1">제품관리</a></li>
				<li><a href="/gm/m04/list.php?left_location=1">재고관리</a></li>
				<li><a href="/gm/m05/list.php?left_location=1">입고지시관리</a></li>
				<li><a href="/gm/m06/list.php">출고지시관리</a></li>
			</ul>
		</div>
		<div class="links">
			<span class="user-info"><? echo $_SESSION['admin_name']." (".$_SESSION['admin_id'].") ";?></span>
			<a href="#" class="site">사이트 바로가기</a>
			<a href="/gm/logout.php" class="logout">로그아웃</a>
		</div>
	</div> -->
	
	
  </head>

  <body class="nav-md"> 
    <div class="container body">  
	
	
	

<?
$change_state = is_change_pw();

if (($change_state[0]['pw_setting_exist']=="0") && (basename($_SERVER['PHP_SELF'])!="myinfo.php")&&($_SESSION['sys'] == "N")) { ?>

<!-- 버튼을 클릭하면 모달이 열립니다 -->
<button id="openModalBtn"><!-- 모달 열기 --></button>

<!-- 모달 창 -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
		<center>
        <h2 style="color:#ff0000">비밀번호 변경후 이용하시기 바랍니다.</h2>
        <p style="font-size:24px">비밀번호 변경 바로가기 <a href="/gm/home/myinfo.php"><span style="color:#ff0000">click !!</span></a></p>
		</center>
    </div>
</div>

<script>
    // 페이지가 로드되면 모달 창을 자동으로 엽니다
    window.onload = function() {
        const modal = document.getElementById("myModal");
        const span = document.getElementsByClassName("close")[0];
        
        // 모달 창을 엽니다
        modal.style.display = "block";
        
        // 닫기 버튼을 클릭하면 모달이 닫힙니다
        span.onclick = function() {
            modal.style.display = "none";
        }

        // 모달 바깥을 클릭하면 모달이 닫힙니다
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }
</script>

<?}?>
 