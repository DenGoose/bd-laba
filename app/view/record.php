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
    <form class="pt-4" method="post" action="<?=$params['result']['action']?>" enctype="multipart/form-data">
    <?php foreach ($params['result']['items'] as $item): ?>
            <?php if (isset($item['value']) && ($item['code'] == 'id' || $item['code'] == 'ID')):?>
                <input type="hidden" name="<?=$item['code']?>" value="<?=$item['value']?>">
            <?php elseif ($item['type'] == 'list'):?>
            <label for="input<?=$item['code']?>" class="form-label"><?=$item['name']?></label>
            <select class="form-select form-select-lg mb-3" id="input<?=$item['code']?>" name="<?=$item['code']?>" aria-label="Выберите клиента" required>
                <?php foreach ($item['list_values'] as $list):?>
                    <option <?=(isset($item['value']) && $item['value'] == $list['id']) ? 'selected' : ''?> value="<?=$list['id']?>"><?=$list['name']?></option>
                <?php endforeach;?>
            </select>
            <?php elseif ($item['type'] == 'multiple_list'):?>
            <p><?=$item['name']?></p>
                <?php foreach ($item['list_values'] as $list):?>
                <div class="mb-3 form-check">
                    <input <?=(in_array($list['id'], $item['value'])) ? 'checked' : ''?> name="<?=$item['code']?>[]" value="<?=$list['id']?>" type="checkbox" class="form-check-input" id="input<?=$item['code']?>-<?=$list['id']?>">
                    <label class="form-check-label" for="input<?=$item['code']?>-<?=$list['id']?>"><?=$list['name']?></label>
                </div>
                <?php endforeach;?>
		    <?php elseif ($item['type'] == 'int'):?>
            <div class="mb-3">
                <label for="input<?=$item['code']?>" class="form-label"><?=$item['name']?></label>
                <input type="number" class="form-control <?=isset($item['error']) && mb_strlen($item['error'])? 'is-invalid' : ''?>" placeholder="<?=$item['name']?>" name="<?=$item['code']?>" id="input<?=$item['code']?>" aria-describedby="<?=$item['code']?>Help" value="<?=$item['value'] ?? ''?>" required>
				<?php if (isset($item['error']) && mb_strlen($item['error'])):?>
                    <div id="<?=$item['code']?>Help" class="invalid-feedback"><?=$item['error']?></div>
				<?php endif;?>
            </div>
			<?php else:?>
            <div class="mb-3">
                <label for="input<?=$item['code']?>" class="form-label"><?=$item['name']?></label>
                <input type="text" class="form-control <?=isset($item['error']) && mb_strlen($item['error'])? 'is-invalid' : ''?>" placeholder="<?=$item['name']?>" name="<?=$item['code']?>" id="input<?=$item['code']?>" aria-describedby="<?=$item['code']?>Help" value="<?=$item['value'] ?? ''?>" required>
                <?php if (isset($item['error']) && mb_strlen($item['error'])):?>
                    <div id="<?=$item['code']?>Help" class="invalid-feedback"><?=$item['error']?></div>
                <?php endif;?>
            </div>
            <?php endif;?>
    <?php endforeach;?>
    <button type="submit" class="btn btn-dark mb-4">Отправить</button>
</form>