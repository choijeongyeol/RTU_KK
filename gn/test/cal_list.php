<?php
// 데이터베이스 연결 정보
$servername = "localhost";
$username = "devbine";
$password = "Hanis123!";
$dbname = "devbine";
try {
    // 데이터베이스 연결
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // PDO 예외 처리 설정
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
 
?>
<h2>일정 추가</h2>
<form action="cal_list.php" method="post">
    Title: <input type="text" name="title"><br>
    Date: <input type="date" name="event_date"><br>
    Description: <textarea name="description"></textarea><br>
    <input type="submit" value="Add Event">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $event_date = $_POST["event_date"];
    $description = $_POST["description"];

    try {
        $stmt = $conn->prepare("INSERT INTO events (title, event_date, description) VALUES (:title, :event_date, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        echo "일정이 추가되었습니다.";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
 <?

try {
    $stmt = $conn->query("SELECT * FROM events");
    if ($stmt->rowCount() > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Title</th><th>Date</th><th>Description</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["title"] . "</td>";
            echo "<td>" . $row["event_date"] . "</td>";
            echo "<td>" . $row["description"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "일정이 없습니다.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
} 
 
 ?>