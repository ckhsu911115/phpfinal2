<!DOCTYPE html>
<html>
<head>
  <title>購物網站</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  <header>
    <h1>歡迎光臨購物網站</h1>
    <?php if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true): ?>
      <form method="POST" action="logout.php">
      </form>
    <?php endif; ?>
  </header>
  
  <div class="container">
    <?php include 'browse_products.php'; ?>
  </div>

  <footer>
  <form action="logout.php" method="post">
  <button type="submit">登出</button>
</form>
    <p>版權所有 &copy; 2023 購物網站</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>
