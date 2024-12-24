<?php require_once($_SERVER['DOCUMENT_ROOT'].'/gu/tp/inc/logo.php'); ?>
		<header>
			<nav>
				<ul>
					<li>
						<a href="#">대시보드</a>
						<ul>
							<li><a href="#">기업소개</a></li>
							<li><a href="#">연혁</a></li>
							<li><a href="#">주요 실적</a></li>
							<li><a href="#">인증서</a></li>
							<li><a href="#">오시는길</a></li>
						</ul>
					</li>
					<li><a href="#">모니터링</a></li>
					<li><a href="#">통계분석</a></li>
					<li><a href="#">게시판</a></li>
					<li><a href="#">설정</a></li>
					<li><a href="#">미정메뉴</a>
						<ul>
							<li><a>[ <? echo $_SESSION['admin_name'];?> ]</a></li>
							<li><a href="logout.php" target="_top">로그아웃</a></li>
							
							
							<? if ($_SESSION['admin_role'] < 10) { ?>
							<li><a href="create_partner_list.php">기업관리 (BINE만 가능)</a></li>
							
							<? } ?>
							
							<? if ($_SESSION['spartner_id'] < 1000) { ?>
							<li><a href="partner_list.php">관리자관리</a></li>
							
							<? } ?>
							<li><a href="spartner_list.php">지자체관리</a></li>
							
							<li><a href="user_list.php">이용자 관리</a></li>
							<!-- <li><a href="user_form.php">이용자 등록</a></li> -->
							<li><a href="lora_form.php">로라 등록</a></li>
							<li><a href="facility_form.php">설비관리</a></li>	
							<!-- <li><a href="facility_subscription.php">설비연결관리</a></li>	 -->
							
							<li><a href="/gu/view/list_sunlight_0101.php">태양광 단상목록</a></li>
							<li><a href="/gu/view/list_sunlight_0103.php">태양광 삼상목록</a></li>
							<li><a href="list_user_lora_cid.php">이용자별 로라 / CID 목록</a></li>
							
							<li><a href="issue_history_admin_cate.php">장애 이력 카테고리관리</a></li>
							<li><a href="issue_history_admin_input.php">장애 이력 입력</a></li>
							<li><a href="issue_history_list.php">장애 이력 목록</a></li>
							<li><a href="as_request_list.php">장애 AS신청 목록</a></li>
							<li><a href="as_request.php">AS신청하기(고객테스트)</a></li>
							
							<li><a href="technician_list.php">장애처리담당자관리</a></li>
							
							
							<li><a href="notice_list.php">공지사항</a></li>
							<li><a href="notice_write.php">공지사항 등록</a></li>
							<li><a href="faq_category_list.php">FAQ 카테고리 관리</a></li>
							<li><a href="faq_list.php">FAQ 내용 관리</a></li>

							<li><a href="qna_list.php">QNA 목록 관리</a></li>
							<li><a href="qna_add.php">QNA 등록(고객테스트)</a></li>
							 
							<li><a href="daily_statistics.php">일별 통계 분석</a></li>
							<li><a href="monthly_statistics.php">월별 통계 분석</a></li>
							 
							<li><a href="weather_js.php">기상정보</a></li>
						</ul>					
					</li>
				</ul>
			</nav>
		</header>
