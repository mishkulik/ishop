<?php defined('ISHOP') or die('Access denied'); ?>
<div class="catalog-index">
    <h1>Распродажа</h1>
<?php if($eyestoppers): ?>
    <?php foreach($eyestoppers as $eyestopper): ?>
        <div class="product-index">
            <h2><a href="?view=product&amp;goods_id=<?=$eyestopper['goods_id']?>"><?=$eyestopper['name']?></a></h2>    
            <div class="product-table-img">
            <a href="?view=product&amp;goods_id=<?=$eyestopper['goods_id']?>"><img src="<?=PRODUCTIMG?><?=$eyestopper['img']?>" alt="" /></a>
            </div>
            <p>Цена : <span><?=$eyestopper['price']?></span></p>
            <a href="?view=addtocart&amp;goods_id=<?=$eyestopper['goods_id']?>"><img class="addtocard-index" src="<?=TEMPLATE?>images/addcard-index.jpg" alt="Добавить в корзину" /></a>
        </div> <!-- .product-index -->
    <?php endforeach; ?>
    <?php else: ?>
        <p>Здесь товаров пока нет!</p>
<?php endif; ?>
</div>