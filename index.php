<?php

$taskFile = 'tasks.json';
$taskList = file_exists($taskFile) ? json_decode(file_get_contents($taskFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newTask'])) {
        $taskList[] = ['description' => $_POST['newTask'], 'isComplete' => false, 'timestamp' => date('Y-m-d H:i:s')];
        file_put_contents($taskFile, json_encode($taskList));
    } elseif (isset($_POST['markComplete'])) {
        $taskList[$_POST['markComplete']]['isComplete'] = true;
        file_put_contents($taskFile, json_encode($taskList));
    } elseif (isset($_POST['deleteTask'])) {
        array_splice($taskList, $_POST['deleteTask'], 1);
        file_put_contents($taskFile, json_encode($taskList));
    } elseif (isset($_POST['editTask'])) {
        $taskList[$_POST['editTask']]['description'] = $_POST['updatedTask'];
        file_put_contents($taskFile, json_encode($taskList));
    }
}

$remainingTasks = count(array_filter($taskList, fn($task) => !$task['isComplete']));
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
        }
        .navbar {
            background-color: #007bff;
            padding: 10px;
            color: white;
            text-align: center;
            font-size: 18px;
        }
        .task-container {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
            margin: 20px auto;
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
        .task-buttons button.edit {
            background: #ffc107;
            color: #fff;
        }
        .task-buttons button.edit:hover {
            background: #e0a800;
        }
        .timestamp {
            font-size: 12px;
            color: #666;
            display: block;
            margin-top: 5px;
        }
        .task-desc {
            display: flex;
            flex-direction: column;
        }
        .remaining-tasks {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Task Manager
    </div>
    <div class="task-container">
        <h1>Task List</h1>
        <form method="post">
            <input type="text" name="newTask" placeholder="New task..." required>
            <input type="submit" value="Add Task">
        </form>
        <ul>
            <?php foreach ($taskList as $index => $task): ?>
                <li>
                    <div class="task-desc">
                        <?php if ($task['isComplete']): ?>
                            <s><?= htmlspecialchars($task['description']) ?></s>
                        <?php else: ?>
                            <?= htmlspecialchars($task['description']) ?>
                        <?php endif; ?>
                        <span class="timestamp"><?= htmlspecialchars($task['timestamp']) ?></span>
                    </div>
                    <div class="task-buttons">
                        <form method="post">
                            <button type="submit" name="markComplete" value="<?= $index ?>" class="complete">Complete</button>
                            <button type="submit" name="deleteTask" value="<?= $index ?>" class="delete">Delete</button>
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="editTask" value="<?= $index ?>">
                            <input type="text" name="updatedTask" placeholder="Update task" required>
                            <button type="submit" class="edit">Edit</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="remaining-tasks">
            Remaining Tasks: <?= $remainingTasks ?>
        </div>
    </div>
</body>
</html>
