<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/gu/inc/db_connection.php');

// DB 연결
$conn = getDbConnection();
if (!$conn) {
    echo "Database connection failed";
    exit();
}

// 트랜잭션 시작
$conn->beginTransaction();

try {
    // 변수 선언
    $ty = 4;
    $ri = "CI00000000057327847779";
    $rn = "CI00000000057327847779";
    $pi = "CT00000000000001288261";
    $ct = date("Y-m-d H:i:s");
    $lt = date("Y-m-d H:i:s");
    $gwl = "36.877293,127.966977,0";
    $geui = "0017b2fffe09e3b8";
    $devl = "36.877293,127.966977,0";
    $fp = 2;
    $trid = "{}";
    $plidx = 0;
    $ctype = 11;
    $fixType = 8;
    $result = 0;
    $accuracy = 3000;
    $sr = "/0060231000001132/v1_0/remoteCSE-00001132d02544fffef3b7bf/container-LoRa/subscription-kk_3";
    $et = date("Y-m-d H:i:s");
    $st = 2178;
    $cr = "RC00000000000001356551";
    $cnf = "LoRa/Sensor";
    $cs = 10;
    $con = "140101000000e2000a093d00e7000a08e203e802570000000000045dcf0000";
    $containerCurrentByteSize = 79292;
    $ltid = "00001132d02544fffef3b7bf";

    // SQL 준비
    $stmt = $conn->prepare("INSERT INTO RTU_6431 (ty, ri, rn, pi, ct, lt, gwl, geui, devl, fp, trid, plidx, ctype, fixType, result, accuracy, sr, et, st, cr, cnf, cs, con, containerCurrentByteSize, ltid)
                            VALUES (:ty, :ri, :rn, :pi, :ct, :lt, :gwl, :geui, :devl, :fp, :trid, :plidx, :ctype, :fixType, :result, :accuracy, :sr, :et, :st, :cr, :cnf, :cs, :con, :containerCurrentByteSize, :ltid)");

    // 변수 바인딩
    $stmt->bindParam(':ty', $ty);
    $stmt->bindParam(':ri', $ri);
    $stmt->bindParam(':rn', $rn);
    $stmt->bindParam(':pi', $pi);
    $stmt->bindParam(':ct', $ct);
    $stmt->bindParam(':lt', $lt);
    $stmt->bindParam(':gwl', $gwl);
    $stmt->bindParam(':geui', $geui);
    $stmt->bindParam(':devl', $devl);
    $stmt->bindParam(':fp', $fp);
    $stmt->bindParam(':trid', $trid);
    $stmt->bindParam(':plidx', $plidx);
    $stmt->bindParam(':ctype', $ctype);
    $stmt->bindParam(':fixType', $fixType);
    $stmt->bindParam(':result', $result);
    $stmt->bindParam(':accuracy', $accuracy);
    $stmt->bindParam(':sr', $sr);
    $stmt->bindParam(':et', $et);
    $stmt->bindParam(':st', $st);
    $stmt->bindParam(':cr', $cr);
    $stmt->bindParam(':cnf', $cnf);
    $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':con', $con);
    $stmt->bindParam(':containerCurrentByteSize', $containerCurrentByteSize);
    $stmt->bindParam(':ltid', $ltid);

    // SQL 실행 및 결과 출력
    if ($stmt->execute()) {
        echo "Data inserted successfully";
    } else {
        $error_info = $stmt->errorInfo();
        echo "Insert query failed. SQLSTATE: " . $error_info[0] . ", Error Code: " . $error_info[1] . ", Message: " . $error_info[2];
    }

    // 트랜잭션 커밋
    $conn->commit();

} catch (PDOException $e) {
    // 트랜잭션 롤백
    $conn->rollBack();
    echo "Transaction failed: " . $e->getMessage();
}
?>
