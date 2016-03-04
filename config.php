<?php

defined('ISHOP') or die('Access denied');

// домен
define('PATH','http://ishop.loc/');

// модель
define('MODEL','model/model.php');

// контроллер
define('CONTROLLER','controller/controller.php');

// вид
define('VIEW','views/');

// активный шаблон
define('TEMPLATE', VIEW.'ishop/');

// папка с картинками контента. С картинками, которые не относятся к оформлению дизайна приложения. (Здесь картинки для базовой картинки товара.)
define('PRODUCTIMG', PATH.'userfiles/product_img/baseimg/');

// путь хранения картинок галереи
define('GALLERYIMG', PATH.'userfiles/product_img/');

// константа для работы с картинками
define('TMPIMG', '../userfiles/product_img/tmp/');
define('BASEIMG', '../userfiles/product_img/baseimg/');

// максимально допустиый вес загружаемых картинок - 1 Мб
define('SIZE', 1048576);

// сервер БД    
define('HOST','localhost');

// пользователь
define('USER','root');

// пароль
define('PASS','');

// БД
define('DB','ishop');

// название магазина - title
define('TITLE','Интернет магазин сотовых телефонов');

// email администратора интернет-магазина
define('ADMIN_EMAIL', 'michael.187@mail.ru');

// количество товаров на страницу
define('PERPAGE', 9);

// папка шаблонов административной части
define('ADMIN_TEMPLATE', 'templates/');

mysql_connect(HOST, USER, PASS) or die('No connect to Server'); // когда подключение к БД делается с конфигурационного файла (то есть отсюда), оно делается один раз (в чём и фишка данного метода)
mysql_select_db(DB) or die('No connect to DB');
mysql_query("SET NAMES 'UTF8'") or die('Can\'t set charset');
