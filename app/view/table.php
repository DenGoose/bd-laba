<?php
/* @var array $params */
$errors = $_SESSION['alert'] ?? [];
unset($_SESSION['alert']);
?>
<?php if ($errors):?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <h4 class="alert-heading">Ошибка!</h4>
        <p><?=implode('</p><hr><p>', $errors)?></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif;?>
<?php if (isset($params['result']['items']) && $params['result']['items']):?>
<table class="table table-striped mt-3">
	<thead>
	<tr>
		<?php foreach ($params['result']['columns'] as $column): ?>
			<th scope="col"><?=$column?></th>
		<?php endforeach; ?>
		<th scope="col">Действия</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($params['result']['items'] as $item): ?>
	<tr>
		<?php foreach ($item as $el): ?>
                <td><?=$el?></td>
		<?php endforeach;?>
		<td>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" id="element_actions" data-bs-toggle="dropdown" aria-expanded="false">Выбрать действие</button>
                <ul class="dropdown-menu" aria-labelledby="element_actions">
                    <li><a href="<?=$params['currentUrl']?>update/?id=<?=$item['ID']?>" class="dropdown-item">Изменить</a></li>
                    <li><a href="<?=$params['currentUrl']?>delete/?id=<?=$item['ID']?>" class="dropdown-item confirm-delete">Удалить</a></li>
                </ul>
            </div>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
<?php else:?>
    <div class="alert alert-warning alert-dismissible fade show mt-5" role="alert">
        Элементов не найдено
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif;?>
<div class="pt-5 pb-5">
    <a href="<?=$params['currentUrl']?>add/" class="btn btn-dark">Добавить запись</a>
</div>
