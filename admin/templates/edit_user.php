<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<h2>Редактирование пользователя</h2>
<?php
if(isset($_SESSION['edit_user']['res'])){
    echo $_SESSION['edit_user']['res'];
    unset($_SESSION['edit_user']);
} 
?>
    <form action="" method="post">
        <table class="add_edit_page">
            <tr>
                <td class="add-edit-txt">Имя:</td>
                <td><input class="for_add_user" type="text" name="name" value="<?=htmlspecialchars($get_user['name'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">Логин:</td>
                <td>
<?php if($_SESSION['auth']['user_id'] != $user_id): // если редактируется не свой профиль ?>
                <input class="for_add_user" type="text" name="login" value="<?=htmlspecialchars($get_user['login'])?>" />
<?php else: // если редактируется свой профиль ?>
                <input class="for_add_user" type="text" name="login" value="<?=htmlspecialchars($get_user['login'])?>" disabled="" /><span style="display: inline;" class="small">Собственный логин изменить нельзя.</span>
<?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="add-edit-txt">Новый пароль пользователя:</td>
                <td><input class="for_add_user" type="text" name="password" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">Email:</td>
                <td><input class="for_add_user" type="text" name="email" value="<?=htmlspecialchars($get_user['email'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">Телефон:</td>
                <td><input class="for_add_user" type="text" name="phone" value="<?=htmlspecialchars($get_user['phone'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">Адрес:</td>
                <td><input class="for_add_user" type="text" name="address" value="<?=htmlspecialchars($get_user['address'])?>" /></td>
            </tr>
<?php if($_SESSION['auth']['user_id'] != $user_id): // если редактируется не свой профиль ?>
            <tr>
                <td class="add-edit-txt">Роль пользователя:</td>
                <td>
                    <?php if($roles): ?>
                    <select name="id_role" class="for_add_user_select" style="cursor: pointer;">
                        <?php foreach($roles as $item): ?>
                            <option value="<?=$item['id_role']?>" <?php if($get_user['id_role'] == $item['id_role']) echo "selected=''"; ?>><?=$item['name_role']?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
            </tr>
<?php endif; ?>
        </table>
        <input style="margin-top: 30px;" type="image" src="<?=ADMIN_TEMPLATE?>images/save_btn.jpg" />
    </form>
    
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>