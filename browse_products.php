<!DOCTYPE html>
<html>
<head>
  <title>購物網站</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
  <style>
    body {
      background-color: #FFC0CB;
      font-family: Arial, sans-serif;
      margin: 0;
    }
.container {
display: flex;
flex-wrap: wrap;
justify-content: center;
max-width: 1500px;
margin: 0 auto;
padding: 20px;
}

.product {
background-color: rgba(255, 255, 255, 0.9);
border-radius: 5px;
padding: 20px;
width: 23%; /* 每行显示4个商品 */
margin: 10px;
text-align: center;
}

.product img {
width: 200px;
height: 200px;
object-fit: cover;
border-radius: 5px;
}

.product h2 {
margin-top: 10px;
font-size: 18px;
}

.product p {
margin-top: 5px;
font-size: 14px;
}

.product form {
margin-top: 10px;
}

.product input {
width: 50px;
}

.product button {
margin-top: 5px;
}
</style>

</head>
<body>
  <div class="container">
    <?php
    // 连接到数据库
    $conn = new mysqli("localhost", "root", "", "pet_shop");
// 检查连接是否成功
if ($conn->connect_error) {
  die("连接数据库失败: " . $conn->connect_error);
}
// 查询所有商品数据
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// 检查是否有结果
if ($result->num_rows > 0) {
// 输出每个商品的信息
while ($row = $result->fetch_assoc()) {
echo "<div class='product'>";
echo "<a href='product_details.php?id=" . $row["pp_SKU"] . "'>";
echo "<img src='images/" . $row["pp_image"] . "' alt='商品圖片'>";
echo "<h2>" . $row["pp_name"] . "</h2>";
echo "<p>售價: $" . $row["pp_price"] . "</p>";
// 添加加入购物车的表单
echo "<form method='POST' action='cart.php'>";
echo "<input type='hidden' name='product_id' value='" . $row["pp_SKU"] . "'>";
echo "<input type='number' name='quantity' min='1' value='1'>";
echo "<button type='submit'>加入購物車</button>";
echo "</form>";

echo "</a>";
echo "</div>";
}
} else {
echo "<p>暫無商品</p>";
}

// 关闭数据库连接
$conn->close();
?>

  </div>
</body>
</html>