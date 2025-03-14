-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:8889
-- Время создания: Мар 14 2025 г., 14:54
-- Версия сервера: 8.0.35
-- Версия PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `shop`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddProduct` (IN `p_name` VARCHAR(255), IN `p_price` DECIMAL(10,2), IN `p_description` TEXT, IN `p_image_path` VARCHAR(255), IN `p_color_id` INT, IN `p_category_id` INT)   BEGIN
    INSERT INTO products (name, price, description, image_path, color_id, category_id) 
    VALUES (p_name, p_price, p_description, p_image_path, p_color_id, p_category_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProduct` (IN `p_id` INT)   BEGIN
    DELETE FROM products WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `FilterProducts` (IN `p_color` VARCHAR(255), IN `p_category` VARCHAR(255))   BEGIN
    SELECT p.id, p.name, p.price, p.description, p.image_path, c.name as color, cat.name as category 
    FROM products p
    LEFT JOIN colors c ON p.color_id = c.id
    LEFT JOIN categories cat ON p.category_id = cat.id
    WHERE (p_color = '' OR c.name = p_color)
      AND (p_category = '' OR cat.name = p_category);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCategorySummary` ()   BEGIN
    SELECT 
        COALESCE(cat.name, 'Без категории') AS category,
        SUM(p.price) AS total_price,
        COUNT(p.id) AS product_count
    FROM products p
    LEFT JOIN categories cat ON p.category_id = cat.id
    GROUP BY cat.name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetColorSummary` ()   BEGIN
    SELECT 
        COALESCE(c.name, 'Без цвета') AS color,
        COUNT(p.id) AS product_count,
        SUM(p.price) AS total_value
    FROM products p
    LEFT JOIN colors c ON p.color_id = c.id
    GROUP BY c.name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPriceExtremes` ()   BEGIN
    -- Самый дорогой товар
    SELECT name, price 
    FROM products 
    ORDER BY price DESC 
    LIMIT 1;

    -- Самый дешевый товар
    SELECT name, price 
    FROM products 
    ORDER BY price ASC 
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductPrices` ()   BEGIN
    SELECT 
        name,
        price,
        (SELECT name FROM categories WHERE id = p.category_id) AS category,
        (SELECT name FROM colors WHERE id = p.color_id) AS color
    FROM products p
    ORDER BY name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProducts` (IN `sort_column` VARCHAR(50), IN `sort_order` VARCHAR(4))   BEGIN
    SET @query = CONCAT(
        'SELECT p.id, p.name, p.price, p.description, p.image_path, c.name as color, cat.name as category ',
        'FROM products p ',
        'LEFT JOIN colors c ON p.color_id = c.id ',
        'LEFT JOIN categories cat ON p.category_id = cat.id ',
        'ORDER BY ', sort_column, ' ', sort_order
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SearchProducts` (IN `query` TEXT)   BEGIN
    SET @query = CONCAT('%', query, '%');
    SELECT p.id, p.name, p.price, p.description, p.image_path, c.name as color, cat.name as category
    FROM products p
    LEFT JOIN colors c ON p.color_id = c.id
    LEFT JOIN categories cat ON p.category_id = cat.id
    WHERE p.name LIKE @query 
       OR p.price LIKE @query 
       OR p.description LIKE @query 
       OR c.name LIKE @query 
       OR cat.name LIKE @query;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProduct` (IN `p_id` INT, IN `p_name` VARCHAR(255), IN `p_price` DECIMAL(10,2), IN `p_description` TEXT, IN `p_image_path` VARCHAR(255), IN `p_color_id` INT, IN `p_category_id` INT)   BEGIN
    UPDATE products 
    SET name = p_name, 
        price = p_price, 
        description = p_description,
        image_path = p_image_path,
        color_id = p_color_id,
        category_id = p_category_id
    WHERE id = p_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Аксессуары'),
(4, 'Бу'),
(2, 'Компьютерная периферия'),
(1, 'Электроника');

-- --------------------------------------------------------

--
-- Структура таблицы `colors`
--

CREATE TABLE `colors` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `colors`
--

INSERT INTO `colors` (`id`, `name`) VALUES
(2, 'Белый'),
(3, 'Красный'),
(4, 'Синий'),
(5, 'уцй'),
(1, 'Черный');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `color_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image_path`, `color_id`, `category_id`) VALUES
(1, 'Ноутбук HP', 3000.00, 'Мощный ноутбук для работы и игр', 'images/Unknown.jpeg', 1, 1),
(2, 'Смартфон Samsung', 2500.00, 'Новый флагман с отличной камерой', 'images/cc66d01b8f5d5a7e07b815d48b8de06d.webp', 2, 1),
(3, 'Клавиатура Logitech', 399.00, 'Механическая клавиатура для геймеров', 'images/Logitech-G-PRO-X-TKL-GX-Taktile-magenta.jpg', 3, 2),
(4, 'Мышь Razer', 59.00, 'Игровая мышь с RGB-подсветкой', 'images/6368581832.jpg', 4, 2),
(9, 'MacBook Air 13', 3999.00, 'Модель-Ноутбук Apple MacBook Air 13&M2 2022 512GB / MLY03 (серебристый) код 7.683.061 имеет кириллицу', 'images/index.webp', 2, 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
