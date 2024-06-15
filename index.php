<?php

$taskFile = 'tasks.json';
$taskList = file_exists($taskFile) ? json_decode(file_get_contents($taskFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newTask'])) {
        $taskList[] = ['description' => $_POST['newTask'], 'isComplete' => false];
        file_put_contents($taskFile, json_encode($taskList));
    } elseif (isset($_POST['markComplete'])) {
        $taskList[$_POST['markComplete']]['isComplete'] = true;
        file_put_contents($taskFile, json_encode($taskList));
    } elseif (isset($_POST['deleteTask'])) {
        array_splice($taskList, $_POST['deleteTask'], 1);
        file_put_contents($taskFile, json_encode($taskList));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .task-container {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
        }
        h1 {
            margin: 0 0 20px;
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 75%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 20%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: #218838;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        li s {
            color: #999;
        }
        .task-buttons form {
            display: inline;
        }
        .task-buttons button {
            margin-left: 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .task-buttons button.complete {
            background: #007bff;
            color: #fff;
        }
        .task-buttons button.complete:hover {
            background: #0069d9;
        }
        .task-buttons button.delete {
            background: #dc3545;
            color: #fff;
        }
        .task-buttons button.delete:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="task-container">
        <h1>Task Manager</h1>
        <form method="post">
            <input type="text" name="newTask" placeholder="New task..." required>
            <input type="submit" value="Add Task">
        </form>
        <ul>
            <?php foreach ($taskList as $index => $task): ?>
                <li>
                    <?php if ($task['isComplete']): ?>
                        <s><?= htmlspecialchars($task['description']) ?></s>
                    <?php else: ?>
                        <?= htmlspecialchars($task['description']) ?>
                    <?php endif; ?>
                    <div class="task-buttons">
                        <form method="post">
                            <button type="submit" name="markComplete" value="<?= $index ?>" class="complete">Complete</button>
                            <button type="submit" name="deleteTask" value="<?= $index ?>" class="delete">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
