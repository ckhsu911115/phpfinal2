<?php
session_start();

$conn = new mysqli("localhost", "root", "", "pet_shop");

if ($conn->connect_error) {
  die("连接数据库失败: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
  echo "用户未登录";
  exit;
}

$username = $_SESSION['username'];

$sql = "SELECT c_id FROM users WHERE c_account = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $userID = $row["c_id"];

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $SKU = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $_SESSION['cart'][$userID][$SKU] = $quantity;

    echo "商品已添加到购物车";

    $cartData = serialize($_SESSION['cart'][$userID]);
    $sql = "UPDATE users SET c_cart = '$cartData' WHERE c_id = '$userID'";
    $conn->query($sql);
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
    $SKUToRemove = $_GET['remove'];

    if (isset($_SESSION['cart'][$userID][$SKUToRemove])) {
      unset($_SESSION['cart'][$userID][$SKUToRemove]);

      echo "商品已从购物车中移除";

      $cartData = serialize($_SESSION['cart'][$userID]);
      $sql = "UPDATE users SET c_cart = '$cartData' WHERE c_id = '$userID'";
      $conn->query($sql);
    } else {
      echo "购物车中不存在该商品";
    }
  }

  $cartData = $_SESSION['cart'][$userID];
} else {
  echo "无法获取用户ID";
  exit;
}

// 关闭数据库连接

?>
<!DOCTYPE html>
<html>
<head>
  <title>購物車</title>
  <meta charset="utf-8">
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #f5f5f5;
      font-weight: bold;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f5f5f5;
    }
    a {
      text-decoration: none;
      color: #333;
    }
    .empty-cart {
      text-align: center;
      color: #666;
    }
    .checkout-btn {
      display: block;
      width: 100%;
      max-width: 200px;
      margin: 0 auto;
      padding: 10px;
      text-align: center;
      background-color: #4caf50;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
    }
    .product-image {
      max-width: 100px;
    }
  </style>
</head>
<body>
  <h1>購物車</h1>
  <table>
    <thead>
      <tr>
        <th>產品圖片</th>
        <th>產品名稱</th>
        <th>價格</th>
        <th>數量</th>
        <th>總價</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php
        // 檢查購物車是否為空
        if (!empty($cartData)) {
          // 遍歷購物車中的商品SKU並顯示詳細信息
          foreach ($cartData as $SKU => $quantity) {
            // 查詢商品數據，根據實際情況從數據庫或其他數據源獲取商品信息
            $sql = "SELECT * FROM products WHERE pp_SKU = '$SKU'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $product_name = $row["pp_name"];
              $price = $row["pp_price"];
              $image = $row["pp_image"];  // 商品圖片的URL

              // 顯示購物車中商品的信息
              echo "<tr>";
              echo "<td><img class='product-image' src='images/{$image}' alt='Product Image'></td>";
              echo "<td>{$product_name}</td>";
              echo "<td>{$price}</td>";
              echo "<td>{$quantity}</td>"; // 顯示商品數量
              echo "<td>總價：" . ($price * $quantity) . "</td>";
              echo "<td><a href='cart.php?remove={$SKU}'>刪除</a></td>";
              echo "</tr>";
            }
          }
        } else {
          echo "<tr><td colspan='5' class='empty-cart'>購物車為空</td></tr>";
        }
    ?>
    </tbody>
  </table>
  <a href='checkout.php' class='checkout-btn'>下單</a>
</body>
</html>

