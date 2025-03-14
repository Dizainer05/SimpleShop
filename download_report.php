<?php
require 'db.php';

$report = $_GET['report'] ?? '';
$allowed_reports = ['prices', 'categories', 'colors'];

if (!in_array($report, $allowed_reports)) {
    die("Неверный тип отчета");
}

// Получаем данные для отчета
switch($report) {
    case 'prices':
        $data = getProductPrices($pdo);
        $filename = "prices_report_" . date('Y-m-d') . ".csv";
        $headers = ['Название товара', 'Цена', 'Категория', 'Цвет'];
        break;
    case 'categories':
        $data = getCategorySummary($pdo);
        $filename = "categories_report_" . date('Y-m-d') . ".csv";
        $headers = ['Категория', 'Количество товаров', 'Общая стоимость'];
        break;
    case 'colors':
        $data = getColorSummary($pdo);
        $filename = "colors_report_" . date('Y-m-d') . ".csv";
        $headers = ['Цвет', 'Количество товаров', 'Общая стоимость'];
        break;
}

// Генерируем CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, $headers, ';');

foreach ($data as $row) {
    $values = [];
    switch($report) {
        case 'prices':
            $values = [
                $row['name'],
                $row['price'],
                $row['category'] ?? 'Нет категории',
                $row['color'] ?? 'Нет цвета'
            ];
            break;
        case 'categories':
            $values = [
                $row['category'],
                $row['product_count'],
                $row['total_price']
            ];
            break;
        case 'colors':
            $values = [
                $row['color'],
                $row['product_count'],
                $row['total_value']
            ];
            break;
    }
    fputcsv($output, $values, ';');
}

fclose($output);
exit;