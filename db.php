<?php
$host = 'localhost';
$dbname = 'shop';
$user = 'root'; 
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Функции для работы с цветами и категориями
function getOrCreateColor($pdo, $colorName) {
    if (empty($colorName)) return null;
    $stmt = $pdo->prepare("SELECT id FROM colors WHERE name = ?");
    $stmt->execute([$colorName]);
    $color = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($color) return $color['id'];
    $stmt = $pdo->prepare("INSERT INTO colors (name) VALUES (?)");
    $stmt->execute([$colorName]);
    return $pdo->lastInsertId();
}

function getOrCreateCategory($pdo, $categoryName) {
    if (empty($categoryName)) return null;
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$categoryName]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category) return $category['id'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$categoryName]);
    return $pdo->lastInsertId();
}

// Функции для работы с товарами
function getProducts($pdo, $sort_column = 'id', $sort_order = 'ASC') {
    $stmt = $pdo->prepare("CALL GetProducts(?, ?)");
    $stmt->execute([$sort_column, $sort_order]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchProducts($pdo, $query) {
    $stmt = $pdo->prepare("CALL SearchProducts(?)");
    $stmt->execute([$query]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function filterProducts($pdo, $color = null, $category = null) {
    $stmt = $pdo->prepare("CALL FilterProducts(?, ?)");
    $stmt->execute([$color, $category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addProduct($pdo, $name, $price, $description, $image_path, $color = null, $category = null) {
    $colorId = getOrCreateColor($pdo, $color);
    $categoryId = getOrCreateCategory($pdo, $category);
    $stmt = $pdo->prepare("CALL AddProduct(?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $price, $description, $image_path, $colorId, $categoryId]);
}

function updateProduct($pdo, $id, $name, $price, $description, $image_path, $color = null, $category = null) {
    $colorId = getOrCreateColor($pdo, $color);
    $categoryId = getOrCreateCategory($pdo, $category);
    $stmt = $pdo->prepare("CALL UpdateProduct(?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$id, $name, $price, $description, $image_path, $colorId, $categoryId]);
}

function deleteProduct($pdo, $id) {
    $stmt = $pdo->prepare("CALL DeleteProduct(?)");
    return $stmt->execute([$id]);
}

// Функции для отчетов
function getCategorySummary($pdo) {
    $stmt = $pdo->prepare("CALL GetCategorySummary()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getColorSummary($pdo) {
    $stmt = $pdo->prepare("CALL GetColorSummary()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductPrices($pdo) {
    $stmt = $pdo->prepare("CALL GetProductPrices()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>