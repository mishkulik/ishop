<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">   

	<h2>Информеры</h2>
<?php
if(isset($_SESSION['answer'])){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
	<a href="?view=add_informer"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_inf.jpg" alt="добавить информер" /></a>
<div id="sotr_inf">
<?php foreach($informers as $informer): ?>
<div id="<?=$informer['informer_id'];?>" class="ss">
	<div class="inf-right">
        <p class="toggle"></p>
    	<h3><?=$informer[0]?></h3>
        <p class="inf-link"><a href="?view=edit_informer&amp;informer_id=<?=$informer['informer_id']?>" class="edit">изменить</a>&nbsp; | &nbsp;<a href="?view=del_informer&amp;informer_id=<?=$informer['informer_id']?>" class="del">удалить</a></p>
    </div><!-- .inf-right --> 

	<div class="inf-page">
		<table id="<?=$informer['informer_id']?>" class="inf-tabl" cellspacing="1">
		  <tr class="no_sort">
			<th class="number">№</th>
			<th class="str_name">Название страницы</th>
			<th class="str_sort">Сортировка</th>
			<th class="str_action">Действие</th>
		  </tr>
<?php if($informer['sub']): ?>
<?php $i = 1; ?>
<?php foreach($informer['sub'] as $key => $value): ?>
		  <tr id="<?=$key?>">
			<td class="position" style="width:25px;"><?=$i?></td>
			<td style="width:320px;" class="name_page"><?=$value?></td>
			<td class="position" style="width:80px;"><?=$i?></td>
			<td style="width: 160px;"><a href="?view=edit_link&amp;link_id=<?=$key?>" class="edit">изменить</a>&nbsp; | &nbsp;<a href="?view=del_link&amp;link_id=<?=$key?>" class="del">удалить</a></td>
		  </tr>
<?php $i++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4"><h4>В этом информере нет ни одной страницы.</h4></td>
    </tr>
<?php endif; ?>
		</table>
		<a href="?view=add_link&amp;informer_id=<?=$informer['informer_id']?>"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_page_inf.jpg" alt="добавить страницу" /></a>  
	</div><!-- .inf-page -->
    </div> <!-- .ss -->
<?php endforeach; ?>
</div> <!-- #sotr_inf -->	
	<a href="?view=add_informer"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_inf.jpg" alt="добавить информер" /></a>

		</div> <!-- .content -->
<div class="load"></div> <!-- .load -->
<div class="res"></div> <!-- .res -->
	</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>