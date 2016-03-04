<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<?php //print_arr($show_order); ?>
<?php if($show_order): 
if($show_order[0]['status']){
    $state = "<span style='color: #5FCE2D;'>(Обработан)</span>";
}else{
    $state = "<span style='color: #960b06'>(Не обработан)</span>";
    $confirm = "<a href='?view=orders&amp;confirm={$order_id}' class='edit'>Подтвердить заказ</a> | ";
}
?>
    <h2>Заказ № <?=$order_id?> <?=$state?></h2>
    
<p><?=$confirm?><a href="?view=orders&amp;del_order=<?=$order_id?>" class="del">Удалить заказ</a></p>
<br />
        
	<table class="tabl" cellspacing="1">
	  <tr>
		<th class="number">№</th>
		<th class="str_name" style="width:280px;">Название товара</th>
		<th class="str_sort">Цена</th>
		<th class="str_action">Количество</th>
	  </tr>
<?php 
$i = 1; 
$total_sum = 0;
foreach($show_order as $value):
?>      
	  <tr>
		<td><?=$i?></td>
		<td class="name_page"><?=$value['name']?></td>
		<td><?=$value['price']?></td>
		<td><?=$value['quantity']?></td>
	  </tr>
<?php 
$i++;
$total_sum += $value['price'] * $value['quantity'];
endforeach; 
?>
	</table>
    
    <h2>Общая цена заказа: <span style="color:#e35a0f;"><?=$total_sum?></span> руб.</h2> 
    <h2>Дата заказа: <span style="font-size: 15px;"><?=$value['date']?></span></h2>      
    <h2>Способ доставки: <span style="font-size: 16px;"><?=$value['sposob']?></span></h2>
    <h2>Данные покупателя:</h2>
    
    <table class="tabl" cellspacing="1">
	  <tr>
		<th class="number" style="width:140px;">ФИО</th>
		<th class="str_name" style="width:200px;">Адрес</th>
		<th class="str_sort">Для связи</th>
		<th class="str_action">Примечание</th>
	  </tr>
	  <tr>
		<td><?=htmlspecialchars($value['customer'])?></td>
		<td class="name_page"><?=htmlspecialchars($value['address'])?></td>
		<td><?=htmlspecialchars($value['email'])?><br /><?=htmlspecialchars($value['phone'])?></td>
		<td style="text-align:left;"><?=htmlspecialchars($value['prim'])?></td>
	  </tr>
    </table>
<p><?=$confirm?><a href="?view=orders&amp;del_order=<?=$order_id?>" class="del">Удалить заказ</a></p>
<?php else: ?>
<div class="error">Заказа с таким номером нет.</div>
<?php endif; ?>
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>