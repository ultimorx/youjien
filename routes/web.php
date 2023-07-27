<?php

use \App\Models\{
    Attendance,
    Disease,
    Bus,
    Classroom,
    EveningTime,
    MorningTime,
    Roster,
    CityKindergarten,
};

function KindergartenViewable() {
    if( ! \Login::has_user() ) {
        header('Location: /login');
        exit;
    }
    if( ! \Login::has_view_kindergarten_id() ) {
        header('Location: /city');
        exit;
    }
}
function CityOnly() {
    if( ! \Login::has_user() ) {
        header('Location: /login');
        exit;
    }
    // 本巣市役所以外は閲覧できない。
    if( ! \Login::is_city() ) {
        header('Location: /');
        exit;
    }
}

Route::get('/login', function () {
    \Login::clear();
    return view('pages/login');
});

Route::get('/direct', 'Rest\CityUserController@direct_login');

Route::get('/', function () {
    $id = (isset($_GET['id']) && !empty($_GET['id']))? $_GET['id']: 0;
    if( ! empty($id)) {
        \Login::set_view_kindergarten($id);
    }
    KindergartenViewable();
    return view('pages/top');
});
Route::get('/arrive', function () {
    KindergartenViewable();
    return view('pages/arrive');
});
Route::get('/attendance/class', function () {
    KindergartenViewable();
    // $diseases = Disease::query()->orderBy('order')->get(); // 22.9.12無効化
    $diseases = Disease::list();
    $classrooms = Classroom::actives();
    $buses = Bus::query()->orderBy('order')->get();
    $evening_times = EveningTime::query()->orderBy('order')->get();
    $morning_times = MorningTime::query()->orderBy('order')->get();
    return view('pages/attendance/class', [
        'classrooms' => $classrooms,
        'buses' => $buses,
        'evening_times' => $evening_times,
        'morning_times' => $morning_times,
        'diseases' => $diseases
    ]);
});
Route::get('/depart/bus', function () {
    KindergartenViewable();
    return view('pages/depart/bus');
});
Route::get('/depart/daytime', function () {
    KindergartenViewable();
    return view('pages/depart/daytime');
});
Route::get('/depart/evening', function () {
    KindergartenViewable();
    return view('pages/depart/evening');
});
Route::get('/children', function () {
    KindergartenViewable();
    $classrooms = Classroom::actives();
    $buses = Bus::query()->orderBy('order')->get();
    $evening_times = EveningTime::query()->orderBy('order')->get();
    $morning_times = MorningTime::query()->orderBy('order')->get();
    return view('pages/children', [
        'classrooms' => $classrooms,
        'buses' => $buses,
        'evening_times' => $evening_times,
        'morning_times' => $morning_times
    ]);
});

Route::get('/sheet/attendance', function () {
    KindergartenViewable();
    return view('pages/sheet/attendance');
});
Route::get('/sheet/attendance/total', function () {
    KindergartenViewable();
    return view('pages/sheet/attendance/total');
});
Route::get('/sheet/absence', function () {
    KindergartenViewable();
    return view('pages/sheet/absence');
});
Route::get('/sheet/attendance/stats', function () {
    KindergartenViewable();
    return view('pages/sheet/attendance/stats');
});
Route::get('/sheet/contract/list', function () {
    KindergartenViewable();
    return view('pages/sheet/contract/list');
});
Route::get('/sheet/contract/child/{bizyear}/{month}/{roster_id}', function ($bizyear, $month, $roster_id) {
    KindergartenViewable();
    return view('pages/sheet/contract/child', [
        'bizyear' => $bizyear,
        'month' => $month,
        'roster_id' => $roster_id,
    ]);
});
Route::get('/sheet/contract/count', function () {
    KindergartenViewable();
    return view('pages/sheet/contract/count');
});
Route::get('/sheet/contract/month', function () {
    KindergartenViewable();
    return view('pages/sheet/contract/month');
});
Route::get('/sheet/contract/year', function () {
    KindergartenViewable();
    return view('pages/sheet/contract/year');
});
Route::get('/mst/calendar', function () {
    KindergartenViewable();
    return view('pages/mst/calendar');
});
Route::get('/mst/bizyears', function () {
    KindergartenViewable();
    return view('pages/mst/bizyear');
});
Route::get('/mst/classrooms', function () {
    KindergartenViewable();
    return view('pages/mst/classrooms');
});
Route::get('/mst/children', function () {
    KindergartenViewable();
    return view('pages/mst/children');
});
Route::get('/mst/children/import', function () {
    KindergartenViewable();
    return view('pages/mst/children/import');
});
Route::get('/mst/rosters', function () {
    KindergartenViewable();
    return view('pages/mst/rosters');
});
Route::get('/mst/aims', function () {
    KindergartenViewable();
    return view('pages/mst/aim');
});
Route::get('/mst/reports', function () {
    KindergartenViewable();
    return view('pages/mst/report');
});

#印刷ページ
Route::get('/mst/print/calendar', function () {
    KindergartenViewable();
    if(isset($_GET['bizyear'])){
        return view('pages/mst/print/calendar_bizyear');
    }
    if(isset($_GET['bizmonth'])){
        return view('pages/mst/print/calendar_bizmonth');
    }
    return view('pages/mst/print/calendar');
});
Route::get('/mst/print/report', function () {
    KindergartenViewable();
    return view('pages/mst/print/report');
});

// 本巣市用
Route::get('/city', function () {
    CityOnly();
    return view('pages/city/top');
});
Route::get('/city/information', function () {
    CityOnly();
    return view('pages/city/information');
});
Route::get('/city/absence', function () {
    CityOnly();
    return view('pages/city/absence');
});
Route::get('/city/event', function () {
    CityOnly();
    return view('pages/city/event');
});
Route::get('/city/user', function () {
    CityOnly();
    return view('pages/city/user');
});
Route::get('/city/accesslog', function () {
    CityOnly();
    return view('pages/city/accesslog');
});


// 環境確認
Route::get('version', function() {
    $laravel = app();
    return "Your Laravel version is ".$laravel::VERSION;
});
Route::get('phpinfo', function() {
    phpinfo();
    return '';
});


// Route::get('/city/set_view_kindergarten', function () {
//     // header() exit()関数を行うとsession処理が途中で中断され、保存されない。
//     $id = (isset($_GET['id']) && !empty($_GET['id']))? $_GET['id']: 0;
//     \Log::info('set_view_kindergarten : '.$id);
//     \Sess::set_view_kindergarten_id($id);
//     header('Location: /');
//     // exit;
// });

#検証ページ
#年度の日付と週数表示
// Route::get('/test/bizdays', function () {
//     return view('pages/test/bizdays');
// });

// サーバ運用に伴いバックアップ処理は使用しない 2022年度
// use App\Util\Db;
// Route::get('/db/export', function () {
//     Db::export();
//     return '';
// });
