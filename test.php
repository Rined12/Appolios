<?php
require_once __DIR__ . "/Model/Category.php";
require_once __DIR__ . "/Model/Book.php";

echo "Testing Category and Book entities...\n";

// Create a category
$category = new Category(1, "Science");
echo "Category created: ID=" . $category->getId() . ", Name=" . $category->getName() . "\n";

// Create a book
$book = new Book(1, "Test Book", "Test Author", "2023-01-01", "EN", true, 5, $category);
echo "Book created: ID=" . $book->getId() . ", Title=" . $book->getTitle() . ", Category=" . $book->getCategory()->getName() . "\n";

// Check relationship
$category->addBook($book);
echo "Books in category: " . count($category->getBooks()) . "\n";

echo "Test passed!\n";
?>