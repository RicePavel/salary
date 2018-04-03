-- phpMyAdmin SQL Dump
-- version 4.4.15.9
-- https://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Апр 03 2018 г., 14:47
-- Версия сервера: 5.6.37
-- Версия PHP: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `salary`
--

-- --------------------------------------------------------

--
-- Структура таблицы `day_info`
--

CREATE TABLE IF NOT EXISTS `day_info` (
  `day_info_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `time` decimal(10,0) DEFAULT NULL,
  `timetable_worker_id` int(11) NOT NULL,
  `employment_type_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `day_info`
--

INSERT INTO `day_info` (`day_info_id`, `day`, `time`, `timetable_worker_id`, `employment_type_id`) VALUES
(84, 1, '5', 40, 3),
(85, 2, '4', 40, 4),
(86, 3, '4', 40, 3),
(88, 2, NULL, 41, 3),
(89, 3, '5', 41, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `employment_type`
--

CREATE TABLE IF NOT EXISTS `employment_type` (
  `employment_type_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `short_name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `employment_type`
--

INSERT INTO `employment_type` (`employment_type_id`, `name`, `short_name`) VALUES
(2, 'явка', 'Я'),
(3, 'выходной', 'В'),
(4, 'Больничный', 'Б'),
(5, 'Время обучения', 'ВУ');

-- --------------------------------------------------------

--
-- Структура таблицы `position`
--

CREATE TABLE IF NOT EXISTS `position` (
  `position_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `position`
--

INSERT INTO `position` (`position_id`, `name`) VALUES
(6, 'Бухгалтер'),
(7, 'Художественный руководитель');

-- --------------------------------------------------------

--
-- Структура таблицы `timetable`
--

CREATE TABLE IF NOT EXISTS `timetable` (
  `timetable_id` int(11) NOT NULL,
  `create_date` date NOT NULL,
  `number` varchar(256) DEFAULT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `timetable`
--

INSERT INTO `timetable` (`timetable_id`, `create_date`, `number`, `month`, `year`, `unit_id`) VALUES
(40, '2018-04-03', 'null', 4, 2018, 2),
(41, '2018-04-05', 'null', 12, 2018, 3),
(42, '2018-05-01', 'null', 5, 2018, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `timetable_worker`
--

CREATE TABLE IF NOT EXISTS `timetable_worker` (
  `timetable_worker_id` int(11) NOT NULL,
  `timetable_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `timetable_worker`
--

INSERT INTO `timetable_worker` (`timetable_worker_id`, `timetable_id`, `worker_id`, `number`) VALUES
(40, 40, 5, 1),
(41, 41, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `unit`
--

CREATE TABLE IF NOT EXISTS `unit` (
  `unit_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `unit`
--

INSERT INTO `unit` (`unit_id`, `name`) VALUES
(2, 'Администрация'),
(3, 'Бухгалтерия');

-- --------------------------------------------------------

--
-- Структура таблицы `worker`
--

CREATE TABLE IF NOT EXISTS `worker` (
  `worker_id` int(11) NOT NULL,
  `fio` varchar(256) NOT NULL,
  `person_number` varchar(256) NOT NULL,
  `position_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `worker`
--

INSERT INTO `worker` (`worker_id`, `fio`, `person_number`, `position_id`) VALUES
(2, 'Иванов Иван Иванович', '111112', 7),
(3, 'Петров Петр Петрович', '132332', 6),
(5, 'Ревин Лев Соломонович', '121212', 6),
(6, 'Погодина Тамара Анатольевна', '4343', 6);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `day_info`
--
ALTER TABLE `day_info`
  ADD PRIMARY KEY (`day_info_id`),
  ADD KEY `employment_type_id` (`employment_type_id`),
  ADD KEY `timetable_element_ibfk_1` (`timetable_worker_id`);

--
-- Индексы таблицы `employment_type`
--
ALTER TABLE `employment_type`
  ADD PRIMARY KEY (`employment_type_id`);

--
-- Индексы таблицы `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Индексы таблицы `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`timetable_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Индексы таблицы `timetable_worker`
--
ALTER TABLE `timetable_worker`
  ADD PRIMARY KEY (`timetable_worker_id`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `timetable_worker_ibfk_2` (`timetable_id`);

--
-- Индексы таблицы `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- Индексы таблицы `worker`
--
ALTER TABLE `worker`
  ADD PRIMARY KEY (`worker_id`),
  ADD KEY `position_id` (`position_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `day_info`
--
ALTER TABLE `day_info`
  MODIFY `day_info_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT для таблицы `employment_type`
--
ALTER TABLE `employment_type`
  MODIFY `employment_type_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `timetable`
--
ALTER TABLE `timetable`
  MODIFY `timetable_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT для таблицы `timetable_worker`
--
ALTER TABLE `timetable_worker`
  MODIFY `timetable_worker_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT для таблицы `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `worker`
--
ALTER TABLE `worker`
  MODIFY `worker_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `day_info`
--
ALTER TABLE `day_info`
  ADD CONSTRAINT `day_info_ibfk_1` FOREIGN KEY (`timetable_worker_id`) REFERENCES `timetable_worker` (`timetable_worker_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `day_info_ibfk_2` FOREIGN KEY (`employment_type_id`) REFERENCES `employment_type` (`employment_type_id`);

--
-- Ограничения внешнего ключа таблицы `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`);

--
-- Ограничения внешнего ключа таблицы `timetable_worker`
--
ALTER TABLE `timetable_worker`
  ADD CONSTRAINT `timetable_worker_ibfk_1` FOREIGN KEY (`worker_id`) REFERENCES `worker` (`worker_id`),
  ADD CONSTRAINT `timetable_worker_ibfk_2` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`timetable_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `worker`
--
ALTER TABLE `worker`
  ADD CONSTRAINT `worker_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `position` (`position_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
