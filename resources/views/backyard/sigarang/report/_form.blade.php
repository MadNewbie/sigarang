
<div id="formApp">
    <div class="row">
        <div class="form-group col-sm-12 col-xs-12">
            {{ Form::label('market_id', 'Pasar') }}
            {{ Form::select('market_id', $marketOptions, null, ['class' => 'form-control', 'v-model' => 'idPasar', '@change' => 'onMarketSelect']) }}
        </div>
    </div>
    <div class="row">
        <div class="col-5 col-sm-3">
            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                <?php foreach($categories as $category): ?>
                    <a class="nav-link" id="tab-{{$category['id']}}" data-toggle="pill" href="#content-{{$category['id']}}" role="tab" aria-controls="#content-{{$category['id']}}" aria-selected="false">{{$category['name']}}</a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-7 col-sm-9">
            <div class="tab-content" id="vert-tabs-tabContent">
                <?php foreach($categories as $category): ?>
                    <div class="tab-pane fade" id="content-{{$category['id']}}" role="tabpanel" aria-labelledby="tab-{{$category['id']}}">
                       <?php if(isset($category['goods'])):?>
                          <?php foreach ($category['goods'] as $item):?>
                                <?php $id = $item['id'] ?>
                                <?php $category_id = $item['category_id'] ?>
                                <div class="row">
                                    <div class="form-group col-sm-6 col-xs-6">
                                        {{Form::label($item['id'], $item['name'])}}
                                        <span class="text-right">/ {{$item['unit_name']}}</span>
                                        <jq-priceformat
                                            name="goods[{{$item['id']}}]"
                                            class="text-right form-control"
                                            <?php if(empty($item['latest_price'])):?>
                                                :placeholder="placeholder[<?=$category_id?>].goods[<?=$id?>].latest_stock"
                                            <?php else:?>
                                                :placeholder="placeholder[<?=$category_id?>].goods[<?=$id?>].latest_price"
                                            <?php endif?>
                                        ></jq-priceformat>
                                        {{-- {{Form::text("goods[{$item['id']}]", null, ['class'=>'text-right form-control priceformat', 'placeholder'=>"{$placeholder}"])}} --}}
                                    </div>
                                </div>
                          <?php endforeach; ?>
                       <?php endif;?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-6 col-sm-2">
        <a class="btn btn-primary" href="{{ URL::previous() }}" style="width:100%">Back</a>
    </div>
    <div class="form-group col-xs-6 col-sm-2">
        <button type="submit" class="btn btn-success" style="width:100%">Submit</button>
    </div>
</div>
@section('js-include')
<script src="<?= asset('js/backyard/sigarang/report/_form.js') ?>"></script>
@endsection
@section('js-inline-data')
//<script type="text/javascript">
let varName = `_reportFormData`;
window[varName] = <?= json_encode([
    'data' => [
        'placeholder' => $categories,
        'flag' => $flag,
    ],
    'routes' => [
        'getPricePlaceholder' => route("{$routePrefix}.post.price.placeholder"),
        'getStockPlaceholder' => route("{$routePrefix}.post.stock.placeholder"),
    ],
])
?>
<!--</script>-->
@endsection
