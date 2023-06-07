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

  // 檢查購物車是否存在，如果不存在則建立一個空陣列
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  // 處理新增到購物車的請求
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 獲取提交的商品SKU和數量
    $SKU = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // 將商品SKU及數量新增到使用者的購物車
    $_SESSION['cart'][$userID][$SKU] = $quantity;

    echo "商品已新增到購物車";

    // 更新使用者資料，將購物車資料儲存到資料庫
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

      // 更新使用者資料，將購物車資料儲存到資料庫
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

?>

<!DOCTYPE html>
<html>
</title>
</head>
<body>
  <h1>下單頁面</h1>

  <form method="POST" action="checkout.php">
    <label for="name">姓名:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="phone">電話:</label>
    <input type="text" id="phone" name="phone" required><br>

    <label for="store">7-11店家:</label>
    <input type="text" id="store" name="store" required><br>

    <label for="payment">付款方式:</label>
    <select id="payment" name="payment" required>
      <option value="cash_on_delivery">貨到付款</option>
      <option value="line_pay">LINE PAY</option>
    </select><br>

    <label for="notes">備註:</label><br>
    <textarea id="notes" name="notes" rows="4" cols="50"></textarea><br>

    <h2>訂單明細:</h2>
    <?php
    // 檢查購物車是否為空
    if (!empty($cartData)) {
      // 遍歷購物車中的商品SKU並顯示詳細資訊
      foreach ($cartData as $SKU => $quantity) {
        // 查詢商品資料，根據實際情況從資料庫或其他資料來源獲取商品資訊
        $sql = "SELECT * FROM products WHERE pp_SKU = '$SKU'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $product_name = $row["pp_name"];
          $price = $row["pp_price"];

          // 顯示購物車中商品的資訊
          echo "<tr>";
          echo "<td>{$product_name}</td>";
          echo "<td>{$price}</td>";
          echo "<td>{$quantity}</td>";  // 顯示商品數量
          echo "<td><a href='cart.php?remove={$SKU}'>刪除</a></td>";
          echo "</tr>";
        }
      }
    } else {
      echo "<tr><td colspan='4'>購物車為空</td></tr>";
    }
    ?>
    <br>
    <button type="submit" formaction="check_done.php">提交訂單</button>
  </form>
</body>
</html>
