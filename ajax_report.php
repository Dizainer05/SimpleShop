<?php
require 'db.php';

$report = $_GET['report'] ?? '';
$format = $_GET['format'] ?? 'html';

switch($report) {
    case 'prices':
        $data = getProductPrices($pdo);
        if($format === 'json') {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }
        foreach($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td><?= htmlspecialchars($row['category'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['color'] ?? '—') ?></td>
            </tr>
        <?php endforeach;
        break;
        
    case 'categories':
        $data = getCategorySummary($pdo);
        if($format === 'json') {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }
        foreach($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= $row['product_count'] ?></td>
                <td><?= number_format($row['total_price'], 2) ?></td>
            </tr>
        <?php endforeach;
        break;
        
    case 'colors':
        $data = getColorSummary($pdo);
        if($format === 'json') {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }
        foreach($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><?= $row['product_count'] ?></td>
                <td><?= number_format($row['total_value'], 2) ?></td>
            </tr>
        <?php endforeach;
        break;
}
?>