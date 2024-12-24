<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>간단한 지도 표시하기</title>
    <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=fl077w0ucc&callback=CALLBACK_FUNCTION"></script>
	<script type="text/javascript" src="MarkerClustering.js"></script>	
</head>
<body>
	<div id="map" style="width:100%;height:100%;padding:0;margin:0;"></div>
	<script>
		var map = new naver.maps.Map("map", {
	        zoom: 6,
	        center: new naver.maps.LatLng(36.2253017, 127.6460516)
	    });

	    var markers = data;	// Array

	    var htmlMarker1 = { content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(/example/images/cluster-marker-1.png);background-size:contain;"></div>', size: N.Size(40, 40), anchor: N.Point(20, 20) },
	        htmlMarker2 = { content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(/example/images/cluster-marker-2.png);background-size:contain;"></div>', size: N.Size(40, 40), anchor: N.Point(20, 20) },
	        htmlMarker3 = { content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(/example/images/cluster-marker-3.png);background-size:contain;"></div>', size: N.Size(40, 40),anchor: N.Point(20, 20) },
	        htmlMarker4 = { content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(/example/images/cluster-marker-4.png);background-size:contain;"></div>', size: N.Size(40, 40), anchor: N.Point(20, 20) },
	        htmlMarker5 = { content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url(/example/images/cluster-marker-5.png);background-size:contain;"></div>',size: N.Size(40, 40), anchor: N.Point(20, 20) };

	    var markerClustering = new MarkerClustering({
	        minClusterSize: 2,
	        maxZoom: 13,
	        map: map,
	        markers: markers,
	        disableClickZoom: false,
	        gridSize: 120,
	        icons: [htmlMarker1, htmlMarker2, htmlMarker3, htmlMarker4, htmlMarker5],
	        indexGenerator: [10, 100, 200, 500, 1000],
	        stylingFunction: function(clusterMarker, count) {
	            $(clusterMarker.getElement()).find('div:first-child').text(count);
	        }
	    });
	</script>
</body>
</html>
