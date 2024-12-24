<!DOCTYPE html>
<html>
<head>
    <title>네이버 지도</title>
    <script src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=aiwhoshchv"></script>
</head>
<body>
    <div id="map" style="width:100%;height:500px;"></div>
    <script>
        // 네이버 지도 초기화
        var map = new naver.maps.Map('map', {
            center: new naver.maps.LatLng(37.5665, 126.9780), // 서울시청 좌표
            zoom: 13
        });

        // PHP에서 데이터 가져오기
        fetch('get_locations.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(location => {
                    // 지도에 마커 추가
                    var marker = new naver.maps.Marker({
                        position: new naver.maps.LatLng(location.latitude, location.longitude),
                        map: map,
                        title: location.name
                    });

                    // 마커 클릭 이벤트
                    naver.maps.Event.addListener(marker, 'click', function() {
                        alert(location.name + " 상태: " + location.status);
                    });
                });
            })
            .catch(error => console.error('Error:', error));
    </script>
</body>
</html>
