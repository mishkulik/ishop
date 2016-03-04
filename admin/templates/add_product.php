<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<?php //print_arr($cat); ?>
	<h2>Добавление товара</h2>
<?php  
if(isset($_SESSION['add_product']['res'])){
    echo $_SESSION['add_product']['res'];
}?>
    <form action="" method="post" enctype="multipart/form-data">			
    	<table class="add_edit_page" cellspacing="0" cellpadding="0">
    	  <tr>
    		<td class="add-edit-txt">Название товара:</td>
    		<td><input class="head-text" type="text" name="name" /></td>
    	  </tr>
          
          <tr>
              <td class="add-edit-txt">Цена:</td>
              <td><input class="head-text" type="text" name="price" value="<?=$_SESSION['add_product']['price']?>" /></td>
          </tr>
          
          <tr>
            <td class="add-edit-txt">Ключевые слова:</td>
            <td><input class="head-text" type="text" name="keywords" value="<?=htmlspecialchars($_SESSION['add_product']['keywords'])?>" /></td>
          </tr>
          
          <tr>
            <td class="add-edit-txt">Описание:</td>
            <td><input class="head-text" type="text" name="description" value="<?=htmlspecialchars($_SESSION['add_product']['description'])?>" /></td>
          </tr>
          
          <tr>
    		<td>Родительская категория:</td>
    		<td>
                <select class="select-inf" name="goods_brandid" size="10" style="height: auto;">
<?php foreach($cat as $key_parent => $item): ?>
<?php if(count($item) > 1): // если это родительская категория ?>
    <option disabled=""><?=$item[0]?></option>
<?php $i = 0; ?>
<?php foreach($item['sub'] as $key => $sub): // цикл дочених подкатегорий?>
    <option <?php if($key == $brand_id OR $key_parent == $brand_id AND $i == 0){echo 'selected'; $i = 1;} ?> value="<?=$key?>">&nbsp;&nbsp;-&nbsp;<?=$sub?></option>
<?php endforeach; // конец цикла дочерних подкатегорий ?>
<?php elseif($item[0]): // если самостоятельная категория без подкатегорий ?>
    <option <?php if($key_parent == $brand_id) echo 'selected'; ?> value="<?=$key_parent?>"><?=$item[0]?></option>
<?php endif; // конец условия родительской категории ?>
<?php endforeach; ?> 
                </select>
            </td>
    	  </tr>
    	  
        <tr>
            <td>Базовая картинка товара:</td>
            <td><input type="file" name="baseimg"/></td>
        </tr>
          
          <tr>
    		<td>Краткое описание:</td>
    		<td></td>
    	  </tr>
    	  <tr>
    		<td colspan="2">
    			<textarea id="editor1" class="anons-text" name="anons" ><?=htmlspecialchars($_SESSION['add_product']['anons'])?></textarea>
<script type="text/javascript">
CKEDITOR.replace('editor1');
</script>                
    		</td>
    	  </tr>
          
          <tr>
    		<td>Подробное описание:</td>
    		<td></td>
    	  </tr>
    	  <tr>
    		<td colspan="2">
    			<textarea id="editor2" class="anons-text" name="content"><?=htmlspecialchars($_SESSION['add_product']['content'])?></textarea>
<script type="text/javascript">
CKEDITOR.replace('editor2');
</script>                
    		</td>
    	  </tr>
          <tr>
            <td>Картинки галереи:</td>
            <td></td>
          </tr>
          
          <tr>
            <td id="btnimg">
                <div><input type="file" name="galleryimg[]" /></div>
            </td>
          </tr>
          
          <tr>
            <td>
                <input type="button" id="add" value="Добавить поле" />
                <input type="button" id="del" value="Удалить поле" />
            </td>
          </tr>
          
          <tr>
            <td>Отметить как:</td>
            <td>
                <label style="cursor: pointer;"><input type="checkbox" value="1" name="new" /> Новинка </label><br />
            	<label style="cursor: pointer;"><input type="checkbox" value="1" name="hits" /> Лидер продаж </label><br />
                <label style="cursor: pointer;"><input type="checkbox" value="1" name="sale" /> Распродажа </label><br />
            </td>
          </tr>
          
          <tr>
            <td>Показывать в товарах на сайте:</td>
            <td><label style="cursor: pointer;"><input type="radio" value="1" name="visible" checked="" /> Да</label><br /><label style="cursor: pointer;"><input type="radio" value="0" name="visible" /> Нет</label></td>
          </tr>
            
    	</table>
    	<input style="margin: 20px 0 10px 0;" type="image" src="<?=ADMIN_TEMPLATE?>images/save_btn.jpg" /> 	
    </form>
<?php unset($_SESSION['add_product']); ?>
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>