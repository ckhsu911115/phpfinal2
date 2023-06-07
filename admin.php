<?php
// 连接到数据库
$conn = new mysqli("localhost", "root", "", "pet_shop");

// 检查连接是否成功
if ($conn->connect_error) {
  die("連接資料庫失敗: " . $conn->connect_error);
}

// 查询数据库以获取统计数据
$sql = "SELECT COUNT(*) AS totalProducts, AVG(pp_price) AS avgPrice FROM products";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$totalProducts = $row['totalProducts'];
$avgPrice = $row['avgPrice'];

// 查询数据库以获取每个商品的点击数
$sqlClicks = "SELECT pp_name, pp_clicks FROM products";
$resultClicks = $conn->query($sqlClicks);

// 计算所有商品的点击数总和
$totalClicks = 0;
while ($rowClicks = $resultClicks->fetch_assoc()) {
  $totalClicks += $rowClicks['pp_clicks'];
}

// 重新查询每个商品的点击数
$resultClicks = $conn->query($sqlClicks);

// 查询数据库以获取新用户的注册日期数据
$sqlRegistrations = "SELECT DATE(c_join_date) AS registrationDate, COUNT(*) AS registrationCount FROM users GROUP BY DATE(c_join_date)";
$resultRegistrations = $conn->query($sqlRegistrations);

// 定义数组来存储日期和对应的注册用户数量
$dates = array();
$userCounts = array();

// 遍历查询结果并将数据存入数组
while ($rowRegistrations = $resultRegistrations->fetch_assoc()) {
  $dates[] = $rowRegistrations['registrationDate'];
  $userCounts[] = $rowRegistrations['registrationCount'];
}

// 查询数据库以获取支付方式的统计数据
$sqlPayment = "SELECT payment, COUNT(*) AS paymentCount FROM orders GROUP BY payment";
$resultPayment = $conn->query($sqlPayment);

// 定义数组来存储支付方式和对应的订单数量
$paymentMethods = array();
$paymentCounts = array();

// 遍历查询结果并将数据存入数组
if ($resultPayment->num_rows > 0) {
  while ($rowPayment = $resultPayment->fetch_assoc()) {
    $paymentMethods[] = $rowPayment['payment'];
    $paymentCounts[] = $rowPayment['paymentCount'];
  }
}

// 计算订单总数
$totalOrders = array_sum($paymentCounts);

// 关闭数据库连接
?>

<!DOCTYPE html>
<html>
<head>
  <title>管理者頁面</title>
  <!-- 引入 Chart.js 库 -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h2>管理者頁面</h2>

  <h3>統計數據</h3>
  <p>總產品數量：<?php echo $totalProducts; ?></p>
  <p>平均價格：<?php echo number_format($avgPrice, 2); ?></p>

  <h3>查詢數據</h3>

  <form method="post" action="">
  查詢產品價格大於： <input type="text" name="minPrice">
    <input type="submit" value="查詢">
  </form>

  <h3>點擊率</h3>
<?php
// 顯示每個商品的點擊率
if ($resultClicks->num_rows > 0) {
  while ($rowClicks = $resultClicks->fetch_assoc()) {
    $productName = $rowClicks['pp_name'];
    $clicks = $rowClicks['pp_clicks'];

    // 計算點擊率
    $clickRate = ($clicks / $totalClicks) * 100;

    echo "商品名稱： " . $productName . "，點擊率： " . number_format($clickRate, 2) . "%<br>";
    echo "商品名稱： " . $productName . "，點擊數： " . $clicks . "<br>";
  }
} else {
  echo "沒有商品的點擊數據。";
}
?>

<?php
// 连接到数据库
$conn = new mysqli("localhost", "root", "", "pet_shop");

// 检查连接是否成功
if ($conn->connect_error) {
  die("连接数据库失败: " . $conn->connect_error);
}

// 查询数据库以获取点击数和加入购物车数
$sqlClicksAndCarts = "SELECT pp_name, pp_clicks, pp_carts, pp_orders FROM products";
$resultClicksAndCarts = $conn->query($sqlClicksAndCarts);

if ($resultClicksAndCarts) {
  // 计算总点击数和总下单数
  $totalClicks = 0;
  $totalOrders = 0;

  // 计算总点击数和总下单数
  while ($rowClicksAndCarts = $resultClicksAndCarts->fetch_assoc()) {
    $totalClicks += $rowClicksAndCarts['pp_clicks'];
    $totalOrders += $rowClicksAndCarts['pp_orders'];
  }

  // 重新查询每个商品的点击数和下单数
  $resultClicksAndCarts = $conn->query($sqlClicksAndCarts);

  if ($resultClicksAndCarts->num_rows > 0) {
    // 输出每个商品的点击率和下单转化率
    while ($rowClicksAndCarts = $resultClicksAndCarts->fetch_assoc()) {
      $productName = $rowClicksAndCarts['pp_name'];
      $clicks = $rowClicksAndCarts['pp_clicks'];
      $orders = $rowClicksAndCarts['pp_orders'];

      // 计算点击率和下单转化率
      $clickRate = ($clicks / $totalClicks) * 100;
      $conversionRate = ($orders / $clicks) * 100;

      echo "商品名稱： " . $productName . "<br>";
      echo "點擊率： " . number_format($clickRate, 2) . "%<br>";
      echo "下單轉化率： " . number_format($conversionRate, 2) . "%<br>";
    }
  } else {
    echo "沒有商品的點擊數和下單數數據。";
  }
}

?>


<h3>支付方式比例</h3>
<div style="max-width: 500px;">
  <canvas id="paymentChart"></canvas>
</div>

<script>
  // 获取支付方式和订单数量数据
  var paymentMethods = <?php echo json_encode($paymentMethods); ?>;
  var paymentCounts = <?php echo json_encode($paymentCounts); ?>;

  // 创建饼图
  var ctx = document.getElementById('paymentChart').getContext('2d');

  var chart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: paymentMethods,
      datasets: [{
        data: paymentCounts,
        backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)'],
        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // 显示支付方式比例
  for (var i = 0; i < paymentMethods.length; i++) {
    var percentage = (paymentCounts[i] / <?php echo $totalOrders; ?>) * 100;
    console.log(paymentMethods[i] + ': ' + percentage.toFixed(2) + '%');
  }
</script>

</script>




<!DOCTYPE html>
<html>
<head>
  <title>管理者頁面</title>
  <style>
    canvas {
      max-width: 900px;
      height: auto;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h3>新註冊用戶數量</h3>

  <!-- 添加表单来接收开始日期和结束日期 -->
  <form method="POST" action="">
    <label for="startDate">開始日期:</label>
    <input type="date" id="startDate" name="startDate" required>

    <label for="endDate">結束日期:</label>
    <input type="date" id="endDate" name="endDate" required>

    <input type="submit" value="提交">
  </form>

  <?php
  // 检查是否提交了开始日期和结束日期
  if (isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // 处理日期格式
    $formattedStartDate = date('Y-m-d', strtotime($startDate));
    $formattedEndDate = date('Y-m-d', strtotime($endDate));

    // 根据开始日期和结束日期过滤查询数据
    $sqlRegistrations = "SELECT DATE(c_join_date) AS registrationDate, COUNT(*) AS registrationCount FROM users WHERE DATE(c_join_date) BETWEEN '$formattedStartDate' AND '$formattedEndDate' GROUP BY DATE(c_join_date)";
    $resultRegistrations = $conn->query($sqlRegistrations);

    // 重新获取数据
    $dates = array();
    $userCounts = array();

    // 遍历查询结果并将数据存入数组
    while ($rowRegistrations = $resultRegistrations->fetch_assoc()) {
      $date = date('Y-m-d', strtotime($rowRegistrations['registrationDate']));
      $dates[] = $date;
      $userCounts[] = $rowRegistrations['registrationCount'];
    }

    // 创建折线图
    echo "<canvas id='registrationChart'></canvas>";
    echo "<script>";
    echo "document.addEventListener('DOMContentLoaded', function() {";
    echo "var dates = " . json_encode($dates) . ";";
    echo "var userCounts = " . json_encode($userCounts) . ";";

    echo "var ctx = document.getElementById('registrationChart').getContext('2d');";
    echo "var chart = new Chart(ctx, {";
    echo "type: 'line',";
    echo "data: {";
    echo "labels: dates,";
    echo "datasets: [{";
    echo "label: '新註冊用戶人數',";
    echo "data: userCounts,";
    echo "backgroundColor: 'rgba(75, 192, 192, 0.5)',";
    echo "borderColor: 'rgba(75, 192, 192, 1)',";
    echo "borderWidth: 1";
    echo "}]";
    echo "},";
    echo "options: {";
    echo "scales: {";
    echo "y: {";
    echo "beginAtZero: true";
    echo "}";
    echo "}";
    echo "}";
    echo "});";
    echo "});";
    echo "</script>";
  }
  ?>

</body>
</html>




