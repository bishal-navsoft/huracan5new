<select id="report_data" name="report_data">
      <option value="0">Select One</option>
      <?php
      if (!empty($reportDeatil['Report']) && is_array($reportDeatil['Report'])) {
            foreach ($reportDeatil['Report'] as $report) {
                  $id = $report['id'][0] ?? '';
                  $name = $report['report_name'][0] ?? '';
                  ?>
                  <option value="<?php echo 'hsse_' . h($id); ?>">
                  <?php echo h($name); ?>
                  </option>
                  <?php
            }
      }
      ?>
</select>