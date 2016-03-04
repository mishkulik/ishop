<?php
// здесь в админской части индексный файл он одновременно и контроллер
// запрет прямого обращения
define('ISHOP', TRUE);

// стартуем сессию
session_start();

// 'выход' админа из админки
if($_GET['do'] == 'logout'){
    unset($_SESSION['auth']);
}

// подключение файла авторизации
if(!$_SESSION['auth']['admin']){
include $_SERVER['DOCUMENT_ROOT'].'/admin/auth/index.php'; // $_SERVER['DOCUMENT_ROOT'] возвращает путь к корневой директории всего сайта
}

// подключаем конфигурационный файл из пользовательской части
require_once '../config.php';

// подключение файла общих функций 
require_once '../functions/functions.php';

// подключение файла функций административной части (он же модель)
require_once 'functions/functions.php';

// получение количества необработанных заказов
$count_new_orders = count_new_orders();

// асинхронная загрузка картинок AjaxUpload 
if($_POST['id']){
    $id = (int)$_POST['id'];
    upload_gallery_img($id);
}

// асинхронное удаление картинок
if($_POST['img']){
    $res = del_img();
    exit($res);
}

// сортировка страниц
if($_POST['sortable']) {
	
	$result = sort_pages($_POST['sortable']);
	if(!$result) {
		exit(FALSE);
	}
	
	exit(json_encode($result));
}

//сортировка ссылок
if($_POST['sort_link']) {
	
	//проверяем есть ли идентификатор информера к которому принадлежат ссылки
	if(array_key_exists('parent',$_POST)) {
		$parent = $_POST['parent'];
		unset($_POST['parent']);
	}
	else {
		exit(FALSE);
	}
	
	$result = sort_links($_POST['sort_link'],$parent);
	if(!$result) {
		exit(FALSE);
	}
	exit(json_encode($result));
}

//сортировка информеров
if($_POST['sort_inf']) {
	
	$result = sort_informers($_POST['sort_inf'],$parent);
	if(!$result) {
		exit(FALSE);
	}
	exit(TRUE);
}

// получение массива каталога
$cat = catalog();

// получение динамичной части шаблона #content (важно, чтобы эта часть была раньше, чем мы подключаем шаблон)
$view = empty($_GET['view']) ? 'pages' : $_GET['view'];

switch($view){
    case('pages'):
        // страницы (контент)
        $pages = pages();
    break;
    
    case('edit_page'):
        // редактирование страницы
        $page_id = (int)$_GET['page_id']; 
        $get_page = get_page($page_id);
        
        if($_POST){
            if(edit_page($page_id)) redirect('?view=pages'); // возвращает на вид pages, если функция edit_page возвращает истину
                else redirect(); // возвращает на ту же страницу, где мы находились до отправки данных из формы (до нажатия кнопки "Сохранить"), если функция edit_page вернула ложь
        }
    break;
    
    case('add_page'):
        if($_POST){ // описание этого условия как на 40-й строке
            if(add_page()) redirect('?view=page');
            else redirect();
        }
    break;
    
    case('del_page'):
        $page_id = (int)$_GET['page_id'];
        del_page($page_id);
        redirect();
    break;
    
    case('news'):
        // этот кейс - все новости (архив новостей)
        
        // параметры для постраничной навигации
        $perpage = 3; // здесь мы указываем количество новостей, которое будет выводиться на 1 страницу
        if(isset($_GET['page'])){ // параметр page - это номер текущей страницы
            $page = (int)$_GET['page'];
            if($page < 1) $page = 1;
        }else{
            $page = 1; // если не существует $_GET['page']
        }
        $count_rows = count_news(); // получили общее количество новостей
        $pages_count = ceil($count_rows / $perpage); // округлённое количество страниц (в большую сторону)
        if(!$pages_count) $pages_count = 1; // минимум одна страница (проверка на 0-ые или отрицательные страницы, каких быть не должно)
        if($page > $pages_count) $page = $pages_count; // если пользователь введёт номер страницы, превышающий максимальное имеющееся количество страниц
        $start_pos = ($page - 1) * $perpage; // начальная позиция для запроса 
        // параметры для постраничной навигации
        
        $all_news = get_all_news($start_pos, $perpage);
    break;
    
    case('add_news'):
        if($_POST){
            if(add_news()) redirect('?view=news');
            else redirect();
        }
    break;
    
    case('edit_news'):
        $news_id = (int)$_GET['news_id'];
        $get_news = get_news($news_id);
        
        if($_POST){
            if(edit_news($news_id)) redirect('?view=news');
            else redirect();
        }
    break;
    
    case('del_news'):
        $news_id = (int)$_GET['news_id'];
        del_news($news_id);
        redirect();
    break;
    
    case('informers'):
        // информеры
        $informers = informer();
    break;
    
    case('add_link'):
        $informer_id = (int)$_GET['informer_id'];
        $informers = get_informers(); // здесь получим список всех информеров
        if($_POST){
            if(add_link()) redirect('?view=informers');
                else redirect();
        }
    break;
    
    case('edit_link'):        
        $link_id = (int)$_GET['link_id'];
        $get_link = get_link($link_id);
        $informers = get_informers(); // здесь получим список всех информеров
        
        if($_POST){
            if(edit_link($link_id)) redirect('?view=informers');
                else redirect(); 
        }
    break;
    
    case('del_link'):
        $link_id = (int)$_GET['link_id'];
        del_link($link_id);
        redirect();
    break;
    
    case('add_informer'):
        if($_POST){
            if(add_informer()) redirect('?view=informers');
                else redirect();
        }
    break;
    
    case('del_informer'):
        $informer_id = (int)$_GET['informer_id'];
        del_informer($informer_id);
        redirect();
    break;
    
    case('edit_informer'):
        $informer_id = (int)$_GET['informer_id'];
        $get_informer = get_informer($informer_id);
        
        if($_POST){
            if(edit_informer($informer_id)) redirect('?view=informers');
                else redirect();
        }
    break;
    
    case('brands'):
        
    break;
    
    case('add_brand'):
        if($_POST){
            if(add_brand()) redirect('?view=brands');
                else redirect();
        }
    break;
    
    case('edit_brand'):
        $brand_id = (int)$_GET['brand_id'];
        $parent_id = (int)$_GET['parent_id'];
        if($parent_id == $brand_id OR !$parent_id){ 
            // если категория родительская OR мы пришли в одноимённый вид из вида brands
            $brand_name = $cat[$brand_id][0];
        }else{
            // если дочерняя категория
            $brand_name = $cat[$parent_id]['sub'][$brand_id];
        }
        if($_POST){
            if($parent_id AND edit_brand($brand_id)){
                redirect("?view=cat&category=$brand_id");
            }elseif(edit_brand($brand_id)){
                redirect('?view=brands');
            }else{
                redirect();
            }
        }
    break;
    
    case('del_brand'):
        $brand_id = (int)$_GET['brand_id'];
        del_brand($brand_id);
        redirect();
    break;
    
    case('cat'):
        $category = (int)$_GET['category'];
        
        // параметры для постраничной навигации
        $perpage = 6; // здесь мы указываем количество товаров, которое будет выводиться на 1 страницу
        if(isset($_GET['page'])){ // параметр page - это номер текущей страницы
            $page = (int)$_GET['page'];
            if($page < 1) $page = 1;
        }else{
            $page = 1;
        }
        $count_rows = count_rows($category); // получили общее количество товаров (результат, который выдала после своей работы функция count_rows($category))
        $pages_count = ceil($count_rows / $perpage); // округлённое количество страниц
        if(!$pages_count) $pages_count = 1; // минимум одна страница (проверка на 0-ые или отрицательные страницы, каких быть не должно)
        if($page > $pages_count) $page = $pages_count; // если пользователь введёт номер страницы, превышающий максимальное имеющееся количество страниц
        $start_pos = ($page - 1) * $perpage; // начальная позиция для запроса 
        // параметры для постраничной навигации
        
        $brand_name = brand_name($category); // хлебные крохи
        
        $products = products($category, $start_pos, $perpage); // получаем массив товаров из модели
    break;
    
    case('add_product'):
        $brand_id = (int)$_GET['brand_id'];
        if($_POST){
            if(add_product($category)) redirect("?view=cat&category=$brand_id");
                else redirect();
        }
    break;
    
    case('edit_product'):
        $goods_id = (int)$_GET['goods_id']; 
        $get_product = get_product($goods_id);
        $brand_id = $get_product['goods_brandid'];
        // условие для вывода инпута базовой картинки товара (условие - если есть базовая картинка)
        if($get_product['img'] != 'no_image.jpg'){
            $baseimg = '<img src="'.PRODUCTIMG.$get_product['img'].'" class="delimg" rel="0" width="68" height="" alt="'.$get_product['img'].'">';
        }else{
            $baseimg = '<input type="file" name="baseimg" />';
        }
        // формирование условия и вывода для картинок галереи (условие - если есть картинки галереи)
        $imgslide = '';
        if($get_product['img_slide']){
            $images = explode("|", $get_product['img_slide']);
            foreach($images as $img){
                $imgslide .= "<img class='delimg' rel='1' alt='{$img}' src='".GALLERYIMG."thumbs/{$img}'>";
            }
        }
        
        if($_POST){
            if(edit_product($goods_id)) redirect("?view=cat&category=$brand_id");
                else redirect();
        }
    break;
    
    case('orders'):
        // строки кода для подтверждения заказа
        if(isset($_GET['confirm'])){
            $order_id = (int)$_GET['confirm'];
            if(confirm_order($order_id)){
                $_SESSION['answer'] = "<div class='success'>Заказ № {$order_id} подтверждён!</div>";
            }else{
                $_SESSION['answer'] = "<div class='error'>Произошла ошибка при подтверждении заказа №{$order_id}. Возможно, заказа с таким номером не существует, либо он уже подтверждён.</div>";
            }
            redirect('?view=orders');
        }
        // удаление заказа
        if(isset($_GET['del_order'])){
            $order_id = (int)$_GET['del_order'];
            if(del_order($order_id)){
                $_SESSION['answer'] = "<div class='success'>Заказ № {$order_id} удалён!</div>";
            }else{
                $_SESSION['answer'] = "<div class='error'>Произошла ошибка. Возможно, заказ № {$order_id} уже удалён либо не существует.</div>";
            }
            redirect('?view=orders');
        }
        
        // эти две строчки для того чтобы функция orders($status) выбрала либо необработанные заказы, либо все
        if($_GET['status'] == '0'){
            $status = " WHERE orders.status = '0'";
        }else{
            $status = NULL;
        }       
        
        // параметры для постраничной навигации
        $perpage = 6; // здесь мы указываем количество товаров, которое будет выводиться на 1 страницу
        if(isset($_GET['page'])){ // параметр page - это номер текущей страницы
            $page = (int)$_GET['page'];
            if($page < 1) $page = 1;
        }else{
            $page = 1;
        }
        $count_orders = count_orders($status); // получили общее количество заказов
        $pages_count = ceil($count_orders / $perpage); // округлённое количество страниц
        if(!$pages_count) $pages_count = 1; // минимум одна страница (проверка на 0-ые или отрицательные страницы, каких быть не должно)
        if($page > $pages_count) $page = $pages_count; // если пользователь введёт номер страницы, превышающий максимальное имеющееся количество страниц 
        $start_pos = ($page - 1) * $perpage; // начальная позиция для запроса
        
        $orders = orders($status, $start_pos, $perpage);
    break;
    
    case('show_order'):
        $order_id = (int)$_GET['order_id'];
        $show_order = show_order($order_id);
    break;
    
    case('users'):
        // параметры для постраничной навигации
        $perpage = 2; // здесь мы указываем количество товаров, которое будет выводиться на 1 страницу
        if(isset($_GET['page'])){ // параметр page - это номер текущей страницы
            $page = (int)$_GET['page'];
            if($page < 1) $page = 1;
        }else{
            $page = 1;
        }
        $count_users = count_users(); // получили общее количество пользователей
        $pages_count = ceil($count_users / $perpage); // округлённое количество страниц
        if(!$pages_count) $pages_count = 1; // минимум одна страница (проверка на 0-ые или отрицательные страницы, каких быть не должно)
        if($page > $pages_count) $page = $pages_count; // если пользователь введёт номер страницы, превышающий максимальное имеющееся количество страниц 
        $start_pos = ($page - 1) * $perpage; // начальная позиция для запроса
        
        $users = get_users($start_pos, $perpage);
    break;
    
    case('add_user'):
        $roles = get_roles(); // получаем список ролей
        if($_POST){
            if(add_user()) redirect('?view=users');
                else redirect();
        }
    break;
    
    case('edit_user'):
        $user_id = (int)$_GET['user_id'];
        $get_user = get_user($user_id);
        $roles = get_roles();
        $page = (int)$_GET['page'];
        if($_POST){
            if(edit_user($user_id)) redirect('?view=users&page='.$page);
            else redirect();
        }
    break;
    
    case('del_user'):
        $user_id = (int)$_GET['user_id'];
        $page = (int)$_GET['page'];
        del_user($user_id); redirect();
    break;
        
    default:
        // если из адресной строки получено имя несуществующего вида
        $view = 'pages';
        $pages = pages();
}

// подключаем header
include ADMIN_TEMPLATE.'header.php';

// подключаем leftbar
include ADMIN_TEMPLATE.'leftbar.php';

// подключаем pages (content)
include ADMIN_TEMPLATE.$view.'.php';

?>