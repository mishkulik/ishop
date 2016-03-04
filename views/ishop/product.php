<?php defined('ISHOP') or die('Access denied'); ?>
<?php if($goods): // если есть запрошенный товар (если в массиве $goods что-то есть) ?>
<div class="kroshka">
<?php if(count($brand_name) > 1): // мы работаем с категорией, у кт есть родитель (др. словами мы работаем с подкатегорией, субкатегорией и т.д.) ?>
    <a href="<?=PATH?>">Главная</a> / <a href="?view=cat&amp;category=<?=$brand_name[0]['brand_id']?>"><?=$brand_name[0]['brand_name']?></a> / <a href="?view=cat&amp;category=<?=$brand_name[1]['brand_id']?>"><?=$brand_name[1]['brand_name']?></a> / <span><?=$goods['name']?></span>
<?php elseif(count($brand_name) == 1): // это категория, не имеющая родителя?>
<a href="<?=PATH?>">Главная</a> / <a href="?view=cat&amp;category=<?=$brand_name[0]['brand_id']?>"><?=$brand_name[0]['brand_name']?></a> / <span><?=$goods['name']?></span>
<?php endif; ?>
</div> <!-- .kroshka -->

<div class="catalog-detail">
<h1><?=$goods['name']?></h1>
<img src="<?=PRODUCTIMG.$goods['img']?>" style="float:left;"/>
<div> <!-- Иконки -->
    <?php if($goods['hits']) echo '<img src="'.TEMPLATE.'images/ico-det-lider.jpg" alt="Лидеры продаж" />'; ?>
    <?php if($goods['new']) echo '<img src="'.TEMPLATE.'images/ico-det-new.jpg" alt="Новинка" />'; ?>
    <?php if($goods['sale']) echo '<img src="'.TEMPLATE.'images/ico-det-sale.jpg" alt="Распродажа" />'; ?>
</div> <!-- Иконки -->
<div class="short-opais">
<?=$goods['anons']?>
    <p class="price-detail">Цена : <span><?=$goods['price']?></span></p>
    <a href="?view=addtocart&amp;goods_id=<?=$goods['goods_id']?>"><img class="addtocard-index" src="<?=TEMPLATE?>images/addcard-detail.jpg" alt="Добавить в корзину" /></a>
</div> <!-- .short-opais -->

<div class="clr"></div>

<!-- Блок галереи -->
<?php if($goods['img_slide']): // если есть картинки галереи ?>
<div class="item_gallery">
    <div class="item_thumbs">
    <?php foreach($goods['img_slide'] as $item): ?>
        <a rel="gallery" title="<?=$goods['name']?>" href="<?=GALLERYIMG?>photos/<?=$item?>"><img src="<?=GALLERYIMG?>thumbs/<?=$item?>" /></a> 
    <?php endforeach; ?>
    </div><!-- .item_thumbs -->
</div> <!-- .item_gallery -->
<?php endif; ?>
<!-- Блок галереи -->

<div class="long-opais">
    <h3>Описание телефона <?=$goods['name']?>:</h3>
    <?=$goods['content']?>            
</div> <!-- .long-opais -->

<div class="clr"></div>

</div> <!-- .catalog-detail -->
<?php else: ?>
    <div class="error">Такого товара нет. Либо он закончился только что.</div>
<?php endif; ?>