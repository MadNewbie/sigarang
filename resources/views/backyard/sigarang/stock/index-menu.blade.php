<?php

use App\Models\Sigarang\Price;

/* @var $model Price */
?>

@can($routePrefix.'.approve')
<a href="<?= route($routePrefix.".approve", $model->id) ?>" class="btn btn-sm btn-success btn-index-menu" title="Setujui" onclick="return confirm('Aksi yang anda lakukan akan mempengaruhi data yang ditampilkan. Apakah anda yakin?')">
    <i class="far fa-check-circle"></i>
</a>
@endcan
@can($routePrefix.'.not.approve')
 <a href="<?= route($routePrefix.".not.approve", $model->id) ?>" class="btn btn-sm btn-danger btn-index-menu" title="Tidak Setujui" onclick="return confirm('Aksi yang anda lakukan akan mempengaruhi data yang ditampilkan. Apakah anda yakin?')">
    <i class="far fa-times-circle"></i>
</a>
@endcan
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

