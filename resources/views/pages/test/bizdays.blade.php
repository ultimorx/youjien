@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
@endsection

@include('components/header')

@section('content')
@php
//var_dump($_GET['bizyear'], $_GET['all']);
$bizyear = empty($_GET['bizyear'])? 2020 : $_GET['bizyear'];
echo '<h4>'.$bizyear.'年度の日付と週数表示</h4>';
$biz_week_num = 1;
$sep = '-';
// 4月〜翌3月
for($month = 4; $month <= 15; $month++) :
    $y = $bizyear;
    $m = $month;
    if($month > 12) {
        $y = $bizyear+1;
        $m = $month-12;
    }
    $monthlylastday = date('t', strtotime($y.$sep.$m.$sep.'1'));
    for($d = 1; $d <= $monthlylastday; $d++) :
        $str_date = $y.$sep.$m.$sep.$d;
        $date = date('Y-m-d', strtotime($str_date));
        $week = date('w', strtotime($str_date));
        $wn = date('W', strtotime($str_date));//年間週番号1/1始まり

        $output_flg = isset($_GET['all'])? true: false;
        //$output_flg = true;
        //$output_flg = false;
        if($m==4 && $d==1) $output_flg = true;
        if($week == 1) :
            $biz_week_num++;
            $output_flg = true;
        endif;

        if($output_flg) :
            echo '<div>'.$date.' W:'.$week.' BizWN:'.$biz_week_num.'</div>';
        endif;
    endfor;
endfor;
@endphp

@endsection()
