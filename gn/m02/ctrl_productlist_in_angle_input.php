<? include_once($_SERVER['DOCUMENT_ROOT'].'/gn/inc/head.php'); ?>
<style>
    body {
        overflow-y: scroll; /* 수직 스크롤 바를 항상 표시 */
    }
    .popup-container {
        max-height: 70vh; /* 최대 높이를 화면의 80%로 제한 */
        overflow-y: auto; /* 세부 컨텐츠가 더 길어지면 수직 스크롤 바를 표시 */
    }
</style>
 </head>
 <body style="overflow: hidden;background:#405469"> 
 
 
		
 <?
    // 화면에 앵글이름 출력을 위한 처리 
     if ((isset($_GET['arg2']))&&(isset($_GET['arg3']))){  // arg2 = warehouse_id ,  arg3 = angle_id
			$result = select_angle_one($_GET['arg2'],$_GET['arg3']);
	
			// 특정 데이터 1개 추출
			if (!empty($result)) {						
				$angle_name = $result[0]['angle_name'];  	
			} else {
				//echo "No data found.";	
			}		
     } 
	 
 
	  $stock_count =  stock_count($_GET['arg2'],$_GET['arg3']);	// 앵글안의 제품 종류
 	  
	  $result_sum =  stock_sum($_GET['arg2'],$_GET['arg3']);	// 앵글안의 제품 총수량
	  
	  $warehouse_name =  stock_warehouse_name($_GET['arg2']);	// 창고명 알아오기
	  
	  
 
?>
 	 

	<br />
	<center>
	<table width="90%" style="color:#fff" bgcolor="#fff" style="border-spacing:0px;" border="1">
	<tr bgcolor="#fff" >
		<td bgcolor="#405469" style="padding:10px" width="20%">창고명</td>
		<td bgcolor="#eee" style="padding:10px;color:#405469"  width="30%"><?echo $warehouse_name?></td>
		<td bgcolor="#405469" style="padding:10px" width="20%">앵글명</td>
		<td bgcolor="#eee" style="padding:10px;color:#405469" width="30%"><?echo $angle_name?></td>
	</tr>
	<tr><td height="1px"  bgcolor="#fff"></td> <td height="1px"  bgcolor="#405469"></td> <td height="1px"  bgcolor="#fff"></td> <td height="1px"  bgcolor="#405469"></td></tr>
	<tr bgcolor="#fff">
		<td bgcolor="#405469" style="padding:10px">제품수</td>
		<td bgcolor="#eee" style="padding:10px;color:#405469"><?echo $stock_count[0]['count']?></td>
		<td bgcolor="#405469" style="padding:10px">총수량</td>
		<td bgcolor="#eee" style="padding:10px;color:#405469"><?echo number_format($result_sum);?></td>
	</tr>
	</table>
	</center>
	<div class="ln_solid"></div>		


     <div class="popup-container">
		<?php
		// 제품 목록 가져오기
		$result_list =  stock_list($_GET['arg2'],$_GET['arg3']);	// 앵글안의 제품 총수량
		?>				
			

		<table id="tb_border" class="table-striped table-bordered dataTable dataCustomTable"   aria-describedby="datatable_info" style="background:#fff;width:90%;margin:0 auto;">
			<thead>
				<tr>
					<th>NO</th>
					<th>제품명</th>
					<th>수량</th>
					<th>분류명</th>
				</tr>
			</thead>
			<tbody>
			<?
                 $i = 1;
				if ($result_list) {
					foreach ($result_list as $in_stock_item) {
						echo "<tr>";					 
						echo "<td>".$i."</td>";
						echo "<td>{$in_stock_item['item_name']}</td>";
						echo "<td>".number_format("{$in_stock_item['item_cnt']}")."</td>";
						echo "<td>{$in_stock_item['cate_name']}</td>";
						$i = $i + 1;	
					}
					echo "</tr>";
				} else {
					echo "<tr><td colspan='4'>등록된 제품 없음</td></tr>";
				}
			?>					

			</tbody>
		</table>

	  </div>





 </body>
</html>

<?
		add_history('A','앵글내 제품목록 조회',$warehouse_name,$angle_name);
?>