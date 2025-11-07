<?php //dd($rootChildCauseData);?>    
	<tr id='tr-<?php echo $parrentid; ?>'>
  		<td width="22%" align="left" valign="middle">
    		<select id="<?php echo 'root_cause-' . $parrentid; ?>" name="<?php echo 'root_cause-' . $parrentid; ?>" onchange="add_root_cause('<?php echo 'root_cause-' . $parrentid; ?>', <?php echo $parrentid; ?>)">
      			<option value="0">Select One</option>
				<?php 
					$rootChildCauseData = $rootChildCauseData->toArray(); 
					foreach ($rootChildCauseData as $cause): 
				?>
				<option value="<?php echo h($cause['id']); ?>">
          		<?php echo h($cause['type']); ?>
        		</option>
     			<?php endforeach; ?>
    		</select>
  		</td>
  		<td id="error_msg" width="68%" class="textcmpul" style="color: red;"></td>
  		<td width="10%" align="right" valign="middle">&nbsp;</td>
	</tr>