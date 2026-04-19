<?php
require_once __DIR__ . "/../../Model/Book.php";
require_once __DIR__ . "/../../Controller/BookController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: showBook.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$publicationDate = $_POST['publication_date'] ?? '';
$language = $_POST['language'] ?? '';
$status = isset($_POST['status']) && (int)$_POST['status'] === 1;
$copies = (int)($_POST['number_of_copies'] ?? 0);
$categoryId = (int)($_POST['category_id'] ?? 0);

if ($id < 1 || $title === '' || $author === '' || $publicationDate === '' || $language === '' || $copies < 1 || $categoryId < 1) {
    die('Donnees invalides.');
}

$controller = new BookController();
$category = $controller->findCategoryById($categoryId);
if (!$category) {
    die('Categorie invalide.');
}

$book = new Book($id, $title, $author, $publicationDate, $language, $status, $copies, $category);

$controller->update($book);

header('Location: showBook.php');
exit;
?>
