<script language="javascript" type="text/javascript">
    function redirection(rdroot){
         var path='<?php echo $this->webroot; ?>';
          document.location=path+rdroot;        
    }
    
    
</script>

<?php if (!empty($admin_menus_parrentdata)): ?>
    <?php foreach ($admin_menus_parrentdata as $parentMenu): ?>
        <?php if (!empty($parentMenu)): ?>
            <ul>
                <li>
                    <a href="javascript:void(0);">
                        <?= h($parentMenu->menu_name) ?>
                    </a>

                    <?php
                    $parentId = $parentMenu->id;
                    if (!empty($admin_menus_children[$parentId])): ?>
                        <div class="dropdown_box">
                            <div class="innerdrop">
                                <ul>
                                    <?php foreach ($admin_menus_children[$parentId] as $childGroup): ?>
                                        <?php foreach ($childGroup as $childMenu): ?>
                                            <?php if (!empty($childMenu)): ?>
                                                <li>
                                                    <a href="<?= $this->Url->build('/' . ltrim($childMenu->url, '/')) ?>">
                                                        <?= h($childMenu->menu_name) ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

