<div class="row">
    <div class="form-group col-sm-12 col-xs-12">
        {{ Form::label('market_id', 'Pasar') }}
        {{ Form::select('market_id', $marketOptions, null, ['class' => 'form-control']) }}
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
                           <div class="row">
                              <div class="form-group col-sm-6 col-xs-6">
                                  {{Form::label($item['id'], $item['name'])}}
                                  <span class="text-right">/ {{$item['unit_name']}}</span>
                                  {{Form::text("goods[{$item['id']}]", null, ['class'=>'text-right form-control'])}}
                              </div>
                           </div>
                      <?php endforeach; ?>
                   <?php endif;?>
                </div>
            <?php endforeach; ?>
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
