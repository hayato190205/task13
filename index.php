<?php
require_once('functions.php');
require_once('config.php');

session_start();

if (empty($_SESSION['id']))
{
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $name = $_SESSION['name'];
  $title = $_POST['title'];
  $img_name = $_FILES['img']['name'];
  $errors = array();

  if ($title == '')
  {
    $errors['title'] = 'メッセージが未入力です';
  }

  if (empty($errors))
  {
    if(isset($_FILES['img']) && is_uploaded_file($_FILES['img']['tmp_name']))
    {
      $uploadfile="./pic/".$_FILES['img']['name'];
      if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadfile))
      {
        echo $_FILES['img']['name']."のアップロード完了";
      }
      else
      {
        echo $_FILES['img']['name']."のアップロード失敗";
      }
    }
  else
  {
    echo "ファイル未選択";
  }

    $dbh = connectDatabase();
    $sql = "insert into image (name, title, file_name, created_at, updated_at) values (:name, :title, :file_name, now(), now())";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":file_name", $img_name);
    $stmt->execute();

    header('Location: index.php');
    exit;
  }
}

$dbh = connectDatabase();
$sql = "select * from image order by updated_at desc";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($posts);

?>

<!DOCTYPEhtml>
  <html>
  <head>
    <meta charset="utf-8">
    <title>会員制掲示板</title>
  </head>
  <body>
  <h1><?php echo h($_SESSION['name']); ?>さん 会員制掲示板へようこそ!</h1>
  <a href="logout.php">ログアウト</a>
  <p>画像・コメントをどうぞ!</p>

  <form enctype="multipart/form-data" action="index.php" method="post">
    画像タイトル<input type="text" name="title"><br>
    <?php if ($errors['title']) : ?>
      <?php echo h($errors['title']); ?><br>
    <?php endif; ?>
    画像ファイル(PNG, JPGのみ対応)<input type="file" name="img"><br>
    <input type="submit" value="投稿する">
  </form>
  <hr>
  <h1>投稿されたメッセージ</h1>

  <?php if (count($posts)) : ?>
  <?php foreach ($posts as $post) : ?>
    [#<?php echo h($post['id']); ?>]<br>
    「<?php echo h($post['title']); ?>」
    @<?php echo h($post['name']); ?>
    <?php echo h($post['updated_at']); ?><br>
    <img src = "pic/<?php echo h($post['file_name']); ?>" height = "150"><br>
    <a href="edit.php?id=<?php echo h($post['id']); ?>">[編集]</a>
    <a href="delete.php?id=<?php echo h($post['id']); ?>">[削除]</a><br><br>
  <?php endforeach; ?>

  <?php else: ?>
    投稿されたメッセージはありません
  <?php endif; ?>

  </body>
  </html>