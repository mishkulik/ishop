<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<?php //print_arr($cat); ?>
	<h2>Редактирование товара</h2>
<?php  
if(isset($_SESSION['edit_product']['res'])){
    echo $_SESSION['edit_product']['res'];
    unset($_SESSION['edit_product']);
}?>
<div id="goods_id" style="display: none;"><?=$get_product['goods_id']?></div>
    <form action="" method="post" enctype="multipart/form-data">			
    	<table class="add_edit_page" cellspacing="0" cellpadding="0">
    	  <tr>
    		<td class="add-edit-txt">Название товара:</td>
    		<td><input class="head-text" type="text" name="name" value="<?=htmlspecialchars($get_product['name'])?>" /></td>
    	  </tr>
          
          <tr>
              <td class="add-edit-txt">Цена:</td>
              <td><input class="head-text" type="text" name="price" value="<?=$get_product['price']?>" /></td>
          </tr>
          
          <tr>
            <td class="add-edit-txt">Ключевые слова:</td>
            <td><input class="head-text" type="text" name="keywords" value="<?=htmlspecialchars($get_product['keywords'])?>" /></td>
          </tr>
          
          <tr>
            <td class="add-edit-txt">Описание:</td>
            <td><input class="head-text" type="text" name="description" value="<?=htmlspecialchars($get_product['description'])?>" /></td>
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
            <td>Базовая картинка товара:<br />
            <?php if($get_product['img'] != 'no_image.jpg') echo '<span class="small">Для удаления картинки кликните по ней.</span>';?></td>
            <td class="baseimg"><?=$baseimg?></td>
        </tr>
          
          <tr>
    		<td>Краткое описание:</td>
    		<td></td>
    	  </tr>
    	  <tr>
    		<td colspan="2">
    			<textarea id="editor1" class="anons-text" name="anons" ><?=htmlspecialchars($get_product['anons'])?></textarea>
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
    			<textarea id="editor2" class="anons-text" name="content"><?=htmlspecialchars($get_product['content'])?></textarea>
<script type="text/javascript">
CKEDITOR.replace('editor2');
</script>                
    		</td>
    	  </tr>
          <tr>
            <td>Картинки галереи:<br />
            <?php if($get_product['img_slide']) echo '<span class="small">Для удаления картинки кликните по ней.</span>';?></td>
            <td class="slideimg"><?=$imgslide?></td>
          </tr>
          
          <tr>
            <td> 
                <div id="butUpload">Выбрать файл</div>  
            </td>
            <td>
                <div id="filesUpload"></div>
            </td>
          </tr>
          
          <tr>
            <td>Отметить как:</td>
            <td>
                <label style="cursor: pointer;"><input type="checkbox" value="1" name="new" <?php if($get_product['new']) echo 'checked=""'; ?> /> Новинка </label><br />
            	<label style="cursor: pointer;"><input type="checkbox" value="1" name="hits" <?php if($get_product['hits']) echo 'checked=""'; ?> /> Лидер продаж </label><br />
                <label style="cursor: pointer;"><input type="checkbox" value="1" name="sale" <?php if($get_product['sale']) echo 'checked=""'; ?> /> Распродажа </label><br />
            </td>
          </tr>
          
          <tr>
            <td>Показывать в товарах на сайте:</td>
            <td><label style="cursor: pointer;"><input type="radio" value="1" name="visible" <?php if($get_product['visible']) echo 'checked=""'; ?> /> Да</label><br /><label style="cursor: pointer;"><input type="radio" value="0" name="visible" <?php if(!$get_product['visible']) echo 'checked=""'; ?> /> Нет</label></td>
          </tr>
            
    	</table>
    	<input style="margin: 20px 0 10px 0;" type="image" src="<?=ADMIN_TEMPLATE?>images/save_btn.jpg" /> 	
    </form>
<?php unset($_SESSION['add_product']); ?>
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->

<script type="text/javascript">
// загрузка картинок
var button = $("#butUpload"), interval; // кнопка загрузки + интервал ожидания
var path = '<?=GALLERYIMG?>thumbs/'; // путь к папке превью (миниатюр)
var id = $("#goods_id").text(); // id товара

new AjaxUpload(button, {
    action: './',
    name: 'userfile',
    data: {id: id},
    onSubmit: function(file, ext){
        if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
            // если недопустимое расширение
            alert('Недопустимое расширение файла.');
            // отмена загрузки
            return false;
        }
        button.text("Загрузка");
        this.disable();
        
        interval = window.setInterval(function(){
            var text = button.text();
            if(text.length < 17){
                button.text(text + ' . ');
            }else{
                button.text("Загрузка");
            }
        }, 800);
    },
    onComplete: function(file, response){
        button.text("Загрузить ещё?");
        window.clearInterval(interval);
        this.enable();
        var res = JSON.parse(response);
        if(res.answer == "OK"){
            $("#filesUpload").append("<img src='"+ path + res.file+"' />"); 
        }else{
            alert(res.answer);
        }
    }    
});
</script>

</body>
</html>