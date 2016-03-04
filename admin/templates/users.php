<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<h2>Список пользователей</h2>
<?php //print_arr($users); ?>
<?php
if($_SESSION['answer']){
    echo $_SESSION['answer'];
    unset($_SESSION['answer']);
}
?>
    <a href="?view=add_user"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_user.jpg" alt="добавить пользователя" /></a>
    
    <table class="tabl" cellspacing="1">
        <tr>
            <th class="number">№</th>
            <th class="str_name">Имя</th>
            <th class="str_name">Логин</th>
            <th class="str_name">Email</th>
            <th class="str_sort">Роль</th>
            <th class="str_action">Действие</th>
        </tr>
<?php $i = 1; ?>        
<?php foreach($users as $item): ?>
        <tr <?php if($item['name_role'] == 'Администратор') echo "class='rowlight'"; ?>>
            <td><?=$i?></td>
            <td class="name_page"><?=htmlspecialchars($item['name'])?></td>
            <td class="name_page"><?=htmlspecialchars($item['login'])?></td>
            <td class="name_page"><?=htmlspecialchars($item['email'])?></td>
            <td><?=$item['name_role']?></td>
            <td><a href="?view=edit_user&amp;user_id=<?=$item['customer_id']?>&amp;page=<?=$page?>" class="edit">изменить</a>&nbsp; | &nbsp;<a href="?view=del_user&amp;user_id=<?=$item['customer_id']?>&amp;page=<?=$page?>" class="del">удалить</a></td>
        </tr>
<?php $i++; ?>
<?php endforeach; ?>
    </table>
    
    <a href="?view=add_user"><img class="add_some" src="<?=ADMIN_TEMPLATE?>images/add_user.jpg" alt="добавить пользователя" /></a>
<?php if($pages_count > 1) pagination($page, $pages_count); ?>
    
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>