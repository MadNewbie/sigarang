<?php 
    $counter = count($data['b']);
?>
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
@if($counter<=16)
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid #555; width: 15%">Nama Barang</th>
                <th style="border: 1px solid #555; width: 5%">Satuan</th>
                @for($n = 0; $n <= $counter; $n++)
                    @if(isset($data['b'][$n]))
                    <th style="border: 1px solid #555; width: 5%">{{$data['b'][$n]['title']}}</th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($data['a'] as $a)
            <tr>
                <td style="border: 1px solid #555; width: 15%">{{$a['name']}}</td>
                <td style="border: 1px solid #555; text-align:center; width: 5%">{{$a['unit']}}</td>
                @for($n = 0; $n <= $counter; $n++)
                    @if(isset($data['b'][$n]))
                    <td style="border: 1px solid #555; width: 5%"> <?php echo number_format($a['data'][$data['b'][$n]['title']],0,",",".")?></td>
                    @endif
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid #555; width: 15%">Nama Barang</th>
                <th style="border: 1px solid #555; width: 5%">Satuan</th>
                @for($n = 0; $n < 16; $n++)
                    @if(isset($data['b'][$n]))
                    <th style="border: 1px solid #555; width: 5%">{{$data['b'][$n]['title']}}</th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($data['a'] as $a)
            <tr>
                <td style="border: 1px solid #555; width: 15%">{{$a['name']}}</td>
                <td style="border: 1px solid #555; text-align:center; width: 5%">{{$a['unit']}}</td>
                @for($n = 0; $n < 16; $n++)
                    @if(isset($data['b'][$n]))
                    <td style="border: 1px solid #555; width: 5%"> <?php echo number_format($a['data'][$data['b'][$n]['title']],0,",",".")?></td>
                    @endif
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="page-break-after: always;"></div>
    <table>
        <thead>
            <tr>
                <th style="border: 1px solid #555; width: 15%">Nama Barang</th>
                <th style="border: 1px solid #555; width: 5%">Satuan</th>
                @for($n = 16; $n <= $counter; $n++)
                    @if(isset($data['b'][$n]))
                    <th style="border: 1px solid #555; width: 5%">{{$data['b'][$n]['title']}}</th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($data['a'] as $a)
            <tr>
                <td style="border: 1px solid #555; width: 15%">{{$a['name']}}</td>
                <td style="border: 1px solid #555; text-align:center; width: 5%">{{$a['unit']}}</td>
                @for($n = 16; $n <= $counter; $n++)
                    @if(isset($data['b'][$n]))
                    <td style="border: 1px solid #555; width: 5%"> <?php echo number_format($a['data'][$data['b'][$n]['title']],0,",",".")?></td>
                    @endif
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
@endif