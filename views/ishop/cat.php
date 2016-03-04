<?php defined('ISHOP') or die('Access denied'); ?>
<div class="catalog-index">

    <div class="kroshka">
<?php if(count($brand_name) > 1): // мы работаем с категорией, у кт есть родитель (др. словами мы работаем с подкатегорией, субкатегорией и т.д.) ?>
    <a href="<?=PATH?>">Главная</a> / <a href="?view=cat&amp;category=<?=$brand_name[0]['brand_id']?>"><?=$brand_name[0]['brand_name']?></a> / <span><?=$brand_name[1]['brand_name']?></span>
<?php elseif(count($brand_name) == 1): // это категория, не имеющая родителя?>
<a href="<?=PATH?>">Главная</a> / <span><?=$brand_name[0]['brand_name']?></span>
<?php endif; ?>
    </div> <!-- .kroshka -->
    
    <div class="vid-sort">
   	Вид:
   	    <a href="#" id="grid" class="grid_list"><img src="<?=TEMPLATE?>images/vid-tab1.gif" title="Табличный вид" alt="Табличный вид" /></a>
        <a href="#" id="list" class="grid_list"><img src="<?=TEMPLATE?>images/vid-line.gif" title="Линейный вид" alt="Линейный вид" /></a>
        &nbsp;&nbsp;
    Сортировать по:&nbsp;
        <a id="param_order" class="sort-top"><?=$order?></a>
            <div class="sort-wrap">
                <?php foreach($order_p as $key => $value): ?>
                    <?php if($value[0] == $order) continue; ?>
<a href="?view=cat&amp;category=<?=$category?>&amp;order=<?=$key?>&amp;page=<?=$page?>" class="sort-bot"><?=$value[0]?></a>
                <?php endforeach; ?>
            </div> 
    </div> <!-- .vid-sort -->
<?php if($products): // если получены товары категории ?>
<?php foreach($products as $product): ?>
<?php if(!isset($_COOKIE['display']) OR $_COOKIE['display'] == 'grid'): // если вид - сетка ?>
<!-- Табличный вид продуктов -->
<div class="product-table">
    <h2><a href="?view=product&amp;goods_id=<?=$product['goods_id']?>"><?=$product['name']?></a></h2>
    <div class="product-table-img-main">
       	<div class="product-table-img">
   	        <a href="?view=product&amp;goods_id=<?=$product['goods_id']?>"><img src="<?=PRODUCTIMG?><?=$product['img']?>" alt="" /></a>
            <div> <!-- Иконки hits, new, sale -->
                <?php if($product['hits']) echo '<img src="'.TEMPLATE.'images/ico-cat-lider.png" alt="Лидеры продаж" />'; ?>
                <?php if($product['new']) echo '<img src="'.TEMPLATE.'images/ico-cat-new.png" alt="Новинка" />'; ?>
                <?php if($product['sale']) echo '<img src="'.TEMPLATE.'images/ico-cat-sale.png" alt="Распродажа" />'; ?>
            </div> <!-- Иконки hits, new, sale -->
        </div>
    </div><!-- .product-table-img-main --> 
    <p class="cat-table-more"><a href="?view=product&amp;goods_id=<?=$product['goods_id']?>">подробнее...</a></p>
    <p>Цена : <span><?=$product['price']?></span></p>
    <a href="?view=addtocart&amp;goods_id=<?=$product['goods_id']?>"><img class="addtocard-index" src="<?=TEMPLATE?>images/addcard-table.jpg" alt="Добавить в корзину" /></a>               
</div> <!-- .product-table -->
<!-- Табличный вид продуктов -->
<?php else: // если линейный вид ?>
<!-- Линейный вид продуктов -->
<div class="product-line">
	<div class="product-line-img">
    	<a href="?view=product&amp;goods_id=<?=$product['goods_id']?>"><img src="<?=PRODUCTIMG?><?=$product['img']?>" width="48" alt="" /></a>
    </div>
    <div class="product-line-price">
    	<p>Цена : <span><?=$product['price']?></span></p>
        <a href="?view=addtocart&amp;goods_id=<?=$product['goods_id']?>"><img class="addtocard-index" src="<?=TEMPLATE?>images/addcard-line.jpg" alt="Добавить в корзину" /></a>
        <div> <!-- Иконки -->
            <?php if($product['hits']) echo '<img src="'.TEMPLATE.'images/ico-line-lider.jpg" alt="Лидеры продаж" />'; ?>
            <?php if($product['new']) echo '<img src="'.TEMPLATE.'images/ico-line-new.jpg" alt="Новинка" />'; ?>
            <?php if($product['sale']) echo '<img src="'.TEMPLATE.'images/ico-line-sale.jpg" alt="Распродажа" />'; ?>
        </div> <!-- Иконки -->
        <p class="cat-line-more"><a href="?view=product&amp;goods_id=<?=$product['goods_id']?>">подробнее...</a></p>
    </div>
    <div class="product-line-opis">
    	<h2><a href="?view=product&amp;goods_id=<?=$product['goods_id']?>"><?=$product['name']?></a></h2>
        <p><?=$product['anons']?></p>
    </div>
</div>
<!-- Линейный вид продуктов -->
<?php endif; // конец условия if($switch) (переключателя видов) ?>
<?php endforeach; ?>
<div class="clr"></div>
<?php if($pages_count > 1) pagination($page, $pages_count); ?>
<?php else: ?>
    <p>Здесь товаров пока нет, либо они закончились только что.</p>
<?php endif; ?>
<a name="nav"></a>
</div> <!-- .catalog-index -->