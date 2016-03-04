<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<?php /*print_arr($cat);
print_arr($brand_name);*/
 ?>
	<h2>Редактирование каталога</h2>
<?php  
if(isset($_SESSION['answer'])){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
} 
?>
    <a href="?view=add_brand"><img class="add_kategory" src="<?=ADMIN_TEMPLATE?>images/add_kategory.jpg" alt="добавить категорию" /></a>
    
    <div class="crosh">
<?php if(count($brand_name) > 1): // условие для вывода родительской и дочерней категорий ?>
    	<p class="crosh-left"><a href="?view=brands" title="Все категории">Мобильные телефоны</a> / <a href="?view=cat&amp;category=<?=$brand_name[0]['brand_id']?>" title="Все товары <?=$brand_name[0]['brand_name']?>"><?=$brand_name[0]['brand_name']?></a> / <?=$brand_name[1]['brand_name']?></p>
<?php else: // условие для вывода родительской категории ?>
<p class="crosh-left"><a href="?view=brands" title="Все категории">Мобильные телефоны</a> / <?=$brand_name[0]['brand_name']?></p>
<?php endif; ?>
        <p class="crosh-right"><a href="?view=edit_brand&amp;brand_id=<?=$category?>&amp;parent_id=<?=$brand_name[0]['brand_id']?>" class="edit">изменить категорию</a>&nbsp; | &nbsp;<a href="?view=del_brand&amp;brand_id=<?=$category?>" class="del">удалить категорию</a></p>
    </div> <!-- .crosh -->
    
	<a href="?view=add_product&amp;brand_id=<?=$category?>"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_product.jpg" alt="добавить продукт" /></a>
    
<?php if($products): // если есть товары в категории ?>

<?php  
$col = 3; // количество ячеек в строке
$row = ceil((count($products) / $col)); // количество рядов 
$start = 0;  
?>
	<table class="tabl-kat" cellspacing="1">
<?php for($i = 0; $i < $row; $i++): // цикл вывода рядов ?>
	  <tr>
    <?php for($k = 0; $k < $col; $k++): // цикл вывода ячеек ?>
		<td>
        <?php if($products[$start]): // если есть товар ?>
        	<h2><?=$products[$start]['name']?><?php if(empty($products[$start]['visible'])) echo ' <div style="color:red;">(товар скрыт)</div>'; ?></h2>
            <div class="product-tabl-img">
                <img src="<?=PRODUCTIMG.$products[$start]['img']?>" alt="" />
            </div>
            <p><a href="?view=edit_product&amp;goods_id=<?=$products[$start]['goods_id']?>" class="edit">изменить</a>&nbsp; | &nbsp;<a href="?view=del_product&amp;goods_id=<?=$products[$start]['goods_id']?>" class="del">удалить</a></p>
        <?php else: // если нет товара ?>
        &nbsp;
        <?php endif; ?>
        <?php $start++; ?>
        </td>
    <?php endfor; // конец цикла вывода ячеек ?>			
	  </tr>
<?php endfor; // конец цикла вывода рядов ?>
	</table>
    
<?php else: // если нет товаров в категории ?>
    <p style="margin: 0 0 20px 0;">Здесь товаров пока нет.</p>
    
<?php endif; // конец условия: если есть товары в категории?>
	<a href="?view=add_product&amp;brand_id=<?=$category?>"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_product.jpg" alt="добавить продукт" /></a>
	
<?php if($pages_count > 1) pagination($page, $pages_count); ?>	
	

</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>