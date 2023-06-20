<?php
$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');
$dsn = "mysql:dbname=$dbName;host=$dbHost:$dbPort";
$dbh = new PDO($dsn, $dbUser, $dbPass);

if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
  $stmt = $dbh->prepare('DELETE FROM notes WHERE id = ?;');
  $stmt->execute(array($_GET['id']));
  header('Location: ./database.php');
}

if (isset($_POST['content']) && $_POST['content'] !== "") {
  $stmt = $dbh->prepare('INSERT INTO notes (content) VALUES (?);');
  $stmt->execute(array($_POST['content']));
  header('Location: ./database.php');
}

$dbh->query('CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    content TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  );');

$stmt = $dbh->query('SELECT * FROM notes ORDER BY created_at DESC;');
$recordLength = $stmt->rowCount();

function convertTz($datetimeText){
  $datetime = new DateTime($datetimeText);
  $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
  return $datetime->format('Y/m/d H:i:s');
}

?>
<!DOCTYPE html>

<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Notes Sample</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .text {
      width: 100%;
      height: 100px;
    }

    .card {
      border: 1px solid grey;
      padding: 16px;
      margin-bottom: 8px;
      display: flex;
      flex-direction: column;
      line-height: 1.2em;
      row-gap: 8px;
    }

    .card .title {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div style="padding:16px;max-width: 1000px;">
    <a href="/">Top page</a>
    <h1>Database Notes Sample</h1>

    <div class="card">
      <form action="./database.php" method="POST">
        <textarea name="content" class="text"></textarea>
        <button type="submit">追加</button>
      </form>
    </div>

    <?= $recordLength === 0 ? "" : "全 $recordLength 件" ?>

    <? while ($note = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
      <? $lines = explode("\n", $note['content']); ?>
      <div class="card">
        <div>
          <span class="title">内容:</span><br>
          <? foreach ($lines as $line) { ?>
            <div style="min-height:1em;">
              <?= htmlspecialchars($line); ?>
            </div>
          <? } ?>
        </div>
        <small>
          <span class="title">ID:</span> <?= $note['id'] ?><br>
          <span class="title">作成日:</span> <?= convertTz($note['created_at']) ?>
        </small>
        <div>
          <a href="./database.php?action=delete&id=<?= $note['id'] ?>">削除</a>
        </div>
      </div>
    <? } ?>

    <? if ($recordLength === 0) {  ?>
      <div class="card">アイテムなし</div>
    <? } ?>
  </div>
</body>

</html>