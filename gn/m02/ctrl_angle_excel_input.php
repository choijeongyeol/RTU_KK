<!DOCTYPE html>
<html lang="ko-kr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>앵글 삽입</title>

    <!-- Bootstrap -->
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/build/css/custom.min.css" rel="stylesheet">
</head>
<body style="overflow: hidden; background: #405469;"> 

    <br />
    <center><h2><span style="font-size:18px; font-weight:bold; color:#fff;">앵글 삽입</span></h2></center>
    <div class="ln_solid"></div>        

    <!-- 엑셀 업로드 시작 -->
    <div style="width:98%; text-align:right; background-color:#cfe9da;">
        <form id="uploadForm" action="/excel/upload_m02_angle.php" method="post" enctype="multipart/form-data">
        
            <input type="hidden" name="warehouse_id" value="<?= htmlspecialchars($_GET['arg2']) ?>"><br>

            <label for="file"><u><a href="/excel/sample_excel/sample_m02_angle.xlsx">샘플다운로드</a></u> &nbsp;| &nbsp;엑셀 파일 선택:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit" class="btn btn-secondary btn-sm" style="padding:12px; margin-right:0px; margin-bottom:0px; width:60%; height:50px; font-weight:bold; background-color:#3c8259;">앵글삽입(엑셀 업로드)</button>
        </form>
    </div>              

</body>
</html>
