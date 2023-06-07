<?php
session_start();

// 連接到資料庫
$conn = new mysqli("localhost", "root", "", "pet_shop");

// 檢查連接是否成功
if ($conn->connect_error) {
  die("連接資料庫失敗: " . $conn->connect_error);
}

// 檢查使用者是否已登入
if (!isset($_SESSION['username'])) {
  echo "使用者未登入";
  exit;
}

// 檢查使用者是否為管理者


// 查詢所有訂單
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo "<h2>訂單列表:</h2>";
  echo "<table>";
  echo "<tr>";
  echo "<th>訂單編號</th>";
  echo "<th>使用者ID</th>";
  echo "<th>收件人姓名</th>";
  echo "<th>聯絡電話</th>";
  echo "<th>取貨門市</th>";
  echo "<th>付款方式</th>";
  echo "<th>備註</th>";
  echo "</tr>";

  while ($row = $result->fetch_assoc()) {
    $orderID = $row["o_id"];
    $userID = $row["user_id"];
    $name = $row["name"];
    $phone = $row["phone"];
    $store = $row["store"];
    $payment = $row["payment"];
    $notes = $row["notes"];

    echo "<tr>";
    echo "<td>$orderID</td>";
    echo "<td>$userID</td>";
    echo "<td>$name</td>";
    echo "<td>$phone</td>";
    echo "<td>$store</td>";
    echo "<td>$payment</td>";
    echo "<td>$notes</td>";
    echo "</tr>";
  }

  echo "</table>";
} else {
  echo "沒有找到訂單";
}

$conn->close();
?>
