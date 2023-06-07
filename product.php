<!DOCTYPE html>
<html>
<head>
  <title>添加新商品</title>
</head>
<body>
  <h2>添加新商品</h2>
  <form action="add_product.php" method="post" enctype="multipart/form-data">
    <label for="pp_SKU">商品編號：</label>
    <input type="text" id="pp_SKU" name="pp_SKU" required>
    <br>
    <label for="pp_name">商品名稱：</label>
    <input type="text" id="pp_name" name="pp_name" required>
    <br>
    <label for="pp_price">商品售價：</label>
    <input type="number" id="pp_price" name="pp_price" step="1" required>
    <br>
    <label for="pp_description">商品介紹：</label>
    <textarea id="pp_description" name="pp_description" required></textarea>
    <br>
    <label for="pp_stock">商品庫存：</label>
    <input type="number" id="pp_stock" name="pp_stock" required>
    <br>
    <label for="pp_cost">商品成本：</label>
    <input type="number" id="pp_cost" name="pp_cost" step="1" required>
    <br>
    <label for="pp_manufacturer">商品廠商：</label>
    <input type="text" id="pp_manufacturer" name="pp_manufacturer" required>
    <br>
    <label for="pp_image">商品圖片：</label>
    <input type="file" id="pp_image" name="pp_image" required>
    <br>
    <input type="submit" value="添加商品">
  </form>
</body>
</html>
