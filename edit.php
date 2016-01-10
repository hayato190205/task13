<?php

require_once('functions.php');
require_once('config.php');

$id = $_GET['id'];

$dbh = connectDatabase();
$sql = "select * from image where id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->execute();

$row = $stmt->fetch();

if (!$row)
{
  header('Location: index.php');
  exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $title = $_POST['title'];
  $errors = array();

  if($title == '')
  {
    $errors['title'] = 'メッセージが未入力です';
  }

  if(empty($errors))
  {
    $dbh = connectDatabase();
    $sql = "update image set title = :title, updated_at = now() where id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":title", $title);
    $stmt->execute();

    header("Location: index.php");
    exit;
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>編集画面</title>
</head>
<body>
  <h1>投稿内容を編集する</h1>
  <p><a href="index.php">戻る</a>
  <form action="" method="post">
  <textarea name="title" rows="1" cols="20"><?php echo h($row['title']); ?></textarea>
    <?php if ($errors['title']) :?>
      <?php echo h($errors['title']); ?>
    <?php endif; ?>
    <input type="submit" value="編集する">
  </form>
</body>
</html>
