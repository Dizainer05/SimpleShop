<?php
require 'db.php';

$sort_column = $_GET['sort_column'] ?? 'id';
$sort_order  = $_GET['sort_order'] ?? 'ASC';
$searchQuery = $_GET['search'] ?? '';
$color = $_GET['color'] ?? '';
$category = $_GET['category'] ?? '';

if ($searchQuery) {
    $products = searchProducts($pdo, $searchQuery);
} else if ($color || $category) {
    $products = filterProducts($pdo, $color, $category);
} else {
    $products = getProducts($pdo, $sort_column, $sort_order);
}

if(count($products) > 0):
  foreach($products as $product): ?>
    <tr>
      <td><?= $product['id'] ?></td>
      <td><?= htmlspecialchars($product['name']) ?></td>
      <td><?= number_format($product['price'], 2) ?></td>
      <td><?= htmlspecialchars($product['description']) ?></td>
      <td><?= htmlspecialchars($product['color'] ?? '—') ?></td>
      <td><?= htmlspecialchars($product['category'] ?? '—') ?></td>
      <td>
        <?php if ($product['image_path']): ?>
          <img src="<?= $product['image_path'] ?>" alt="Фото" style="max-width: 100px;">
        <?php else: ?>
          Нет фото
        <?php endif; ?>
      </td>
      <td>
        <button type="button" 
                class="btn btn-primary btn-sm edit-btn"
                data-bs-toggle="modal"
                data-bs-target="#editModal"
                data-id="<?= $product['id'] ?>"
                data-name="<?= htmlspecialchars($product['name']) ?>"
                data-price="<?= $product['price'] ?>"
                data-description="<?= htmlspecialchars($product['description']) ?>"
                data-color="<?= htmlspecialchars($product['color']) ?>"
                data-category="<?= htmlspecialchars($product['category']) ?>"
                data-image="<?= $product['image_path'] ?>">
          Изменить
        </button>
        <form method="POST" class="d-inline" onsubmit="return confirm('Удалить товар?');">
          <input type="hidden" name="id" value="<?= $product['id'] ?>">
          <button type="submit" name="delete" class="btn btn-danger btn-sm">Удалить</button>
        </form>
      </td>
    </tr>
  <?php endforeach;
else: ?>
  <tr>
    <td colspan="8" class="text-center">Товары не найдены</td>
  </tr>
<?php endif; ?>