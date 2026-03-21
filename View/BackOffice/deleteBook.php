<?php
require_once __DIR__ . "/../../Controller/BookController.php";

if (!isset($_GET['id'])) {
    header('Location: showBook.php');
    exit;
}

$id = (int)$_GET['id'];
$controller = new BookController();
$controller->delete($id);

header('Location: showBook.php');
exit;
?>
