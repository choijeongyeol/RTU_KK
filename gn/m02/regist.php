<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>

<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/topmenu.php'); ?>

	<div id="container">

	<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/leftmenu.php'); ?>

		<div class="section">
			<div class="page-top">
				<h3>창고관리</h3>
				<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/search.php'); ?>		
			</div>
			<div class="page-content">
				<!-- 내용시작 -->
				<div class="list-top">
					<div class="left">
						<span class="total">Total : 6</span>
					</div>
					<div class="right">
						<form method="post" action="list.php" name="search" onsubmit="return ;">
						<div class="data-search">
							<select name="search">
								<option value="Name" >이름</option>
								<option value="Phone" >휴대폰</option>
							</select>
							<input type="text" name="SearchString" size="20" value="">
							<button class="btn dgray" onClick="checkIt()">검색</button>
							<button type="button" class="btn gray" onClick="location.href='list.php'">초기화</button>
						</div>
						</form>
					</div>
				</div>
				
				<!-- 내용시작 -->
				<form method="post" action="account_regist_ok.php" name="myform">
				<table class="data-regist">
					<colgroup>
						<col class="w15">
						<col>
						<col class="w15">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>소속지점 <span class="required">*</span></th>
							<td>
								<select name="tb_store_Code">
									<!-- <option value="" selected>선택</option> -->
									<?
									// 결과 가져오기
									while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										// 각 행의 데이터 처리
										$Code = $row['Code']; //'지점코드
										$Name = $row['Name']; //'지점명
									?>								
									<option value="<?=$Code?>"><?=$Name?></option>
									<?
									}
									?>
								</select>
							</td>
							<th>이름 <span class="required">*</span></th>
							<td><input type="text" name="Name" class="w45"></td>
						</tr>
						<tr>
							<th>아이디 <span class="required">*</span></th>
							<td><input type="text" name="ID" class="w45"></td>
							<th>패스워드 <span class="required">*</span></th>
							<td><input type="password" name="Pwd" class="w45"></td>
						</tr>
						<tr>
							<th>생년월일 </th>
							<td><input type="text" name="BirthDay" id="BirthDay" class="w45"></td>
							<th>휴대폰 <span class="required">*</span></th>
							<td><input type="text" name="Phone" class="w45"></td>
						</tr>
						<tr>
							<th>이메일 <span class="required">*</span></th>
							<td colspan="3"><input type="text" name="Email" class="w80"></td>
						</tr>
					</tbody>
				</table>

				<div class="btns">
					<button type="button" onClick="checkIt()" class="btn blue">등 록</button>
					<button type="button" class="btn dgray" onClick="history.back()">취 소</button>
				</div>
				</form>

				<!-- //내용끝-->
			</div>
		</div>
	</div><!--//container-->
<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/foot.php'); ?>