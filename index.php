<?php
require 'db.php';

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'images/';
            $image_path = $upload_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }
        addProduct($pdo, $_POST['name'], $_POST['price'], $_POST['description'], $image_path, $_POST['color'], $_POST['category']);
    } elseif (isset($_POST['update'])) {
        $image_path = $_POST['existing_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'images/';
            $image_path = $upload_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }
        updateProduct($pdo, $_POST['id'], $_POST['name'], $_POST['price'], $_POST['description'], $image_path, $_POST['color'], $_POST['category']);
    } elseif (isset($_POST['delete'])) {
        deleteProduct($pdo, $_POST['id']);
    }
}

$colors = $pdo->query("SELECT name FROM colors")->fetchAll(PDO::FETCH_COLUMN);
$categories = $pdo->query("SELECT name FROM categories")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Простой магазин</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 
</head>
<body>
  <!-- Добавить перед закрывающим </body> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <div class="container my-5">
    <h1 class="mb-4">Простой магазин</h1>
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Добавить товар</button>
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" id="searchInput" class="form-control" placeholder="Поиск по товарам">
          <button type="button" id="searchBtn" class="btn btn-primary">Поиск</button>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-auto">
            <select id="sortColumn" class="form-select">
              <option value="id">ID</option>
              <option value="name">Название</option>
              <option value="price">Цена руб</option>
            </select>
          </div>
          <div class="col-auto">
            <select id="sortOrder" class="form-select">
              <option value="ASC">По возрастанию</option>
              <option value="DESC">По убыванию</option>
            </select>
          </div>
          <div class="col-auto">
            <select id="filterColor" class="form-select">
              <option value="">Все цвета</option>
              <?php foreach ($colors as $color): ?>
                <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-auto">
            <select id="filterCategory" class="form-select">
              <option value="">Все категории</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Название</th>
          <th>Цена руб</th>
          <th>Описание</th>
          <th>Цвет</th>
          <th>Категория</th>
          <th>Фото</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody id="productsTable"></tbody>
    </table>


    <!-- Заменить существующий блок отчетов в index.php -->
<div class="mt-5">
    <h2>Отчеты</h2>
    
    <ul class="nav nav-tabs mb-3" id="reportsTab">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#prices">Товары-Цены</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#categories">По категориям</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#colors">По цветам</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Отчет Товары-Цены -->
        <div class="tab-pane fade show active" id="prices">
        <h3>Товары-Цены</h3>
        <button class="btn btn-primary download-report" data-report="prices">Скачать отчет</button>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Цена</th>
                                <th>Категория</th>
                                <th>Цвет</th>
                            </tr>
                        </thead>
                        <tbody id="pricesReport"></tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="priceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Отчет по категориям -->
        <div class="tab-pane fade" id="categories">
        <h3>По категориям</h3>
        <button class="btn btn-primary download-report" data-report="categories">Скачать отчет</button>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Категория</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesReport"></tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Отчет по цветам -->
        <div class="tab-pane fade" id="colors">
        <h3>По цветам</h3>
        <button class="btn btn-primary download-report" data-report="colors">Скачать отчет</button>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Цвет</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody id="colorsReport"></tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="colorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Модальное окно добавления -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="addModalLabel">Добавить товар</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="add-name" class="form-label">Название</label>
              <input type="text" class="form-control" name="name" id="add-name" required>
            </div>
            <div class="mb-3">
              <label for="add-price" class="form-label">Цена руб</label>
              <input type="number" class="form-control" name="price" id="add-price" required>
            </div>
            <div class="mb-3">
              <label for="add-description" class="form-label">Описание</label>
              <textarea class="form-control" name="description" id="add-description"></textarea>
            </div>
            <div class="mb-3">
              <label for="add-color" class="form-label">Цвет</label>
              <input type="text" class="form-control" list="colorList" name="color" id="add-color">
              <datalist id="colorList">
                <?php foreach ($colors as $color): ?>
                  <option value="<?= htmlspecialchars($color) ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="mb-3">
              <label for="add-category" class="form-label">Категория</label>
              <input type="text" class="form-control" list="categoryList" name="category" id="add-category">
              <datalist id="categoryList">
                <?php foreach ($categories as $category): ?>
                  <option value="<?= htmlspecialchars($category) ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="mb-3">
              <label for="add-image" class="form-label">Фото</label>
              <input type="file" class="form-control" name="image" id="add-image">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            <button type="submit" name="add" class="btn btn-success">Добавить</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Модальное окно редактирования -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Редактировать товар</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="existing_image" id="edit-existing-image">
            <div class="mb-3">
              <label for="edit-name" class="form-label">Название</label>
              <input type="text" class="form-control" name="name" id="edit-name" required>
            </div>
            <div class="mb-3">
              <label for="edit-price" class="form-label">Цена руб</label>
              <input type="number" class="form-control" name="price" id="edit-price" required>
            </div>
            <div class="mb-3">
            <label for="edit-description" class="form-label">Описание</label>
            <textarea class="form-control" name="description" id="edit-description"></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-color" class="form-label">Цвет</label>
            <input type="text" class="form-control" list="colorList" name="color" id="edit-color">
          </div>
          <div class="mb-3">
            <label for="edit-category" class="form-label">Категория</label>
            <input type="text" class="form-control" list="categoryList" name="category" id="edit-category">
          </div>
          <div class="mb-3">
            <label for="edit-image" class="form-label">Фото</label>
            <input type="file" class="form-control" name="image" id="edit-image">
            <img src="" id="edit-image-preview" class="mt-2" style="max-width: 200px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
          <button type="submit" name="update" class="btn btn-primary">Сохранить</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = document.getElementById('editModal')
editModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget
  const id = button.getAttribute('data-id')
  const name = button.getAttribute('data-name')
  const price = button.getAttribute('data-price')
  const description = button.getAttribute('data-description')
  const color = button.getAttribute('data-color')
  const category = button.getAttribute('data-category')
  const image = button.getAttribute('data-image')

  editModal.querySelector('#edit-id').value = id
  editModal.querySelector('#edit-name').value = name
  editModal.querySelector('#edit-price').value = price
  editModal.querySelector('#edit-description').value = description
  editModal.querySelector('#edit-color').value = color
  editModal.querySelector('#edit-category').value = category
  editModal.querySelector('#edit-existing-image').value = image
  editModal.querySelector('#edit-image-preview').src = image
})

// Функция загрузки товаров
function loadProducts() {
  const params = new URLSearchParams({
    search: document.getElementById('searchInput').value,
    sort_column: document.getElementById('sortColumn').value,
    sort_order: document.getElementById('sortOrder').value,
    color: document.getElementById('filterColor').value,
    category: document.getElementById('filterCategory').value
  })

  fetch(`ajax_products.php?${params}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('productsTable').innerHTML = html
    })
}

// Обработчики событий
document.getElementById('searchInput').addEventListener('input', loadProducts)
document.getElementById('sortColumn').addEventListener('change', loadProducts)
document.getElementById('sortOrder').addEventListener('change', loadProducts)
document.getElementById('filterColor').addEventListener('change', loadProducts)
document.getElementById('filterCategory').addEventListener('change', loadProducts)
document.getElementById('searchBtn').addEventListener('click', loadProducts)

// Первоначальная загрузка
window.addEventListener('DOMContentLoaded', loadProducts)


// Добавить в секцию скриптов
let priceChart = null;
let categoryChart = null;
let colorChart = null;

function destroyChart(chart) {
    if (chart) {
        chart.destroy();
    }
}

async function loadChartData(reportType) {
    const response = await fetch(`ajax_report.php?report=${reportType}&format=json`);
    return await response.json();
}

async function updateCharts() {
    // Ценовой график (столбчатая диаграмма)
    destroyChart(priceChart);
    const priceData = await loadChartData('prices');
    priceChart = new Chart(document.getElementById('priceChart'), {
        type: 'bar',
        data: {
            labels: priceData.map(item => item.name),
            datasets: [{
                label: 'Цены товаров',
                data: priceData.map(item => item.price),
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        }
    });

    // Категории (круговая диаграмма)
    destroyChart(categoryChart);
    const categoryData = await loadChartData('categories');
    categoryChart = new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: categoryData.map(item => item.category),
            datasets: [{
                label: 'Распределение по категориям',
                data: categoryData.map(item => item.total_price),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                    '#9966FF', '#FF9F40', '#EB5757', '#27AE60'
                ]
            }]
        }
    });

    // Цвета (линейный график)
    destroyChart(colorChart);
    const colorData = await loadChartData('colors');
    colorChart = new Chart(document.getElementById('colorChart'), {
        type: 'line',
        data: {
            labels: colorData.map(item => item.color),
            datasets: [{
                label: 'Количество товаров по цветам',
                data: colorData.map(item => item.product_count),
                borderColor: '#4BC0C0',
                tension: 0.1
            }]
        }
    });
}

// Обновленная функция loadReports
async function loadReports() {
    // Загрузка табличных данных
    const reports = ['prices', 'categories', 'colors'];
    reports.forEach(report => {
        fetch(`ajax_report.php?report=${report}`)
            .then(r => r.text())
            .then(html => document.getElementById(`${report}Report`).innerHTML = html)
    });
    
    // Загрузка данных для графиков
    await updateCharts();
}

// Обновить обработчик событий
window.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    loadReports();
});

// Добавить в секцию скриптов
document.querySelectorAll('.download-report').forEach(btn => {
    btn.addEventListener('click', function() {
        const reportType = this.dataset.report;
        window.location.href = `download_report.php?report=${reportType}`;
    });
});
</script>
</body>
</html>