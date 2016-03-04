<?php defined('ISHOP') or die('Access denied');
// здесь файл с функциями является одновременно и моделью
/* ====Фильтрация входящих данных из админки==== */
function clear_admin($var){ // функция предотвращает попадание в БД ненужных символов (sql-инъекций)
    $var = mysql_real_escape_string($var);
    return $var;
}
/* ====Фильтрация входящих данных из админки==== */    


/* ==== Подсвечивание активного пункта меню ==== */
function active_url($str = 'view=pages'){ // когда передаваемому в функцию параметру присваивается здесь значение, оно выступает в роли "по умолчанию"
    $uri = $_SERVER['QUERY_STRING']; // получаем параметры из адресной строки
    if(!$uri) $uri = 'view=pages'; // параметр по умолчаниюы
    $uri = explode("&", $uri); 
    //if(preg_match("#page=#", end($uri))) array_pop($uri); // отыскание и удаление последнего элемента массива $uri (а именно параметра пагинации - page)
    if(in_array($str, $uri)){
        // если в массиве параметров есть передаваемая параметром $str в функцию строка - тогда это активный пункт меню
        return "class='nav-activ'";
    }
}
/* ==== Подсвечивание активного пункта меню ==== */


/* ==== Ресайз картинок ==== */
function resize($target, $dest, $wmax, $hmax, $ext){
    /*
    $target - путь к оригинальному файлу
    $dest - пусть сохранения обработанного файла
    $wmax - максимальная ширина
    $hmax - максимальная высота
    $ext - расширение файла
    */
    
    list($w_orig, $h_orig) = getimagesize($target);
    $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная
    
    if(($wmax / $hmax) > $ratio){
        $wmax = $hmax * $ratio;
    }else{
        $hmax = $wmax / $ratio;
    }
    
    $img = "";
    // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
    switch($ext){
        case("gif"):
            $img = imagecreatefromgif($target);
        break;
        case("png"):
            $img = imagecreatefrompng($target);
        break;
        default:
            $img = imagecreatefromjpeg($target);
    }
    $newImg = imagecreatetruecolor($wmax, $hmax); // создаём оболочку для новой картинки
    
    if($ext == "png"){
        imagesavealpha($newImg, true); // сохранение альфа канала
        $transPng = imagecolorallocatealpha($newImg,0,0,0,127); // добавляем прозрачность
        imagefill($newImg, 0, 0, $transPng); // заливка
    }
    
    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и рисайзим изображение
    switch($ext){
        case("gif"):
            imagegif($newImg, $dest);
        break;
        case("png"):
            imagepng($newImg, $dest);
        break;
        default:
            imagejpeg($newImg, $dest);
    }
    imagedestroy($newImg);
}
/* ==== Ресайз картинок ==== */


/* ====Каталог - получение массива==== */
function catalog(){
    $query = "SELECT * FROM brands ORDER BY parent_id, brand_name";
    $res = mysql_query($query) or die(mysql_query());
    
    // массив категорий
    $cat = array(); // Андрей рекомендует всегда всегда вот так явно объявлять массив
    while($row = mysql_fetch_assoc($res)){
        if(!$row['parent_id']){
            $cat[$row['brand_id']][] = $row['brand_name']; // $cat[1][0] = $row['brand_name']
        }else{
            $cat[$row['parent_id']]['sub'][$row['brand_id']] = $row['brand_name']; // $cat[1]['sub'][6]
        }
    }
    return $cat;
}
/* ====Каталог - получение массива==== */

/* ====Страницы==== */
function pages(){
    $query = "SELECT page_id, title, position FROM pages ORDER BY position";
    $res = mysql_query($query);
    
    $pages = array();
    while($row = mysql_fetch_assoc($res)){
        $pages[] = $row;
    }
    return $pages;
}
/* ====Страницы==== */


/* ==== Отдельная страница ==== */
function get_page($page_id){
    $query = "SELECT * FROM pages WHERE page_id = $page_id";
    $res = mysql_query($query);
    $page = array();
    $page = mysql_fetch_assoc($res);
    return $page;
}
/* ==== Отдельная страница ==== */


/* ==== Редактирование страницы ==== */
function edit_page($page_id){
    $title = trim($_POST['title']);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $position = (int)$_POST['position'];
    $text = trim($_POST['text']);
    
    if(empty($title)){
        // если администратор не введёт названия
        $_SESSION['edit_page']['res'] = "<div class='error'>Должно быть введено название страницы.</div>";
        return false;
    }else{
        $title = clear_admin($title);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $text = clear_admin($text);
        
        $query = "UPDATE pages SET
                    title = '$title',
                    keywords = '$keywords',
                    description = '$description',
                    position = $position,
                    text = '$text'
                        WHERE page_id = $page_id";
        $res = mysql_query($query) or die(mysql_error());
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Изменения успешно сохранены!</div>";
            return true;
        }else{
            $_SESSION['edit_page']['res'] = "<div class='error'>Вы не внесли никаких изменений (либо на сервере произошла ошибка).</div>";
            return false;
        } 
    }
// даннoe условие функции возвращает либо true (тогда выводится сессионная переменная $_SESSION['answer']), либо false (тогда выводится сессионная переменная $_SESSION['edit_page']['res'])
} 
/* ==== Редактирование страницы ==== */


/* ==== Добавление страницы ==== */
function add_page(){
    $title = trim($_POST['title']);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $position = (int)$_POST['position']; // эта строчка в поле Позиция вернёт всегда значение 0, если ничего не указывать
    $text = trim($_POST['text']);
    
    if(empty($title)){
        // если админ не заполнил название
        $_SESSION['add_page']['res'] = "<div class='error'>Должно быть название страницы!</div>";
        $_SESSION['add_page']['keywords'] = $keywords;
        $_SESSION['add_page']['description'] = $description;
        $_SESSION['add_page']['position'] = $position;
        $_SESSION['add_page']['text'] = $text;
        return false; // это false здесь добавляется для того, чтобы функция add_page при невыполнении этого условия, а именно когда пуста $title, возвращала ложь, для того чтобы редирект оставлял нас на той же странице. Так как в контроллере есть услоие возврата истины/лжи функции add_page.
    }else{
        $title = clear_admin($title);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $text = clear_admin($text); // $position сюда не включили, так как значение этой переменной приводится к целочисленному виду и фильтровать его нет смысла
        
        $query = "INSERT INTO pages SET title='$title', keywords='$keywords', description='$description', position=$position, text='$text'";
        
        $res = mysql_query($query) or die(mysql_error());
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Новая страница добавлена!</div>";
            return true; 
        }else{
            $_SESSION['add_page']['res'] = "<div cladd='error'>Произошла ошибка при добавлении страницы.</div>";
            return false;
        }
    }
    
}
/* ==== Добавление страницы ==== */


/* ==== Удаление страницы ==== */
function del_page($page_id){
    $query = "DELETE FROM pages WHERE page_id = $page_id";
    $res = mysql_query($query) or die(mysql_error());
    
    if(mysql_affected_rows() > 0){
        $_SESSION['answer'] = "<div class='success'>Страница удалена.</div>";
        return true;
    }else{
        $_SESSION['answer'] = "<div class='error'>Ошибка удаления страницы.</div>";
        return false;
    }
}
/* ==== Удаление страницы ==== */


/* ====Количество новостей==== */
function count_news(){
    $query = "SELECT COUNT(news_id) FROM news";
    $res = mysql_query($query);
    
    $count_news = mysql_fetch_row($res);
    return $count_news[0];
}
/* ====Количество новостей==== */


/* ====Архив новостей==== */
function get_all_news($start_pos, $perpage){
    $query = "SELECT news_id, title, anons, date FROM news ORDER BY date DESC LIMIT $start_pos, $perpage";
    $res = mysql_query($query);
    
    $all_news = array();
    while($row = mysql_fetch_assoc($res)){
        $all_news[] = $row;
    }
    
    return $all_news;
}
/* ====Архив новостей==== */


/* ==== Добавление новости ==== */
function add_news(){
    $title = trim($_POST['title']);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $anons = trim($_POST['anons']);
    $text = trim($_POST['text']);
    
    if(empty($title)){
        // если нет заголовка новости
        $_SESSION['add_news']['res'] = "<div class='error'>Должен быть введён заголовок новости.</div>";
        $_SESSION['add_news']['keywords'] = $keywords;
        $_SESSION['add_news']['description'] = $description;
        $_SESSION['add_news']['anons'] = $anons;
        $_SESSION['add_news']['text'] = $text;
        return false;
    }else{
        $title = clear_admin($title);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $anons = clear_admin($anons);
        $text = clear_admin($text);
        $date = date("Y-m-d");
        
        $query = "INSERT INTO news 
                    SET title='$title', keywords='$keywords', description='$description', anons='$anons', text='$text', date='$date'";
                    
        $res = mysql_query($query);
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Новость добавлена!</div>";
            return true;
        }else{
            $_SESSION['add_news']['res'] = "<div class='error'>Произошла ошибка при добавлении новости.</div>";
            return false;
        }
    }
    
}
/* ==== Добавление новости ==== */


/* ==== Отдельная новость ==== */
function get_news($news_id){
    $query = "SELECT * FROM news WHERE news_id = $news_id";
    $res = mysql_query($query);
    
    $news = array();
    $news = mysql_fetch_assoc($res);
    return $news;
}
/* ==== Отдельная новость ==== */


/* ==== Редактирование новости ==== */
function edit_news($news_id){
    $title = trim($_POST['title']);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $date = trim($_POST['date']);
    $anons = trim($_POST['anons']);
    $text = trim($_POST['text']);
    
    if(empty($title)){
        $_SESSION['edit_news']['res'] = "<div class='error'>Должен быть введён заголовок новости.</div>";
        return false;
    }else{
        $title = clear_admin($title);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $date = clear_admin($date);
        $anons = clear_admin($anons);
        $text = clear_admin($text);
        
        $query = "UPDATE news SET
                    title = '$title',
                    keywords = '$keywords',
                    description = '$description',
                    date = '$date',
                    anons = '$anons',
                    text ='$text'
                        WHERE news_id = $news_id";
        $res = mysql_query($query) or die(mysql_error());
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Изменения успешно сохранены!</div>";
            return true;
        }else{
            $_SESSION['edit_news']['res'] = "<div class='error'>Вы не внесли никаких изменений (либо на сервере произошла ошибка)</div>";
            return false;
        }              
    }
}
/* ==== Редактирование новости ==== */


/* ==== Удаление Новости ==== */
function del_news($news_id){
    $query = "DELETE FROM news WHERE news_id = $news_id";
    $res = mysql_query($query) or die(mysql_error());
    
    if(mysql_affected_rows() >0){
        $_SESSION['answer'] = "<div class='success'>Новость удалена.</div>";
        return true;
    }else{
        $_SESSION['answer'] = "<div class='error'>Ошибка удаления новости.</div>";
        return false;
    }
}
/* ==== Удаление новости ==== */


/* ====Информеры - получение массива для вида informers==== */
function informer(){
    $query = "SELECT * FROM links
                RIGHT JOIN informers ON
                    links.parent_informer = informers.informer_id
                        ORDER BY informer_position, links_position";
    $res = mysql_query($query) or die(mysql_query());
    
    $informers = array();
    $name = ''; // флаг имени информера
    while($row = mysql_fetch_assoc($res)){
        if($row['informer_name'] != $name){ // если такого информера в массиве ещё нет
            $informers[$row['informer_id']][] = $row['informer_name']; // добавляем информер в массив
            $informers[$row['informer_id']]['position'] = $row['informer_position'];
            $informers[$row['informer_id']]['informer_id'] = $row['informer_id'];
            $name = $row['informer_name'];
        }
        if($informers[$row['parent_informer']])
        $informers[$row['parent_informer']]['sub'][$row['link_id']] = $row['link_name']; // заносим страницы (ссылки) в информер
    }
    return $informers;
}
/* ====Информеры - получение массива для вида informers==== */


/* ==== Массив информеров для списка на вид informers  ==== */
function get_informers(){
    $query = "SELECT * FROM informers";
    $res = mysql_query($query) or die(mysql_error());
    
    $informers = array();
    while($row = mysql_fetch_assoc($res)){
        $informers[] = $row;
    }
    
    return $informers;
}
/* ==== Массив информеров для списка на вид informers  ==== */


/* ==== Добавление страницы информера ==== */
function add_link(){
    $link_name = trim($_POST['link_name']);
    $parent_informer = (int)$_POST['parent_informer'];
    $links_position = (int)$_POST['links_position'];
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $text = trim($_POST['text']);
    
    if(empty($link_name)){
        // если не введено имя страницы информера
        $_SESSION['add_link']['res'] = "<div class='error'>Должно быть введено название страницы.</div>";
        $_SESSION['add_link']['keywords'] = $keywords;
        $_SESSION['add_link']['description'] = $description;
        $_SESSION['add_link']['links_position'] = $links_position;
        $_SESSION['add_link']['text'] = $text;
        return false;
    }else{
        $link_name = clear_admin($link_name);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $text = clear_admin($text);
        
        $query = "INSERT INTO links SET link_name='$link_name', parent_informer=$parent_informer, links_position=$links_position, keywords='$keywords', description='$description', text='$text'";
        $res = mysql_query($query) or die(mysql_error());
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Страница добавлена в информер!</div>";
            return true;
        }else{
            $_SESSION['add_link']['res'] = "<div class='error'>Произошла ошибка при добавлении страницы в информер.</div>";
            return false;
        } 
    }  
}
/* ==== Добавление страницы информера ==== */


/* ==== Получение данных для страницы информера ==== */
function get_link($link_id){
    $query = "SELECT * FROM links WHERE link_id = $link_id";
    $res = mysql_query($query);
    
    $link = array();
    $link = mysql_fetch_assoc($res);
    
    return $link;
}
/* ==== Получение данных для страницы информера ==== */


/* ==== Редактирование страницы информера ==== */
function edit_link($link_id){
    $link_name = trim($_POST['link_name']);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $parent_informer = (int)$_POST['parent_informer'];
    $links_position = (int)$_POST['links_position'];
    $text = trim($_POST['text']);
    
    if(empty($link_name)){
        // если не ввели название страницы информера
        $_SESSION['edit_link']['res'] = "<div class='error'>Должно быть введено название страницы информера.</div>";
        return false;
    }else{
        $link_name = clear_admin($link_name);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $text = clear_admin($text);
        
        $query = "UPDATE links SET
                    link_name = '$link_name',
                    keywords = '$keywords',
                    description = '$description',
                    parent_informer = $parent_informer,
                    links_position = $links_position,
                    text = '$text'
                        WHERE link_id = $link_id";
        $res = mysql_query($query);
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Изменения успешно сохранены.</div>";
            return true;
        }else{
            $_SESSION['edit_link']['res'] = "<div class='error'>Вы не внесли никаких изменений (либо на сервере произошла ошибка).</div>";
            return false;
        }
    }  
}
/* ==== Редактирование страницы информера ==== */


/* ==== Удаление страницы информера ==== */
function del_link($link_id){
    $query = "DELETE FROM links WHERE link_id = $link_id";
    $res = mysql_query($query);
    
    if(mysql_affected_rows() > 0){
        $_SESSION['answer'] = "<div class='success'>Страница информера удалена.</div>";
    }else{
        $_SESSION['answer'] = "<div class='error'>Ошибка при удалении страницы.</div>";
    }
}   
/* ==== Удаление страницы информера ==== */


/* ==== Добавление информера ====*/
function add_informer(){
    $informer_name = clear_admin(trim($_POST['informer_name']));
    $informer_position = (int)$_POST['informer_position'];
    
    if(empty($informer_name)){
        $_SESSION['add_informer']['res'] = "<div class='error'>Должно быть введено название информера.</div>";
        return false;
    }else{        
        $query = "INSERT INTO informers SET informer_name = '$informer_name', informer_position = $informer_position";
        $res = mysql_query($query);
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Информер успешно добавлен.</div>";
            return true;
        }else{
            $_SESSION['add_informer']['res'] = "<div class='error'>Произошла ошибка при добавлении информера.</div>";
            return false;
        }   
    }    
}
/* ==== Добавление информера ====*/


/* ==== Удаление информера ==== */
function del_informer($informer_id){
    // удаление страниц информера
    
    mysql_query("DELETE FROM links WHERE parent_informer = $informer_id");
    
    // удаляем сам информер
    $query = "DELETE FROM informers WHERE informer_id = $informer_id";
    $res = mysql_query($query);
    
    if(mysql_affected_rows() > 0){
        $_SESSION['answer'] = "<div class='success'>Информер удалён.</div>";
    }else{
        $_SESSION['answer'] = "<div class='error'>Произошла ошибка при удалении информера.</div>";
    }
}
/* ==== Удаление информера ==== */


/* ==== Получение данных о выбранном информере для вида edit_informer ==== */
function get_informer($informer_id){
    $query = "SELECT * FROM informers WHERE informer_id = $informer_id";
    $res = mysql_query($query);
    
    $informers = array();
    $informers = mysql_fetch_assoc($res);
    return $informers;
}
/* ==== Получение данных о выбранном информере для вида edit_informer ==== */


/* ==== Редактирование информера ==== */
function edit_informer($informer_id){
    $informer_name = clear_admin(trim($_POST['informer_name']));
    $informer_position = (int)$_POST['informer_position'];
    
    if(empty($informer_name)){
        $_SESSION['edit_informer']['res'] = "<div class='error'>Должно быть введено название информера.</div>";
        return false;
    }else{
        $query = "UPDATE informers SET informer_name = '$informer_name', informer_position = $informer_position WHERE informer_id = $informer_id";
        $res = mysql_query($query);
        
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Информер редактирован успешно!</div>";
            return true;
        }else{
            $_SESSION['edit_informer']['res'] = "<div class='error'>При редактировании информера возникла ошибка.</div>";
            return false;
        }
    }
}
/* ==== Редактирование информера ==== */


/* ==== Добавление категории (брэнда) ==== */
function add_brand(){
    $brand_name = clear_admin(trim($_POST['brand_name']));
    $parent_id = (int)$_POST['parent_id'];
    
    if(empty($brand_name)){
        $_SESSION['add_brand']['res'] = "<div class='error'>Должно быть введено название категории.</div>";
        return false;
    }else{
        // проверяем, а нет ли такой категории на одном уровне и в одной родительской категории
        $query = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND parent_id = $parent_id";
        $res = mysql_query($query);
        
        if(mysql_num_rows($res) > 0){
            $_SESSION['add_brand']['res'] = "<div class='error'>Категория с таким названием уже есть.</div>";
            return false;
        }else{
            $query = "INSERT INTO brands SET brand_name = '$brand_name', parent_id = $parent_id";
            $res = mysql_query($query);
            
            if(mysql_affected_rows() > 0){
                $_SESSION['answer'] = "<div class='success'>Категория успешно добавлена!</div>";
                return true;
            }else{
                $_SESSION['add_brand']['res'] = "<div class='error'>При добавлении категории произошла ошибка.</div>";
                return false;
            }
        }
    }
}
/* ==== Добавление категории (брэнда) ==== */


/* ==== Редактирование категории (брэнда) ==== */
function edit_brand($brand_id){
    $brand_name = clear_admin(trim($_POST['brand_name']));
    $parent_id = (int)$_POST['parent_id'];
    
    if(empty($brand_name)){
        $_SESSION['edit_brand']['res'] = "<div class='error'>Должно быть введено имя категории.</div>";
        return false;
    }else{
        // проверяем, нет ли уже такой категории
        $query = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND parent_id = $parent_id";
        $res = mysql_query($query);
        
        if(mysql_num_rows($res) > 0){
            $_SESSION['edit_brand']['res'] = "<div class='error'>Категория с таким названием уже есть.</div>";
            return false;
        }else{
            $query = "UPDATE brands SET brand_name = '$brand_name', parent_id = $parent_id WHERE brand_id = $brand_id";
            $res = mysql_query($query);
            
            if(mysql_affected_rows() > 0){
                $_SESSION['answer'] = "<div class='success'>Категория редактирована успешно!</div>";
                return true;
            }else{
                $_SESSION['edit_brand']['res'] = "<div class='error'>При редактировании категории произошла ошибка.</div>";
                return false;
            }
        }
    }
}
/* ==== Редактирование категории (брэнда) ==== */


/* ==== Удаление категории (брэнда) ==== */
function del_brand($brand_id){
    // проверка категории на наличие в ней подкатегорий. По задумке удаляем только ту категорию, в которой нет дочерних
    $query = "SELECT COUNT(*) FROM brands WHERE parent_id = $brand_id";
    $res = mysql_query($query);
    $row = mysql_fetch_row($res);
    
    if($row[0]){ // если в условие попадает истина, то есть когда у категории есть дочерние категории
        $_SESSION['answer'] = "<div class='error'>Данную категорию удалить нельзя, так как она имеет подкатегории.</div>";
    }else{
        mysql_query("DELETE FROM goods WHERE goods_brandid = $brand_id");
        mysql_query("DELETE FROM brands WHERE brand_id = $brand_id");
        $_SESSION['answer'] = "<div class='success'>Категория успешно удалена!</div>";
    }
}
/* ==== Удаление категории (брэнда) ==== */


/* ==== Получение количества товаров для навигации ====*/
function count_rows($category){
    $query = "(SELECT COUNT(goods_id) as count_rows
                   FROM goods
                       WHERE goods_brandid = $category)
              UNION  
              (SELECT COUNT(goods_id) as count_rows 
                   FROM goods
                       WHERE goods_brandid IN
                    (
                        SELECT brand_id FROM brands WHERE parent_id = $category
                    ))";
    $res = mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_assoc($res)){
        if($row['count_rows']) $count_rows = $row['count_rows'];
    }
    return $count_rows;
}
/* ==== Получение количества товаров для навигации ====*/


/* ====Получение названий для хлебных крох==== */
function brand_name($category){ // первая часть запроса sql этой функции работает с дочерними категориями, то есть вытаскивает данные родителя дочерней категории. Вторая часть вытаскивает родительские категории, когда первая часть запроса не сработает, то есть когда parent_id = 0. 
    $query = "(SELECT brand_id, brand_name FROM brands
                WHERE brand_id = 
                    (SELECT parent_id FROM brands WHERE brand_id = $category)
                ) 
                UNION
                    (SELECT brand_id, brand_name FROM brands WHERE brand_id = $category)";
    $res = mysql_query($query);
    $brand_name = array();
    while($row = mysql_fetch_assoc($res)){
        $brand_name[] = $row; 
    }
    return $brand_name;
}
/* ====Получение названий для хлебных крох==== */


/* ====Получение массива товаров по категории==== */
function products($category, $start_pos, $perpage){
    $query = "(SELECT goods_id, name, img, anons, price, hits, new, sale, date, visible
                   FROM goods
                       WHERE goods_brandid = $category)
              UNION  
              (SELECT goods_id, name, img, anons, price, hits, new, sale, date, visible 
                   FROM goods
                       WHERE goods_brandid IN
                    (
                        SELECT brand_id FROM brands WHERE parent_id = $category
                    )
              ) LIMIT $start_pos, $perpage";
    $res = mysql_query($query) or die(mysql_error());
    
    $products = array();
    while($row = mysql_fetch_assoc($res)){
        $products[] = $row;
    }
    return $products; 
}
/* ====Получение массива товаров по категории==== */


/* ==== Добавление товара ====*/
function add_product(){
    $name = trim($_POST['name']);
    $price = round(floatval(preg_replace("#,#", ".", $_POST['price'])), 2);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $goods_brandid = (int)$_POST['goods_brandid'];
    $anons = trim($_POST['anons']);
    $content = trim($_POST['content']);
    $new = (int)$_POST['new'];
    $hits = (int)$_POST['hits'];
    $sale = (int)$_POST['sale'];
    $visible = (int)$_POST['visible'];
    $date = date("Y-m-d");
    
    if(empty($name)){
        $_SESSION['add_product']{'res'} = "<div class='error'>У товара должно быть введено его название.</div>";
        $_SESSION['add_product']['price'] = $price; 
        $_SESSION['add_product']['keywords'] = $keywords;
        $_SESSION['add_product']['description'] = $description;
        $_SESSION['add_product']['anons'] = $anons;
        $_SESSION['add_product']['content'] = $content; 
        $_SESSION['add_product']['date'] = $date;  
        return false;
    }else{
        $name = clear_admin($name);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $anons = clear_admin($anons);
        $content = clear_admin($content);
        
        $query = "INSERT INTO goods 
                    SET name = '$name',
                        keywords = '$keywords',
                        description = '$description',
                        goods_brandid = $goods_brandid,
                        anons = '$anons',
                        content = '$content',
                        visible = '$visible',
                        hits = '$hits',
                        new = '$new',
                        sale = '$sale',
                        price = $price,
                        date = '$date'";
        $res = mysql_query($query) or die(mysql_error());
        
        if(mysql_affected_rows() > 0){
            /* скрипт загрузки базовой картинки */
            $id = mysql_insert_id(); // id последнего добавленного товара
            $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых main-типов, разрешённых к загрузке картинок
            
            if($_FILES['baseimg']['name']){
                $baseimgExt = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['baseimg']['name'])); // в эту переменную попадает расширение картинки
                $baseimgName = "{$id}.{$baseimgExt}"; // новое имя картинки
                $baseimgTmpName = $_FILES['baseimg']['tmp_name']; // временное имя файла (картинки)
                $baseimgSize = $_FILES['baseimg']['size']; // вес файла
                $baseimgType = $_FILES['baseimg']['type']; // тип файла
                $baseimgError = $_FILES['baseimg']['error']; // если в этом элементе значение 0, значит всё ОК. Иначе - ошибка
                
                $error = "";
                // проверки при загрузке картинки
                if(!in_array($baseimgType, $types)) $error .= "Допустимые расширения - .gif, .jpg, .png <br />";
                if($baseimgSize > SIZE) $error .= "Максимальный размер файла - 1 Мб.";
                if($baseimgError) $error .= "Ошибка при загрузке файла. Возможно, файл слишком большой.";
                
                if(!empty($error)) $_SESSION['answer'] = "<div class='error'>Ошибка при загрузке базовой картинки товара.<br /> {$error} </div>";
                // если нет ошибок
                if(empty($error)){
                    if(@move_uploaded_file($baseimgTmpName, TMPIMG.$baseimgName)){
                        resize(TMPIMG.$baseimgName, BASEIMG.$baseimgName, 120, 185, $baseimgExt);
                        @unlink(TMPIMG.$baseimgName); // удаляем временный файл
                        mysql_query("UPDATE goods SET img = '$baseimgName' WHERE goods_id = $id"); // этим запросом убираем у поля img дефолтное значение no_image.jpg
                    }else{
                        $_SESSION['answer'] .= "<div class='error'>Не удалось переместить загруженную картинку. Проверьте права на папки в каталоге /userfiles/product_img/</div>";
                    }
                }                
            }
            /* конец скрипта по загрузке базовой картинки */
            
            //////////////////////////////////////////////////
            
            /* скрипт картинок галереи */
            if($_FILES['galleryimg']['name'][0]){
                for($i= 0; $i < count($_FILES['galleryimg']['name']); $i++){
                    $error = "";
                    if($_FILES['galleryimg']['name'][$i]){
                        // если есть файл
                        $galleryimgExt = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['galleryimg']['name'][$i])); 
                        $galleryimgName = "{$id}_{$i}.{$galleryimgExt}"; 
                        $galleryimgTmpName = $_FILES['galleryimg']['tmp_name'][$i]; 
                        $galleryimgSize = $_FILES['galleryimg']['size'][$i]; 
                        $galleryimgType = $_FILES['galleryimg']['type'][$i]; 
                        $galleryimgError = $_FILES['galleryimg']['error'][$i];
                        
                        // проверки при загрузке картинки
                        if(!in_array($galleryimgType, $types)){
                            $error .= "Допустимые расширения - .gif, .jpg, .png <br />";
                            $_SESSION['answer'] .= "<div class='error'>Ошибка при загрузке картинки {$_FILES['galleryimg']['name'][$i]}<br />{$error}</div>";
                            continue; // данный оператор прекращает выполнение текущей итерации, и происходит выполнение следующей итераци 
                        }
                        
                        if($galleryimgSize > SIZE){
                            $error .= "Максимальный размер файла - 1 Мб."; 
                            $_SESSION['answer'] .= "<div class='error'>Ошибка при загрузке картинки {$_FILES['galleryimg']['name'][$i]}<br />{$error}</div>";
                            continue;
                        }
                        
                        if($galleryimgError){
                            $error .= "Ошибка при загрузке файла ";
                            $_SESSION['answer'] .= "{$error}{$_FILES['galleryimg']['name'][$i]}.<br />Возможно, файл слишком большой.";
                            continue;
                        }
                        
                        // если ошибок нет
                        if(empty($error)){
                            if(@move_uploaded_file($galleryimgTmpName, "../userfiles/product_img/photos/$galleryimgName")){
                                resize("../userfiles/product_img/photos/$galleryimgName", "../userfiles/product_img/thumbs/$galleryimgName", 85, 85, $galleryimgExt);
                                if(!isset($galleryfiles)){
                                    $galleryfiles = $galleryimgName; // к примеру 15_0.jpg 
                                }else{
                                    $galleryfiles .= "|{$galleryimgName}"; // дописывание после 15_0.jpg|15_1.jpg 
                                }
                            }else{
                                $_SESSION['answer'] .= "<div class='error'>Не удалось переместить загруженную картинку. Проверьте права на папки в каталоге /userfiles/product_img/</div>";
                            }  
                        }                   
                    }
                }
                if(isset($galleryfiles)){
                    mysql_query("UPDATE goods SET img_slide = '$galleryfiles' WHERE goods_id = $id");
                }
            }
            /* конец скрипта загрузки картинок галереи */
            
            $_SESSION['answer'] .= "<div class='success'>Товар добавлен!</div>";
            return true;
        }else{
            $_SESSION['add_product']['res'] = "<div class='error'>Ошибка при добавлении товара.</div>";
            return false;
        }
    }
    
    
}
/* ==== Добавление товара ====*/


/* ==== Редактирование товара ==== */
function edit_product($id){
    $name = trim($_POST['name']);
    $price = round(floatval(preg_replace("#,#", ".", $_POST['price'])), 2);
    $keywords = trim($_POST['keywords']);
    $description = trim($_POST['description']);
    $goods_brandid = (int)$_POST['goods_brandid'];
    $anons = trim($_POST['anons']);
    $content = trim($_POST['content']);
    $new = (int)$_POST['new'];
    $hits = (int)$_POST['hits'];
    $sale = (int)$_POST['sale'];
    $visible = (int)$_POST['visible'];
    
    if(empty($name)){
        $_SESSION['edit_product']['res'] = "<div class='error'>У товара должно быть введено его название.</div>";
        return false;
    }else{
        $name = clear_admin($name);
        $keywords = clear_admin($keywords);
        $description = clear_admin($description);
        $anons = clear_admin($anons);
        $content = clear_admin($content);
        
        $query = "UPDATE goods SET
                    name = '$name',
                    price = $price,
                    keywords = '$keywords',
                    description = '$description',
                    goods_brandid = $goods_brandid,
                    anons = '$anons',
                    content = '$content',
                    new = '$new',
                    hits = '$hits',
                    sale = '$sale',
                    visible = '$visible'
                        WHERE goods_id = $id";
        $res = mysql_query($query) or die(mysql_error());
        /* Базовая картинка */
        $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых main-типов        
        if($_FILES['baseimg']['name']){            
            $baseimgExt = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES['baseimg']['name']));
            $baseimgName = "{$id}.{$baseimgExt}";
            $baseimgTmpName = $_FILES['baseimg']['tmp_name'];
            $baseimgSize = $_FILES['baseimg']['size'];
            $baseimgType = $_FILES['baseimg']['type'];
            $baseimgError = $_FILES['baseimg']['error'];
            
            $error = "";
            if(!in_array($baseimgType, $types)) $error .= "Допустимые расширения - .gif, .jpg, .png <br />";
            if($baseimgSize > SIZE) $error .= "Максимальный вес файла = 1 Мб";
            if($baseimgError) $error .= "Ошибка при загрузке файла. Возможно, файл слишком большой.";
            
            if(!empty($error)) $_SESSION['answer'] = "<div class='error'>Ошибка при загрузке базовой картинки товара. <br />{$error}</div>";
            // если нет ошибок
            if(empty($error)){
                if(@move_uploaded_file($baseimgTmpName, TMPIMG.$baseimgName)){
                    resize(TMPIMG.$baseimgName, BASEIMG.$baseimgName, 120, 185, $baseimgExt);
                    @unlink(TMPIMG.$baseimgName);
                    mysql_query("UPDATE goods SET img = '$baseimgName' WHERE goods_id = $id");
            }else{
                $_SESSION['answer'] = "<div class='error'>Не удалось переместить загруженную картинку. Проверьте права на папки в каталоге /userfiles/product_img/</div>";
            }                        
        } 
    }
        /* Базовая картинка */
        $_SESSION['answer'] .= "<div class='success'>Товар успешно редактирован!</div>";
        return true;        
    }
}
/* ==== Редактирование товара ==== */


/* ==== Получение данных товара  ==== */
function get_product($goods_id){
    $query = "SELECT * FROM goods WHERE goods_id = $goods_id";
    $res = mysql_query($query);
    
    $products = array(); 
    $products = mysql_fetch_assoc($res);
    return $products;
}
/* ==== Получение данных товара  ==== */


/* ==== AjaxUpload - загрузка картинок галереи ==== */
function upload_gallery_img($id){
    $uploaddir = '../userfiles/product_img/photos/'; // загружаем картинку, сначала её не обрабатывая
    $file = $_FILES['userfile']['name']; // имя загружаемой картинки
    $ext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $file)); // получаем расширение файла
    $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых main-типов,
    
    if($_FILES['userfile']['size'] > SIZE){ // если файл превышает размер, указанный в константе SIZE
        $res = array("answer" => "Ошибка. Максимальный вес файла - 1 Мб.");
        exit(json_encode($res)); // завершение работы функции с выводом в браузер (так нужно для ajax)
    }
    
    if($_FILES['userfile']['error']){
        $res = array("answer" => "Возможно файл слишком большой.");
        exit(json_encode($res));
    }
    
    if(!in_array($_FILES['userfile']['type'], $types)){
        $res = array("answer" => "Допустимые расширения - .gif, .jpg, .png");
        exit(json_encode($res));
    }
    
    $query = "SELECT img_slide FROM goods WHERE goods_id = $id";
    $res = mysql_query($query);
    $row = mysql_fetch_assoc($res);
    if($row['img_slide']){
        // если есть картинки в галерее
        $images = explode("|", $row['img_slide']);
        $lastimg = end($images); // возврат последнего элемента массива $images
        // получаем номер последней картинки
        $lastnum = preg_replace("#\d+_(\d+)\.\w+#", "$1", $lastimg);
        $lastnum += 1; // взяли то, что уже есть в переменной $lastnum и добавили туда единицу
        // формируем имя новой картинки
        $newimg = "{$id}_{$lastnum}.{$ext}";  
        $images = "{$row['img_slide']}|{$newimg}"; // строка для записи в БД              
    }else{
        // если нет картинок в галерее
        $newimg = "{$id}_0.{$ext}"; // имя новой картинки
        $images = $newimg; // строка для записи в БД
    }
    
    $uploadfile = $uploaddir.$newimg; 
    if(@move_uploaded_file($_FILES['userfile']['tmp'], $uploadfile)){
        resize($uploadfile, "../userfiles/product_img/thumbs/$newimg", 85, 85, $ext);
        mysql_query("UPDATE goods SET img_slide = '$images' WHERE goods_id = $id");
        $res = array("answer" => "ОК", "file" => $newimg);
        exit(json_encode($res));
    }
}
/* ==== AjaxUpload - загрузка картинок галереи ==== */


/* ==== Асинхронное удаление картинок ==== */
function del_img(){
    $goods_id = (int)$_POST['goods_id'];
    $img = clear_admin($_POST['img']);
    $rel = (int)$_POST['rel'];
    
    if(!$rel){
        // если удаляется базовая картинка
        $query = "UPDATE goods SET img = 'no_image.jpg' WHERE goods_id = $goods_id";
        mysql_query($query);
        if(mysql_affected_rows() >0){
            return '<input type="file" name="baseimg" />';
        }else return false; // написал без фигурных скобок     
    }else{
        // если удаляется картинка галереи
        $query = "SELECT img_slide FROM goods WHERE goods_id = $goods_id";
        $res = mysql_query($query);
        $row = mysql_fetch_assoc($res);
        // получаем картинки галереи в массив
        $images = explode("|", $row['img_slide']);
        foreach ($images as $item){
            // пропускаем удаляемую картинку
            if($item == $img) continue;
            // формируем строку с картинками
            if(!isset($galleryfiles)){
                $galleryfiles = $item;
            }else{
                $galleryfiles .= "|$item";
            }
        }
        mysql_query("UPDATE goods SET img_slide = '$galleryfiles' WHERE goods_id = $goods_id");
        if(mysql_affected_rows() >0){
            return true;
        }else{
            return false;
        }
    }
}
/* ==== Асинхронное удаление картинок ==== */


/* ==== Получение количества необработанных заказов ==== */
function count_new_orders(){
    $query = "SELECT COUNT(*) AS count FROM orders WHERE status = '0'";
    $res = mysql_query($query);
    $row = mysql_fetch_assoc($res);
    return $row['count'];
}
/* ==== Получение количества необработанных заказов ==== */


/* ==== Получение количества заказов ==== */
function count_orders($status){
    $query = "SELECT COUNT(order_id) FROM orders".$status;
    $res = mysql_query($query);
    $count_orders = mysql_fetch_row($res);
    return $count_orders[0];
}
/* ==== Получение количества заказов ==== */


/* ==== Получение списка всех заказов ==== */
function orders($status, $start_pos, $perpage){
    $query = "SELECT orders.order_id, orders.date, orders.status, customers.name 
                FROM orders
                LEFT JOIN customers
                    ON customers.customer_id = orders.customer_id".$status." ORDER BY order_id DESC LIMIT $start_pos, $perpage";
    $res = mysql_query($query);
    
    $orders = array();
    while($row = mysql_fetch_assoc($res)){
        $orders[] = $row;
    }
    return $orders;
}
/* ==== Получение списка всех заказов ==== */


/* ==== Просмотр заказа ==== */
function show_order($order_id){
    // таблица zakaz_tovar: name, price, quantity
    // таблица orders: date, prim
    // таблица customers: name, address, phone, email
    // таблица dostavka: name
    $query = "SELECT z.name, z.price, z.quantity,
                    o.date, o.prim, o.status,
                    c.name AS customer, c.address, c.phone, c.email,
                    d.name AS sposob
                        FROM zakaz_tovar z
                    LEFT JOIN orders o
                        ON z.orders_id = o.order_id
                    LEFT JOIN customers c
                        ON o.customer_id = c.customer_id
                    LEFT JOIN dostavka d
                        ON o.dostavka_id = d.dostavka_id
                            WHERE z.orders_id = $order_id";
    $res = mysql_query($query);

    $show_order = array();
    while($row = mysql_fetch_assoc($res)){
        $show_order[] = $row;
    }
    return $show_order;
}
/* ==== Просмотр заказа ==== */


/* ==== Подтверждение заказа ==== */
function confirm_order($order_id){
    $query = "UPDATE orders SET status = '1' WHERE order_id = $order_id";
    $res = mysql_query($query);
    
    if(mysql_affected_rows() > 0){
        return true;
    }else{
        return false;
    }
}
/* ==== Подтверждение заказа ==== */


/* ==== Удаление заказа ==== */
function del_order($order_id){
    mysql_query("DELETE FROM orders WHERE order_id = $order_id");
    mysql_query("DELETE FROM zakaz_tovar WHERE orders_id = $order_id");
    if(mysql_affected_rows() > 0){
        return true;
    }else{
        return false;
    }
}
/* ==== Удаление заказа ==== */


/* ==== Получение количества пользователей ==== */
function count_users(){
    $query = "SELECT COUNT(login) FROM customers"; // функция COUNT не считает значения ячеек таблицы БД, которые имеют значение NULL. Соответственно, данный запрос выберет пользователей, зарегистрированных на сайте.
    $res = mysql_query($query);
    $count_users = mysql_fetch_row($res);
    return $count_users[0];
}
/* ==== Получение количества пользователей ==== */


/* ==== Получение списка пользователей ==== */
function get_users($start_pos, $perpage){
    $query = "SELECT customer_id, name, login, email, name_role
                FROM customers 
                LEFT JOIN roles
                    ON customers.id_role = roles.id_role
                WHERE login IS NOT NULL
                    LIMIT $start_pos, $perpage";
    $res = mysql_query($query); 
    $users = array();
    while($row = mysql_fetch_assoc($res)){
        $users[] = $row;
    }
    return $users;
}
/* ==== Получение списка пользователей ==== */


/* ==== Получение списка ролей пользователей ==== */
function get_roles(){
    $query = "SELECT * FROM roles";
    $res = mysql_query($query);
    $roles = array();
    while($row = mysql_fetch_assoc($res)){
        $roles[] = $row;
    }
    return $roles;
}
/* ==== Получение списка ролей пользователей ==== */


/* ==== Добавление пользователя ==== */
function add_user(){
    $error = "";
    
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $id_role = (int)$_POST['id_role'];
    
    if(empty($name)) $error .= "<li>Не указано Имя</li>";
    if(empty($email)) $error .= "<li>Ну указан Email</li>";
    if(empty($login)) $error .= "<li>Не указан Логин</li>";
    if(empty($password)) $error .= "<li>Не указан Пароль</li>";
    
    if(empty($error)){
        // если все поля заполнены - проверяем, нет ли такого пользователя уже в БД
        $query = "SELECT customer_id FROM customers WHERE login = '".clear($login)."' LIMIT 1";
        $res = mysql_query($query) or die(mysql_error());
        $row = mysql_num_rows($res); // вернёт 1 - такой юзер есть, 0 - такого юзера нет
        if($row){
            // если такой юзер есть
            $_SESSION['add_user']['res'] = "<div class='error'>Пользователь с таким логином уже есть. Введите другой логин.</div>";
            $_SESSION['add_user']['login'] = $login;
            $_SESSION['add_user']['password'] = $password;
            $_SESSION['add_user']['name'] = $name;
            $_SESSION['add_user']['email'] = $email;
            $_SESSION['add_user']['id_role'] = $id_role;
            return false;
        }else{
            // если всё ок - регистрируем юзера
            $login = clear($login);
            $pass = md5($password);
            $name = clear($name);
            $email = clear($email);
            
            $query = "INSERT INTO customers SET
                            login = '$login',
                            password = '$pass',
                            name = '$name',
                            email = '$email',
                            id_role = $id_role";
            $res = mysql_query($query) or die(mysql_error());
            if(mysql_affected_rows() > 0){ // если запись добавлена
                $_SESSION['answer'] = "<div class='success'>Пользователь добавлен.</div>";
                return true;
            }else{
                // если произошла ошибка
                $_SESSION['add_user']['res'] = "<div class='error'>Ошибка при добавлении пользователя.</div>";
                $_SESSION['add_user']['login'] = $login;
                $_SESSION['add_user']['password'] = $password;
                $_SESSION['add_user']['name'] = $name;
                $_SESSION['add_user']['email'] = $email;
                $_SESSION['add_user']['id_role'] = $id_role;
                return false;
            }
        }
    }else{ // если не заполнены обязательные поля
        $_SESSION['add_user']['res'] = "<div class='error'>Не заполнены обязательные поля: <ul style='list-style:none;'>$error</ul></div>";
        $_SESSION['add_user']['login'] = $login;
        $_SESSION['add_user']['password'] = $password;
        $_SESSION['add_user']['name'] = $name;
        $_SESSION['add_user']['email'] = $email;
        $_SESSION['add_user']['id_role'] = $id_role;
        return false;
    }
}
/* ==== Добавление пользователя ==== */


/* ==== Получение данных пользователя ==== */
function get_user($user_id){
    $query = "SELECT name, email, phone, address, login, id_role FROM customers WHERE customer_id = $user_id";
    $res = mysql_query($query);
    $user = array();
    $user = mysql_fetch_assoc($res);
    return $user;
}
/* ==== Получение данных пользователя ==== */


/* ==== Редактирование пользователя ==== */
function edit_user($user_id){
    foreach($_POST as $key => $val){
        if($key == "x" OR $key == "y") continue; // выбрасываем из массива POST элементы x и y
        if($key == "password"){
            $val = trim($val);
            if(!empty($val)){
                $val = md5($val);
            }else{
                continue; // если в пароле пусто (либо там 0) - пропускаем такой элемент, чтобы в БД не попала захэшированная пустота
            }
        }else{
            $val = clear($val);
        }
        $data[$key] = $val; // в цикле сформировали ещё один массив
    }
    // разобъём массив $data на два массива: 1-ый - со значениями ключей, 2-ой - с самими значениями
    $fields = array_keys($data); // 1-ый массив полей (они же ключи) таблицы БД
    $values = array_values($data); // 2-ой массив значений
    
    for($i=0; $i < count($fields); $i++){
        $str .= "{$fields[$i]} = '{$values[$i]}', ";
    }
    $str = substr($str, 0, -2);
    $query = "UPDATE customers SET {$str} WHERE customer_id = $user_id";
    $res = mysql_query($query);
    if(mysql_affected_rows() > 0){
        $_SESSION['answer'] = "<div class='success'>Пользователь успешно редактирован!</div>";
        if($user_id == $_SESSION['auth']['user_id']){
            $_SESSION['auth']['admin'] = htmlspecialchars(clear($_POST['name']));
        }
        return true;
    }else{
        $_SESSION['edit_user']['res'] = "<div class='error'>Произошла ошибка при редактировании пользователя, либо Вы ничего ни изменили.</div>";
        return false;
    }
}
/* ==== Редактирование пользователя ==== */


/* ==== Удаление пользователя ==== */
function del_user($user_id){
    if($user_id == $_SESSION['auth']['user_id']){
        $_SESSION['answer'] = "<div class='error'>Невозможно удалить пользователя, под которым Вы авторизованы в данный момент.</div>";
    }else{
        $query = "DELETE FROM customers WHERE customer_id = $user_id";
        $res = mysql_query($query);
        if(mysql_affected_rows() > 0){
            $_SESSION['answer'] = "<div class='success'>Пользователь удалён.</div>";
        }else{
            $_SESSION['answer'] = "<div class='error'>Произошла ошибка при удалении пользователя.</div>";
        }
    }
}
/* ==== Удаление пользователя ==== */


/* ==== Сортировка страниц ==== */
function sort_pages($post) {

	$position = 1;
	foreach($post as $item){
		$res = mysql_query("UPDATE pages SET position = $position WHERE page_id = $item");
		if(!$res ||(mysql_affected_rows() == -1)) {
			return FALSE;
		}
		$position++;
	}
	
	$result = mysql_query("SELECT page_id, position FROM pages");
	if(!$result) {
		return FALSE;
	}
	$row = array();
	for($i = 0;$i < mysql_num_rows($result);$i++) {
		$row[] = mysql_fetch_assoc($result);
	}
	
	return $row;
}
/* ==== Сортировка страниц ==== */


/* ==== Сортировка ссылок ==== */
function sort_links($post,$parent) {

	$position = 1;
	foreach($post as $item){
		$res = mysql_query("UPDATE `links` SET `links_position`='{$position}' WHERE `link_id`='{$item}' AND `parent_informer` = '{$parent}'");
		if(!$res ||(mysql_affected_rows() == -1)) {
			return FALSE;
		}
		$position++;
	}
	
	$result = mysql_query("SELECT link_id,links_position FROM links WHERE `parent_informer` = '{$parent}' ORDER BY `links_position`");
	if(!$result) {
		return FALSE;
	}
	$row = array();
	for($i = 0;$i < mysql_num_rows($result);$i++) {
		$row[] = mysql_fetch_assoc($result);
	}
	return $row;
}
/* ==== Сортировка ссылок ==== */


/* ==== Сортировка информеров ==== */
function sort_informers($post) {

	$position = 1;
	foreach($post as $item){
		$res = mysql_query("UPDATE `informers` SET `informer_position`='{$position}' WHERE `informer_id`='{$item}'");
		if(!$res ||(mysql_affected_rows() == -1)) {
			return FALSE;
		}
		$position++;
	}
	return TRUE;
}
/* ==== Сортировка информеров ==== */












?>