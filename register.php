<?php
session_start();

// 处理用户提交的注册表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户输入的数据
    $account = $_POST['account'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $join_date = date("Y-m-d"); // 获取当前日期

    // 在此处添加对用户输入的验证和安全性措施

    // 连接到数据库
    $conn = new mysqli("localhost", "root", "", "pet_shop");
    if ($conn->connect_error) {
        die("資料庫連接失敗 " . $conn->connect_error);
    }

    // 检查账号是否已经存在
    $checkAccount = "SELECT * FROM users WHERE c_account = '$account'";
    $result = $conn->query($checkAccount);
    if ($result->num_rows > 0) {
        echo '<script>alert("帳號已存在");setTimeout(function() {window.location.href = "index.php";}, 1000);</script>';
        $conn->close();
        exit();
    }

    // 插入用户信息到数据库表
    $sql = "INSERT INTO users (c_account, c_password, c_name, c_phone, c_email, c_address, c_join_date) VALUES ('$account', '$password', '$name', '$phone_number', '$email', '$address', '$join_date')";
    if ($conn->query($sql) === TRUE) {
        // 註冊成功後的跳轉
        echo '<script>alert("註冊成功");setTimeout(function() {window.location.href = "login.php";}, 1000);</script>';
    } else {
        echo '<script>alert("註冊失敗");setTimeout(function() {window.location.href = "index.php";}, 1000);</script>' . $conn->error;
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>用戶註冊</title>
    <style>
        body {
            background-image: url("br_register.png");
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background-color: rgba(255, 255, 255, 0.7);
            padding: 20px;
            text-align: center;
            border-radius: 10px;
        }

        .register-box h1 {
            margin-bottom: 20px;
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
</head>
<body>
<div class="topnav">
    <a href="index.php">首頁</a>
    <a href="all_product.php">商品</a>
    <a href="cart.php">購物車</a>
    <a href="login.php">登入/</a>
    <a href="logout.php">登出</a>
    </div>
    <div class="container">

        <div class="register-box">
            <h1>用戶註冊</h1>

            <form method="POST" action="">
                <label for="account">帳號:</label>
                <input type="text" id="account" name="account" required><br>

                <label for="password">密碼:</label>
                <input type="password" id="password" name="password" required><br>

                <label for="name">姓名:</label>
                <input type="text" id="name" name="name" required><br>

                <label for="phone_number">電話號碼:</label>
<input type="text" id="phone_number" name="phone_number" required><br>
                <label for="email">電子郵件:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="address">地址:</label>
            <input type="text" id="address" name="address" required><br>

            <button type="submit">註冊</button>
        </form>
    </div>
</div>

