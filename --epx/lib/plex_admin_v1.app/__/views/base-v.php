<?php

$this->ui->canvas->prt(function($view){
    $view->load('views/studio')->prt(function($view){
        $view->vars->set('sidebar/header', function($view){ 
            ?>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold">Site Plex</span>
                <span class="text-secondary small">(<?=0?>)</span>
            </div>
            <?php 
        });
        $view->vars->set('sidebar/middle', function($view){ 
            ?>
            <ul class="nav nav-pills flex-fill flex-column gap-1">
                <li class="nav-item">
                    <a href="<?=$_SERVER['_']['BASE_URL']?>/modules" class="nav-link">Modules</a>
                </li>
                <li class="nav-item">
                    <a href="<?=$_SERVER['_']['BASE_URL']?>/keys" class="nav-link">Keys</a>
                </li>
                <li class="nav-item">
                    <a href="<?=$_SERVER['_']['BASE_URL']?>/impex" class="nav-link">Impex</a>
                </li>
            </ul>
            <?php 
        });
        $view->vars->set('main',function($view){ echo $this->inset(); });
    });
});
