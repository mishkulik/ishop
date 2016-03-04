<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<?php if($status):?>
<h2 style="color: #960b06;">Новые заказы</h2>
<?php else: ?>
<h2>Список всех заказов <span class="small" style="display: inline;">(необработанные заказы подсвечены голубым цветом)</span></h2>
<?php endif; ?> 
<?php if($orders): 
if($_SESSION['answer']){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
	<table class="tabl" cellspacing="1">
	  <tr>
		<th class="number">№ заказа</th>
		<th class="str_name" style="width:280px;">Покупатель</th>
		<th class="str_sort">Дата</th>
		<th class="str_action">Просмотр</th>
	  </tr>
<?php foreach($orders as $value): ?>
	  <tr <?php if($value['status'] == '0') echo "class='rowlight'"; ?>>
		<td><?=$value['order_id']?></td>
		<td class="name_page"><?=htmlspecialchars($value['name'])?></td>
		<td><?=$value['date']?></td>
		<td><a href="?view=show_order&amp;order_id=<?=$value['order_id']?>" class="edit">Просмотреть</a></td>
	  </tr>
<?php endforeach; ?>
	</table>
<?php if($pages_count > 1) pagination($page, $pages_count); ?>
<?php else: ?>
<div class="error">Список заказов пуст.</div>
<?php endif; ?>
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>