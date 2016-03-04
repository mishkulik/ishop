<?php defined('ISHOP') or die('Access denied'); ?>

<div class="kroshka">
	<a href="<?=PATH?>">Главная</a> / <span>Архив новостей</span>
</div>

<div class="content-txt">
    <?php if($all_news): ?>
        <?php foreach($all_news as $item): ?>
            <h2><a href="?view=news&amp;news_id=<?=$item['news_id']?>"><?=$item['title']?></a></h2>
            <span class="news_date"><?=$item['date']?></span>
            <br /><br />
            <?=$item['anons']?>
            <p><a href="?view=news&amp;news_id=<?=$item['news_id']?>">Подробнее...</a></p>
        <?php endforeach; ?>
        <?php if($pages_count > 1) pagination($page, $pages_count); ?>
    <?php else: ?>
        <p>Новостей пока нет.</p>
    <?php endif; ?>





<div class="clr"></div>
</div> <!-- .content-txt -->