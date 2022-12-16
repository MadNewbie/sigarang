<?php $counter = floor(count($data['b'])/16)?>
<style>
    h1, h2, h3, h4, h5, h6, p{
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        vertical-align: baseline;
        background: transparent;
    }
    body {
        font-family: 'Arial', 'Helvetica', 'sans-serif',
    }
    .logo {
        float: left;
        width: 10%;
        height: auto;
    }
    .title-section {
        margin-bottom: 20px;
    }
    .title-section>h2{
        margin: 0px;
        padding: 0px;
    }
    .header-logo {
        width: 100px;
        height: 80px;
    }
</style>
<div class="row title-section">
    <div class="logo">
        <img src="https://2.bp.blogspot.com/-Ne5sknY1pJw/WhUK2mTUbUI/AAAAAAAAFPY/PnobQKmeO3Ev71-6TSlFunw08Pnk3LpogCLcBGAs/s1600/Sampang.png" alt="logo-samapng" class="header-logo">
    </div>
    <div class="text-section">
        <h2>Pemerintah Kabupaten Sampang</h2>
        <h4>Dinas Koperasi, Perindustrian dan Perdagangan</h4>
        <p>Jl Diponogoro No 52 A Telp. (0323) 321066 - 323250 FAX (0323) 323250</p>
    </div>
</div>
<div class="row subtitle-section">
    <div class="text-section">
        <p><b>Nama Pasar: </b>{{$data['d']['market_id']}}</p>
        <p><b>Periode: </b>{{$data['d']['start_date']}} - {{$data['d']['end_date']}}</p>
    </div>
</div>
@for($n = 0; $n < $counter+1; $n++)
<table>
    <thead>
        <tr>
            <th style="border: 1px solid #555">Nama Barang</th>
            <th style="border: 1px solid #555;">Satuan</th>
            @for($m = 0; $m < 16; $m++)
            @if(isset($data['b'][($n*16)+$m]))
            <th style="border: 1px solid #555;">{{$data['b'][($n*16)+$m]['title']}}</th>
            @endif
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($data['a'] as $a)
        <tr>
            <td style="border: 1px solid #555;">{{$a['name']}}</td>
            <td style="border: 1px solid #555; text-align:center">{{$a['unit']}}</td>
            @for($m = 0; $m < 16; $m++)
                @if(isset($data['b'][($n*16)+$m]))
                <td style="border: 1px solid #555;"> <?php echo number_format($a['data'][$data['b'][($n*16)+$m]['title']],0,",",".")?></td>
                @endif
            @endfor
        </tr>
        @endforeach
    </tbody>
</table>
@if($n!=$counter-1 && $counter!=0)
    <div style="page-break-after: always;"></div>
@endif
@endfor