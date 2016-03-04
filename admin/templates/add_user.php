<?php defined('ISHOP') or die('Access denied'); ?>
<div class="content">
<h2>Добавление пользователя</h2>
<?php  
if(isset($_SESSION['add_user']['res'])){
    echo $_SESSION['add_user']['res'];
} 
?>
    <form action="" method="post">
        <table class="add_edit_page">
            <tr>
                <td class="add-edit-txt">* Имя:</td>
                <td><input class="for_add_user" type="text" name="name" value="<?=htmlspecialchars($_SESSION['add_user']['name'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">* Логин:</td>
                <td><input class="for_add_user" type="text" name="login" value="<?=htmlspecialchars($_SESSION['add_user']['login'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">* Пароль:</td>
                <td><input class="for_add_user" type="text" name="password" value="<?=htmlspecialchars($_SESSION['add_user']['password'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">* Email:</td>
                <td><input class="for_add_user" type="text" name="email" value="<?=htmlspecialchars($_SESSION['add_user']['email'])?>" /></td>
            </tr>
            <tr>
                <td class="add-edit-txt">Роль:</td>
                <td>
                    <?php if($roles): ?>
                    <select name="id_role" class="for_add_user_select" style="cursor: pointer;">
                        <?php foreach($roles as $item): ?>
                            <option value="<?=$item['id_role']?>" <?php if($_SESSION['add_user']['id_role'] == $item['id_role']) echo "selected=''"; ?>><?=$item['name_role']?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <input style="margin-top: 30px;" type="image" src="<?=ADMIN_TEMPLATE?>images/save_btn.jpg" />
    </form>
<?php unset($_SESSION['add_user']); ?>
    
</div> <!-- .content -->
</div> <!-- .content-main -->
</div> <!-- .karkas -->
</body>
</html>