<?php

use Spatie\Permission\Models\Role;

/* @var $model Role */
?>

@can($routePrefix.'.edit')
 <a href="<?= route($routePrefix.".edit", $model->id) ?>" class="btn btn-sm btn-warning btn-index-menu" title="Edit">
    <i class="fa fa-edit"></i>
</a>
@endcan
@can($routePrefix.'.destroy')
<button data-id="<?= $model->id ?>" class="btn btn-sm btn-danger btn-destroy btn-index-menu" title="Hapus">
    <i class="fa fa-trash"></i>
</button>
@endcan

