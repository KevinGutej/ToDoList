<?php

$taskFile = 'tasks.json';
$taskList = file_exists($taskFile) ? json_decode(file_get_contents($taskFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newTask'])) {
        $taskList[] = [
            'description' => $_POST['newTask'],
            'isComplete' => false,
            'timestamp' => date('Y-m-d H:i:s')
        ];
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

function getFilteredTasks($taskList, $filter) {
    switch ($filter) {
        case 'completed':
            return array_filter($taskList, fn($task) => $task['isComplete']);
        case 'pending':
            return array_filter($taskList, fn($task) => !$task['isComplete']);
        default:
            return $taskList;
    }
}

$filter = $_GET['filter'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';
$filteredTasks = getFilteredTasks($taskList, $filter);
$remainingTasks = count(array_filter($taskList, fn($task) => !$task['isComplete']));

if ($searchQuery) {
    $filteredTasks = array_filter($filteredTasks, fn($task) => stripos($task['description'], $searchQuery) !== false);
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
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .title {
            font-size: 24px;
        }
        .navbar .links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-size: 18px;
        }
        .navbar .links a:hover {
            text-decoration: underline;
        }
        .task-container {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 500px;
            margin: 40px auto;
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
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 25%;
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
        .filter-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-buttons button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px;
        }
        .filter-buttons button:hover {
            background: #0056b3;
        }
        .filter-buttons .active {
            background: #0056b3;
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
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        li.s {
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
        .search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            margin-right: 10px;
        }
        .search-bar input[type="submit"] {
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .search-bar input[type="submit"]:hover {
            background: #0056b3;
        }
        .task-desc .priority {
            font-weight: bold;
            color: #dc3545;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(20px); }
        }
        li.added {
            animation: fadeIn 0.3s forwards;
        }
        li.deleted {
            animation: fadeOut 0.3s forwards;
        }
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%; 
            left: 50%; 
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">Task Manager</div>
        <div class="links">
            <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">All Tasks</a>
            <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>">Completed</a>
        </div>
    </div>
    <div class="task-container">
        <h1>Task List</h1>
        <div class="search-bar">
            <form method="get">
                <input type="text" name="search" placeholder="Search tasks..." value="<?= htmlspecialchars($searchQuery) ?>">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="filter-buttons">
            <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>"><button>All</button></a>
            <a href="?filter=pending" class="<?= $filter == 'pending' ? 'active' : '' ?>"><button>Pending</button></a>
            <a href="?filter=completed" class="<?= $filter == 'completed' ? 'active' : '' ?>"><button>Completed</button></a>
        </div>
        <form method="post">
            <input type="text" name="newTask" placeholder="New task..." required>
            <input type="submit" value="Add Task">
        </form>
        <ul id="taskList">
            <?php foreach ($filteredTasks as $index => $task): ?>
                <li data-index="<?= $index ?>" class="<?= $task['isComplete'] ? 'completed-task' : 'pending-task' ?>">
                    <div class="task-desc">
                        <?php if ($task['isComplete']): ?>
                            <s><?= htmlspecialchars($task['description']) ?></s>
                        <?php else: ?>
                            <?= htmlspecialchars($task['description']) ?>
                        <?php endif; ?>
                        <span class="timestamp"><?= htmlspecialchars($task['timestamp']) ?></span>
                    </div>
                    <div class="task-buttons">
                        <form method="post" class="mark-complete-form">
                            <button type="submit" name="markComplete" value="<?= $index ?>" class="complete tooltip">Complete<span class="tooltiptext">Mark as complete</span></button>
                            <button type="submit" name="deleteTask" value="<?= $index ?>" class="delete tooltip">Delete<span class="tooltiptext">Delete task</span></button>
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="editTask" value="<?= $index ?>">
                            <input type="text" name="updatedTask" placeholder="Update task" required>
                            <button type="submit" class="edit tooltip">Edit<span class="tooltiptext">Edit task</span></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="remaining-tasks">
            Remaining Tasks: <?= $remainingTasks ?>
        </div>
    </div>
    <script>
        document.querySelectorAll('.mark-complete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                let taskItem = this.closest('li');
                let index = taskItem.getAttribute('data-index');
                taskItem.classList.add('deleted');
                fetch('<?= $_SERVER['PHP_SELF'] ?>', {
                    method: 'POST',
                    body: new FormData(this)
                }).then(response => {
                    if (response.ok) {
                        taskItem.remove();
                    }
                });
            });
        });

        document.querySelectorAll('.delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                this.closest('li').classList.add('deleted');
            });
        });
    </script>
</body>
</html>
