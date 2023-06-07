<?php
session_start();

// 连接到数据库
$conn = new mysqli("localhost", "root", "", "pet_shop");

// 检查连接是否成功
if ($conn->connect_error) {
  die("資料庫連接失敗: " . $conn->connect_error);
}

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // 对输入的用户名和密码进行转义，防止 SQL 注入
  $username = $conn->real_escape_string($username);
  $password = $conn->real_escape_string($password);

  // 查询数据库中是否存在匹配的记录
  $sql = "SELECT * FROM users WHERE c_account = '$username' AND c_password = '$password'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // 登录成功，将用户信息存储在会话中
    $_SESSION['username'] = $username;
    $_SESSION['loggedIn'] = true;

    // 重定向到所有商品页面
    header("Location: all_product.php");
    exit();
  } else {
    // 登录失败，显示错误消息
    echo "帳號或密碼錯誤";
  }
}

// 检查用户是否已登录
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
  // 用户已登录，重定向到所有商品页面
  header("Location: all_product.php");
  exit();
}

// 关闭数据库连接

?>

<!DOCTYPE html>
<html>
<head>
<style>
    body {
      background-image: url('br_index.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container form {
      background-color: rgba(255, 255, 255, 0.5);
      padding: 20px;
      border-radius: 10px;
    }

    .container label {
      display: block;
      margin-bottom: 10px;
      font-size: 18px;
    }

    .container input {
      width: 100%;
      padding: 5px;
      font-size: 16px;
      margin-bottom: 20px;
    }

    .container button {
      margin: 10px;
      padding: 10px 20px;
      font-size: 18px;
    }

    .topnav {
      position: absolute;
      top: 10px;
      right: 10px;
    }

    .topnav a {
      margin-right: 100px;
      color: white;
      text-decoration: none;
      font-size: 35px;
    }
  </style>
  <title>登入</title>
</head>
<body>
  <div class="topnav">
    <a href="index.php">首頁</a>
    <a href="all_product.php">商品</a>
    <a href="cart.php">購物車</a>
    <a href="login.php">登入</a>
    <a href="logout.php">登出</a>
  </div>

  <div class="container">
    <form method="POST" action="login.php">
      <label for="username">帳號:</label>
      <input type="text" name="username" id="username" required>
      <label for="password">密碼:</label>
      <input type="password" name="password" id="password" required>
      <button type="submit">登入</button>
    </form>
    <form action="register.php">
      <button type="submit">註冊</button>
    </form>
  </div>
</body>
</html>
