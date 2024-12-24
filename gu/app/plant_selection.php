<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>발전소 선택</title>
</head>
<body>
    <h1>발전소 선택</h1>

    <label>에너지원 선택: </label>
    <select id="energy_source">
        <option value="solar">태양광</option>
        <!-- 추가 에너지원 옵션 -->
    </select>

    <label>발전소 선택: </label>
    <ul>
        <li><input type="checkbox" value="1"> 발전소 1</li>
        <li><input type="checkbox" value="2"> 발전소 2</li>
        <!-- 필요시 발전소 추가 -->
    </ul>

    <button onclick="location.href='statistics_main.php'">통계 보기</button>
</body>
</html>
