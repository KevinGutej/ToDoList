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
