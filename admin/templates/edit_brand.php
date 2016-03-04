<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<h2>Редактирование категории</h2>
<?php  
if(isset($_SESSION['edit_brand']['res'])){
    echo $_SESSION['edit_brand']['res'];
    unset($_SESSION['edit_brand']); 
} 
?>
	<form action="" method="post">
		
        <table class="add_edit_page" cellspacing="0" cellpadding="0">
          <tr>
        	<td class="add-edit-txt">Название категории:</td>
        	<td><input class="head-text" type="text" name="brand_name" value="<?=$brand_name?>" /></td>
          </tr>
          <tr>	
<?php if(!$cat[$brand_id]['sub']): // если у категории нет подкатегорий ?>
            <td>Родительская категория:</td>
            <td>
            <select class="select-inf" name="parent_id">
                <option value="0">Самостоятельная категория</option>
                <?php foreach($cat as $key => $value): ?>
                <?php if($value[0] == $brand_name) continue; // это означает, что при чередовании элементов массива $cat при обнаружении элемента, заданного в проверке в условии, этот элемент будте пропущен, что нам и нужно, то есть не выводить в выпадающем списке элемент, который мы собрались редактировать ?>
                <option value="<?=$key?>"><?=$value[0]?></option>
                <?php endforeach; ?>
            </select>
            </td>
<?php else: ?>
            <td>Родительская категория:</td>
            <td colspan=''>Это категория содержит подкатегории.</td>
<?php endif; ?>
            </tr>
    		</table>
		
		<input class="inputImg" type="image" src="<?=ADMIN_TEMPLATE?>images/save_btn.jpg" /> 

	</form>
    
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>