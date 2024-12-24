<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>자동 날씨 조회</title>
    <script>
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // 위치 정보를 PHP로 전송
                        fetch('weather.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ latitude, longitude }),
                        })
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('result').innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('result').innerHTML = "오류 발생: " + error.message;
                        });
                    },
                    (error) => {
                        document.getElementById('result').innerHTML = `위치 정보를 가져오는 데 실패했습니다: ${error.message}`;
                    }
                );
            } else {
                document.getElementById('result').innerHTML = "Geolocation은 이 브라우저에서 지원되지 않습니다.";
            }
        };
    </script>
</head>
<body>
    <h1>지역 날씨 자동 조회</h1>
    <div id="result">
        <p>날씨 정보를 확인하려면 위치 권한을 허용하세요.</p>
    </div>
</body>
</html>
