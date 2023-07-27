@section('header')
<nav class="navbar navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="/">{{ config('app.name') }}　{{ \Login::get_view_kindergarten_name() }}</a>
    <ul class="nav small">
@if (\Login::is_city())
        <li class="nav-item">
            <a class="nav-link text-white" href="/city">本巣市</a>
        </li>
@endif
        <li class="nav-item">
            <a class="nav-link text-white" href="/">トップ</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('arrive') }}">早朝一覧</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('attendance/class') }}">クラス出席簿</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('depart/bus') }}">バス一覧</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('depart/daytime') }}">お迎え一覧</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('depart/evening') }}">延長一覧</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="{{ url('children') }}">園児一覧</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                利用状況
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="{{ url('sheet/attendance') }}">出席統計</a>
                <a class="dropdown-item" href="{{ url('sheet/attendance/total') }}">出席統計集計</a>
                <a class="dropdown-item" href="{{ url('sheet/absence') }}">欠席集計表</a>
                <a class="dropdown-item" href="{{ url('sheet/attendance/stats') }}">統計表</a>
                <a class="dropdown-item" href="{{ url('sheet/contract/count') }}">時間外保育 契約者数</a>
                <a class="dropdown-item" href="{{ url('sheet/contract/list') }}">時間外保育 契約者一覧</a>
                <a class="dropdown-item" href="{{ url('sheet/contract/month') }}">預かり人数表（月間）</a>
                <a class="dropdown-item" href="{{ url('sheet/contract/year') }}">預かり人数表（年間）</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link text-warning _font-weight-bolder" href="{{ url('mst/reports') }}">幼児園日誌</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-warning _font-weight-bolder" href="{{ url('mst/calendar') }}">年間予定管理</a>
        </li>
    </ul>
</nav>
@endsection()
