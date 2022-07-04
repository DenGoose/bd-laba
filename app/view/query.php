<?php
/* @var array $params */
$counter = 0;
?>
<div class="accordion pt-4" id="accordionExample">
	<div class="accordion-item">
		<h4 class="accordion-header" id="headingTwo">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
				Запросы в БД
			</button>
		</h4>
		<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" style="">
			<div class="accordion-body">
				<?php foreach ($params['query'] as $query):?>
                <div>
                    <p><?=++$counter?>)</p>
                    <p><pre><?=$query?></pre></p>
                </div>
                <?php endforeach;?>
			</div>
		</div>
	</div>
</div>