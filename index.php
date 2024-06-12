<?php

$file = 'data.json';
$todoList = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['item'])) {
        $todoList[] = ['item' => $_POST['item'], 'done' => false];
        file_put_contents($file, json_encode($todoList));
    } elseif (isset($_POST['mark'])) {
        $todoList[$_POST['mark']]['done'] = true;
        file_put_contents($file, json_encode($todoList));
    } elseif (isset($_POST['remove'])) {
        array_splice($todoList, $_POST['remove'], 1);
        file_put_contents($file, json_encode($todoList));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To do list</title>
</head>
<body>
    <h1>To do:</h1>
    <form method="post">
        <input type="text" name="item" required>
        <input type="submit" value="Add Task">
    </form>
    <ul>
        <?php foreach ($todoList as $key => $task): ?>
            <li>
                <?php if ($task['done']): ?>
                    <s><?= htmlspecialchars($task['item']) ?></s>
                <?php else: ?>
                    <?= htmlspecialchars($task['item']) ?>
                <?php endif; ?>
                <form method="post" style="display:inline;">
                    <button type="submit" name="mark" value="<?= $key ?>">Complete</button>
                    <button type="submit" name="remove" value="<?= $key ?>">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
