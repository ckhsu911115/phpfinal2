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

// 獲取使用者資訊
$username = $_SESSION['username'];

// 查詢資料庫獲取使用者ID
$sql = "SELECT c_id FROM users WHERE c_account = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $userID = $row["c_id"];

  // 查詢使用者的訂單
  $sql = "SELECT * FROM orders WHERE user_id = '$userID'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo "<h2>您的訂單列表:</h2>";
    echo "<table>";
    echo "<tr>";
    echo "<th>訂單編號</th>";
    echo "<th>收件人姓名</th>";
    echo "<th>聯絡電話</th>";
    echo "<th>取貨門市</th>";
    echo "<th>付款方式</th>";
    echo "<th>備註</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
      $orderID = $row["o_id"];
      $name = $row["name"];
      $phone = $row["phone"];
      $store = $row["store"];
      $payment = $row["payment"];
      $notes = $row["notes"];

      echo "<tr>";
      echo "<td>$orderID</td>";
      echo "<td>$name</td>";
      echo "<td>$phone</td>";
      echo "<td>$store</td>";
      echo "<td>$payment</td>";
      echo "<td>$notes</td>";
      echo "</tr>";
    }

    echo "</table>";
  } else {
    echo "沒有找到您的訂單";
  }
} else {
  echo "無法獲取使用者ID";
  exit;
}

$conn->close();
?>
