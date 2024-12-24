<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP 히트맵 예제</title>
    <script src="https://cdn.jsdelivr.net/npm/heatmap.js/build/heatmap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/heatmap.js/plugins/jquery.js"></script>
</head>
<body>
    <div id="heatmapContainer" style="width: 500px; height: 500px;"></div>

    <?php
    // PHP 코드로 데이터 생성
    // 예제로 랜덤한 1000개의 데이터를 생성합니다.
    $data = array();
    for ($i = 0; $i < 1000; $i++) {
        $data[] = array(rand(0, 499), rand(0, 499), rand(1, 10)); // X 좌표, Y 좌표, 값
    }
    ?>

    <script>
        // PHP에서 생성한 데이터를 JavaScript로 가져옴
        var data = <?php echo json_encode($data); ?>;
        
        // heatmap.js를 사용하여 히트맵 그리기
        var heatmapInstance = h337.create({
            container: document.getElementById('heatmapContainer'),
            radius: 20
        });

        // 데이터를 heatmap.js 형식에 맞게 변환
        var heatmapData = {
            max: 10,
            data: []
        };

        for (var i = 0; i < data.length; i++) {
            var point = {
                x: data[i][0],
                y: data[i][1],
                value: data[i][2]
            };
            heatmapData.data.push(point);
        }

        // 히트맵에 데이터 설정
        heatmapInstance.setData(heatmapData);
    </script>
</body>
</html>
