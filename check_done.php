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

  // 檢查購物車是否存在，如果不存在則創建一個空陣列
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  // 處理添加到購物車的請求
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 獲取提交的商品SKU和數量
    // 將商品SKU及數量添加到使用者的購物車
    // 更新使用者資料，將購物車資料保存到資料庫
    $cartData = serialize($_SESSION['cart'][$userID]);
    $sql = "UPDATE users SET c_cart = '$cartData' WHERE c_id = '$userID'";
    $conn->query($sql);
  }

  // 處理從購物車中刪除商品的請求
  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
    $SKUToRemove = $_GET['remove'];

    // 檢查購物車中是否存在要刪除的商品SKU
    if (isset($_SESSION['cart'][$userID][$SKUToRemove])) {
      // 從購物車中移除指定的商品SKU
      unset($_SESSION['cart'][$userID][$SKUToRemove]);

      echo "商品已從購物車中移除";

      // 更新使用者資料，將購物車資料保存到資料庫
      $cartData = serialize($_SESSION['cart'][$userID]);
      $sql = "UPDATE users SET c_cart = '$cartData' WHERE c_id = '$userID'";
      $conn->query($sql);
    } else {
      echo "購物車中不存在該商品";
    }
  }

  // 查詢使用者購物車中的商品資料
  $cartData = $_SESSION['cart'][$userID];

} else {
  echo "無法獲取使用者ID";
  exit;
}

// 生成隨機訂單ID
function generateOrderID() {
  $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $orderID = 'ORD' . mt_rand(1000, 9999);
  return $orderID;
}

// 將訂單明細資料插入到order_detail表格
function insertOrderDetail($orderID, $userID, $SKU, $quantity, $conn) {
  // 檢查 SKU 值是否為空
  if (empty($SKU)) {
    echo "商品的 SKU 值為空";
    return;
  }

  $stmt = $conn->prepare("INSERT INTO order_detail (o_id, user_id, SKU, quantity) VALUES (?, ?, ?, ?)");
  if (!$stmt) {
    die("準備敘述失敗: " . $conn->error);
  }
  $stmt->bind_param("sssi", $orderID, $userID, $SKU, $quantity);
  if (!$stmt->execute()) {
    die("執行敘述失敗: " . $stmt->error);
  }
  $stmt->close();
}

// 處理提交訂單的請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 獲取訂單資訊
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $store = $_POST['store'];
  $payment = $_POST['payment'];
  $notes = $_POST['notes'];

  // 生成訂單ID
  $orderID = generateOrderID();

  // 將訂單資訊插入到 order 表格
  $stmt = $conn->prepare("INSERT INTO `orders` (o_id, user_id, name, phone, store, payment, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
  if (!$stmt) {
    die("準備敘述失敗: " . $conn->error);
  }
  $stmt->bind_param("sssssss", $orderID, $userID, $name, $phone, $store, $payment, $notes);
  if (!$stmt->execute()) {
    die("執行敘述失敗: " . $stmt->error);
  }
  $stmt->close();

  // 遍歷購物車中的商品 SKU 並插入訂單明細
  foreach ($cartData as $SKU => $quantity) {
    insertOrderDetail($orderID, $userID, $SKU, $quantity, $conn);
  }

  // 清空購物車
  $_SESSION['cart'][$userID] = [];

  echo "訂單已提交成功";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>訂單完成</title>
</head>
<body>
  <h2>訂單明細:</h2>
  <table>
    <tr>
      <th>商品名稱</th>
      <th>價格</th>
      <th>數量</th>
      <th>操作</th>
    </tr>
    <?php
    // 檢查購物車是否為空
    if (!empty($cartData)) {
      // 遍歷購物車中的商品 SKU 並顯示詳細資訊
      foreach ($cartData as $SKU => $quantity) {
       
        // 查詢商品資料，根據實際情況從資料庫或其他資料源獲取商品資訊
        $sql = "SELECT * FROM products WHERE pp_SKU = '$SKU'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $product_name = $row["pp_name"];
          $price = $row["pp_price"];

          // 顯示購物車中商品的資訊
          echo "<tr>";
          echo "<td>$product_name</td>";
          echo "<td>$price</td>";
          echo "<td>$quantity</td>";
          echo "<td><a href=\"?remove=$SKU\">移除</a></td>";
          echo "</tr>";
        }
      }
    } else {
      echo "<tr><td colspan='4'>購物車為空或二次交易失敗</td></tr>";
    }
    ?>
  </table>

  <?php
  // 查詢購物車中的商品詳細資訊並顯示
  foreach ($cartData as $SKU => $quantity) {
    $sql = "SELECT pp_name, pp_price, pp_stock FROM products WHERE pp_SKU = '$SKU'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $productName = $row["pp_name"];
      $price = $row["pp_price"];
      $stock = $row["pp_stock"];

      echo "<h2>$productName</h2>";
      echo "<p>價格: $price</p>";
      echo "<p>數量: $quantity</p>";
    } else {
      echo "找不到商品: $SKU";
    }
  }
  ?>
</body>
</html>
