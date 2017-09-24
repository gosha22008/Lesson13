<?php ob_start() ?>
    <html>
    <head>
        <style>
            table {
                border-spacing: 0;
                border-collapse: collapse;
            }
            table td, table th {
                border: 1px solid #ccc;
                padding: 5px;
            }
            table th {
                background: #eee;
            }
        </style>
    </head>
    <body><h1>Список дел на сегодня</h1>
    <div style="float: left">
        <form method="POST">
            <input name="description" placeholder="Описание задачи" value="" type="text">
            <input name="save" value="Добавить" type="submit">
        </form>
    </div>
    <div style="float: left; margin-left: 20px;">
    <!--    <form method="POST">-->
    <!--        <label for="sort">Сортировать по:</label>-->
    <!--        <select name="sort_by">-->
    <!--            <option value="date_created">Дате добавления</option>-->
    <!--            <option value="is_done">Статусу</option>-->
    <!--            <option value="description">Описанию</option>-->
    <!--        </select>-->
    <!--        <input name="sort" value="Отсортировать" type="submit">-->
    <!--    </form>-->
    </div>
    <div style="clear: both"></div>

    <table>
        <tbody>
        <tr>
            <th>Описание задачи</th>
            <th>Статус</th>
            <th>Дата добавления</th>
            <th></th>
        </tr>
    </body>
    </html>

<?php
$host = 'localhost';    //127.0.0.1
$db = 'Lesson13(4.2)';
$user = 'root';
$password = null;
$charset = 'utf8';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);

function showTasks($pdo)
{
    $sql = "SELECT * FROM `tasks` ";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    while ($row = $statement->FETCH(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?= $row['description'] ?> </td>
            <?php if ($row['is_done'] == 0) {
                $done = 'В процессе';
                $color = 'orange';
            } else if ($row['is_done'] == 1) {
                $done = 'Выполнено';
                $color = 'green';
            } ?>
            <td><span style="color: <?= $color ?>;"><?= $done ?></span></td>
            <td><?= $row['date_added'] ?> </td>
            <td>
                <a href="?id=<?= $row['id'] ?>&amp;action=edit">Изменить</a>
                <a href="?id=<?= $row['id'] ?>&amp;action=done">Выполнить</a>
                <a href="?id=<?= $row['id'] ?>&amp;action=delete">Удалить</a>
            </td>
        </tr>
    <?php }
}
if (isset($_POST['description']) and !empty($_POST['description'])) {
    $desc = $_POST['description'];
    $sqlInsert = "INSERT INTO `tasks` (`description`, `is_done`, `date_added`) VALUES ('$desc', '0', now())";
    $statement = $pdo->prepare($sqlInsert);
    $statement->execute();
    showTasks($pdo);
    header('Location: Index.php');
}
if (isset ($_GET['action']) and !empty($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'done') {
        $sqlD = "UPDATE `tasks` SET `is_done` = 1 WHERE id = '$id'";
    } else if ($_GET['action'] == 'delete') {
        $sqlD = "DELETE FROM `tasks` WHERE id = '$id'";
    }
    $statement = $pdo->prepare($sqlD);
    $statement->execute();
    if ($_GET['action'] == 'edit'){
        $sqlDesc = "SELECT * FROM tasks WHERE id = '$id' ";
        $statement = $pdo->prepare($sqlDesc);
        $statement->execute();
        $row1 = $statement->FETCH(PDO::FETCH_ASSOC);
?>
    <form method="POST">
        <input name="Newdescription" placeholder="Описание задачи" value="<?= $row1['description'] ?>" type="text">
        <input name="save" value="Сохранить" type="submit">
    </form>
</div>
<?php
        if (isset($_POST['Newdescription'])){
            $newDesc = $_POST['Newdescription'];
            $sqlNewDesc = "UPDATE tasks SET `description` = '$newDesc' WHERE id = '$id'  ";
            $statement = $pdo->prepare($sqlNewDesc);
            $statement->execute();
            showTasks($pdo);
            header('Location: Index.php');
        }
    }
}
showTasks($pdo);
ob_end_flush();
?>