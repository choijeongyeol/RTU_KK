<?php  session_start();

require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/fn_api_RTU.php');
 
// RTU_6431 데이터를 가져오기
$rows = $userManager->list_SolarInputData_multi();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTU 태양광 입력 데이터 리스트 (삼상)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .table-container {
            display: block;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            border: 1px solid #ddd;
        }

        .table-header, .table-row {
            display: flex;
            justify-content: flex-start;
            border-bottom: 1px solid #ddd;
        }

        .table-header {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .table-cell {
            padding: 4px 6px;
            border-right: 1px solid #ddd;
            flex-grow: 1;
            flex-basis: 100px;
            min-width: 50px;
            max-width: 300px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            line-height: 1.2;
            font-size: 0.85rem;
        }
        .table-hfcell {
            padding: 4px 6px;
            border-right: 1px solid #ddd;
            flex-grow: 1;
            flex-basis: 100px;
            min-width: 40px;
            max-width: 200px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            line-height: 1.2;
            font-size: 0.85rem;
        }
        .table-cell:last-child {
            border-right: none;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-row:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>RTU 태양광 입력 데이터 리스트(삼상)</h1>
    
    <div class="table-container">
        <div class="table-header">
            <!-- <div class="table-hfcell">ID<BR> 고유 식별자 </div>
            <div class="table-cell">ref_6431id<BR> 참조 ID </div> -->
            <div class="table-cell">LTID </div>
            <div class="table-cell">CID </div>
            <div class="table-cell">Subscription Key<BR> 구독 키 </div>
            <div class="table-cell">Date<BR> 데이터 기록 일시 </div>
			
            <div class="table-hfcell">Phase Type<BR> 단상 또는 삼상 구분 </div>
            <div class="table-cell">Command Type<BR> 커맨드 코드 </div>
            <div class="table-cell">Energy Type<BR> 에너지원 타입 </div>
            <div class="table-cell">Multi<BR> 멀티</div>
            <div class="table-cell">Errorcode<BR> 에러 코드 </div>
            <!-- <div class="table-cell">PV Voltage<BR> PV 전압 (V) </div>
            <div class="table-cell">PV Current<BR> PV 전류 (A) </div> -->
            <div class="table-cell">PV Output<BR> PV 출력 (W) </div>
            <div class="table-cell">Out V<BR> 출력 전압 (V) </div>
            <div class="table-cell">Out A<BR> 출력 전류 (A) </div>
            <div class="table-cell">Current Output<BR> 현재 출력 (W) </div>
            <div class="table-cell">Power Factor<BR> 역률 (%) </div>
            <div class="table-cell">Frequency<BR> 주파수 (Hz) </div>
            <div class="table-cell">Cumulative Energy<BR> 누적 발전량 (Wh) </div>
            <div class="table-hfcell">Fault Status<BR> 고장 여부 (0: 정상, 1: 고장) </div>
            <div class="table-cell">System Voltage R<BR> 계통 R상 전압 (V) </div>
            <div class="table-cell">System Voltage S<BR> 계통 S상 전압 (V) (삼상일 경우) </div>
            <div class="table-cell">System Voltage T<BR> 계통 T상 전압 (V) (삼상일 경우) </div>
            <div class="table-cell">System Current R<BR> 계통 R상 전류 (A) </div>
            <div class="table-cell">System Current S<BR> 계통 S상 전류 (A) (삼상일 경우) </div>
            <div class="table-cell">System Current T<BR> 계통 T상 전류 (A) (삼상일 경우) </div>
        </div>
        
        <!-- Data Rows -->
        <?php if (!empty($rows) && is_array($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <div class="table-row" style="background:#ffff<?echo substr($row['ltid'],-2)?>">
                    <div class="table-cell"><?php echo htmlspecialchars($row['ltid']); ?></div> <!-- LTID -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['cid']); ?></div> <!-- cid -->				
                    <div class="table-cell"><?php echo htmlspecialchars($row['subscription_key']); ?></div> <!-- 구독 키 -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['rdate']); ?></div> <!-- 데이터 기록 일시 -->
					
                    <!-- <div class="table-hfcell"><?php echo htmlspecialchars($row['id']); ?></div> --> <!-- 고유 식별자 -->
                    <!-- <div class="table-cell"><?php echo htmlspecialchars($row['ref_6431id']); ?></div> --> <!-- 참조 ID -->
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['phase_type']); ?></div> <!-- 단상(single) 또는 삼상(three) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['command_type']); ?></div> <!-- 커맨드 코드 -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['energy_type']); ?></div> <!-- 에너지원 타입 (예: 0101) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['multi']); ?></div> <!-- 멀티 -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['errorcode']); ?></div> <!-- 에러 코드 -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['pv_voltage']); ?></div> <!-- PV 전압 (V) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['pv_current']); ?></div> <!-- PV 전류 (A) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['pv_output']); ?></div> <!-- PV 출력 (W) -->
                   <!--  <div class="table-cell"><?php echo htmlspecialchars($row['out_v']); ?></div> 출력 전압 (V) -->
                   <!--  <div class="table-cell"><?php echo htmlspecialchars($row['out_a']); ?></div>  출력 전류 (A) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['current_output']); ?></div> <!-- 현재 출력 (W) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['power_factor']); ?></div> <!-- 역률 (%) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['frequency']); ?></div> <!-- 주파수 (Hz) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['cumulative_energy']); ?></div> <!-- 누적 발전량 (Wh) -->
                    <div class="table-hfcell"><?php echo htmlspecialchars($row['fault_status']); ?></div> <!-- 고장 여부 (0: 정상, 1: 고장) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_voltage_r']); ?></div> <!-- 계통R상 전압 (V) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_voltage_s']); ?></div> <!-- 계통S상 전압 (V) (삼상일 경우) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_voltage_t']); ?></div> <!-- 계통T상 전압 (V) (삼상일 경우) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_current_r']); ?></div> <!-- 계통R상 전류 (A) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_current_s']); ?></div> <!-- 계통S상 전류 (A) (삼상일 경우) -->
                    <div class="table-cell"><?php echo htmlspecialchars($row['system_current_t']); ?></div> <!-- 계통T상 전류 (A) (삼상일 경우) -->

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="table-row">
                <div class="table-cell" colspan="26">데이터가 없습니다.</div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
