<?php
// 连接到数据库
$conn = new mysqli("localhost", "root", "", "pet_shop");

if ($conn->connect_error) {
  die("數據庫連接失敗: " . $conn->connect_error);
}

$pp_SKU = $_POST['pp_SKU'];
$pp_name = $_POST['pp_name'];
$pp_price = $_POST['pp_price'];
$pp_description = $_POST['pp_description'];
$pp_stock = $_POST['pp_stock'];
$pp_cost = $_POST['pp_cost'];
$pp_manufacturer = $_POST['pp_manufacturer'];

$pp_SKU = $conn->real_escape_string($pp_SKU);
$pp_name = $conn->real_escape_string($pp_name);
$pp_description = $conn->real_escape_string($pp_description);
$pp_manufacturer = $conn->real_escape_string($pp_manufacturer);

$targetDir = "images/";
$targetFile = $targetDir . basename($_FILES["pp_image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

$check = getimagesize($_FILES["pp_image"]["tmp_name"]);
if ($check === false) {
  $uploadOk = 0;
}

if (file_exists($targetFile)) {
  $uploadOk = 0;
}

if ($_FILES["pp_image"]["size"] > 500000) {
  $uploadOk = 0;
}

if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
  $uploadOk = 0;
}

if ($uploadOk == 1) {
  if (move_uploaded_file($_FILES["pp_image"]["tmp_name"], $targetFile)) {
    $image = basename($_FILES["pp_image"]["name"]);

    $sql = "INSERT INTO products (pp_SKU, pp_name, pp_price, pp_description, pp_stock, pp_cost, pp_manufacturer, pp_image) 
            VALUES ('$pp_SKU', '$pp_name', '$pp_price', '$pp_description', '$pp_stock', '$pp_cost', '$pp_manufacturer', '$image')";

    if ($conn->query($sql) === TRUE) {
      echo "商品添加成功";
    } else {
      echo "添加商品發生錯誤: " . $conn->error;
    }
  } else {
    echo "上傳圖像發生錯誤";
  }
} else {
  echo "無法上傳圖像";
}

$conn->close();
?>
