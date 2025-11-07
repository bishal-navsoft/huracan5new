<select  id="immeditaet_sub_cus" name="immeditaet_sub_cus" >
	<option value="0">Select One</option>
	<?php foreach ($causeList as $cause): ?>
		<option value="<?= h($cause->id) ?>">
			<?= h($cause->type) ?>
		</option>
	<?php endforeach; ?>
</select>