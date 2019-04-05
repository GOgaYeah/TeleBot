-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 12 2019 г., 23:06
-- Версия сервера: 5.7.21-20-beget-5.7.21-20-1-log
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `burov0798_rise`
--

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--
-- Создание: Янв 11 2019 г., 20:58
-- Последнее обновление: Янв 12 2019 г., 13:46
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `qiwi` bigint(11) NOT NULL,
  `code` int(11) NOT NULL,
  `summa` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `qiwi`
--
-- Создание: Янв 11 2019 г., 20:15
-- Последнее обновление: Янв 12 2019 г., 20:06
--

DROP TABLE IF EXISTS `qiwi`;
CREATE TABLE `qiwi` (
  `id` int(11) NOT NULL,
  `number` bigint(12) NOT NULL,
  `token` text NOT NULL,
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `send`
--
-- Создание: Янв 12 2019 г., 14:21
-- Последнее обновление: Янв 12 2019 г., 15:30
--

DROP TABLE IF EXISTS `send`;
CREATE TABLE `send` (
  `qiwi` bigint(20) NOT NULL,
  `id` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `send`
--

INSERT INTO `send` (`qiwi`, `id`) VALUES
(79388739072, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--
-- Создание: Янв 11 2019 г., 19:51
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `balance` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `orders` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `uid`, `name`, `balance`, `discount`, `status`, `orders`, `date`) VALUES
(14, 241998597, 'bur', 0, 0, 0, 0, '2019-01-11 17:38:57'),
(15, 643572844, 'Your Rise', 0, 0, 0, 0, '2019-01-11 19:40:58'),
(16, 201530676, 'Foxes', 0, 0, 0, 0, '2019-01-11 19:43:36');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `qiwi`
--
ALTER TABLE `qiwi`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `qiwi`
--
ALTER TABLE `qiwi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
