<?php

//use Illuminate\Http\Request;


Route::get('/roster', 'Rest\RosterController@index');
Route::put('/roster/{roster}', 'Rest\RosterController@update');
Route::get('/roster/download/{class?}', 'Rest\RosterController@download');
Route::post('/roster/create', 'Rest\RosterController@create');

Route::get('/attendance/test', 'Rest\AttendanceController@test');

Route::get('/attendance/earlylist', 'Rest\AttendanceController@earlylist');
Route::get('/attendance/classroom', 'Rest\AttendanceController@classroom');
Route::post('/attendance/create', 'Rest\AttendanceController@creates');
Route::put('/attendance/{attendance}', 'Rest\AttendanceController@update');
Route::put('/attendance/arrive/{attendance}', 'Rest\AttendanceController@arrive'); // 出席
Route::put('/attendance/absence/{attendance}', 'Rest\AttendanceController@absence'); // 欠席
Route::put('/attendance/cancel/{attendance}', 'Rest\AttendanceController@cancel'); // 欠席取消
Route::post('/attendance/arrives', 'Rest\AttendanceController@arrives');
Route::post('/attendance/reparts', 'Rest\AttendanceController@reparts');
Route::put('/attendance/{attendance}', 'Rest\AttendanceController@update');

Route::get('/sheet/contract/count/list', 'Rest\ContractCountController@list');
Route::get('/sheet/contract/count/download', 'Rest\ContractCountController@download');
Route::get('/sheet/contract/month/list', 'Rest\ContractMonthController@list');
Route::get('/sheet/contract/month/download', 'Rest\ContractMonthController@download');
Route::get('/sheet/contract/year/list', 'Rest\ContractYearController@list');
Route::get('/sheet/contract/year/download', 'Rest\ContractYearController@download');
// Route::get('/sheet/contract/count/download/{bizyear?}/{month?}', 'Rest\ContractCountController@download');
Route::get('/sheet/contract/list', 'Rest\ContractListController@list');
Route::get('/sheet/contract/list/download', 'Rest\ContractListController@download');
Route::get('/sheet/contract/child', 'Rest\ContractChildController@list');
Route::get('/sheet/contract/child/download', 'Rest\ContractChildController@download');
Route::get('/sheet/attendance/list', 'Rest\SheetAttendanceController@list');
Route::get('/sheet/attendance/download', 'Rest\SheetAttendanceController@download');
Route::get('/sheet/attendance/total/list', 'Rest\SheetAttendanceTotalController@list');
Route::get('/sheet/attendance/total/download', 'Rest\SheetAttendanceTotalController@download');
Route::get('/sheet/absence/list', 'Rest\SheetAbsenceController@list');
Route::get('/sheet/absence/download', 'Rest\SheetAbsenceController@download');
Route::get('/sheet/attendance/stats/list', 'Rest\SheetAttendanceStatsController@list');
Route::get('/sheet/attendance/stats/download', 'Rest\SheetAttendanceStatsController@download');
Route::get('/mst/calendar', 'Rest\CalendarController@index');
Route::put('/mst/calendar/dayoff/{calendar}', 'Rest\CalendarController@dayoff');
Route::get('/mst/calendar/dayoffs', 'Rest\CalendarController@getdayoffs');
Route::get('/mst/calendar/dayoff', 'Rest\CalendarController@getdayoff');
Route::put('/mst/calendar/save_note/{calendar}', 'Rest\CalendarController@save_note');
Route::get('/mst/calendar/set/bizweek', 'Rest\CalendarController@set_bizweek');


// Route::get('/mst/aim', 'Rest\AimController@index');
Route::get('/mst/aim/days', 'Rest\AimController@biz_weekly_first_days');
Route::get('/mst/aim/week', 'Rest\AimController@week');
Route::put('/mst/aim/save/{aim}', 'Rest\AimController@save');
Route::get('/mst/report', 'Rest\ReportController@index');
Route::get('/mst/report/days', 'Rest\ReportController@biz_week');
Route::put('/mst/report/save/{report}', 'Rest\ReportController@save');
Route::get('/mst/action/monthlist', 'Rest\ActionController@monthlist');
Route::get('/mst/action/bizweeklist', 'Rest\ActionController@bizweeklist');
Route::put('/mst/action/update/{action}', 'Rest\ActionController@update');
Route::put('/mst/action/create', 'Rest\ActionController@create');
Route::put('/mst/action/remove/{action}', 'Rest\ActionController@remove');

Route::get('/mst/event/monthlist', 'Rest\EventController@monthlist');
Route::get('/mst/event/bizweeklist', 'Rest\EventController@bizweeklist');
Route::put('/mst/event/create', 'Rest\EventController@create');
Route::put('/mst/event/create_in', 'Rest\EventController@create_in');
Route::put('/mst/event/update/{event}', 'Rest\EventController@update');
Route::put('/mst/event/remove/{event}', 'Rest\EventController@remove');

Route::get('/mst/classroom', 'Rest\ClassroomController@ready_and_actives');
Route::get('/mst/classroom/all', 'Rest\ClassroomController@all');
Route::put('/mst/classroom/{classroom}', 'Rest\ClassroomController@update');
Route::post('/mst/classroom/create', 'Rest\ClassroomController@create');
Route::get('/mst/rosters/classroom/search', 'Rest\ClassroomController@search_for_mst_roster');
Route::get('/mst/rosters/classroom/child_ids', 'Rest\RosterController@child_ids_for_bizeyar');
Route::put('/mst/rosters/classroom/child/save', 'Rest\RosterController@save');

Route::get('/mst/bizyear', 'Rest\BizyearController@index');
Route::post('/mst/bizyear/save', 'Rest\BizyearController@save');

Route::get('/mst/children', 'Rest\ChildController@index');
Route::get('/mst/children/search', 'Rest\ChildController@search');
Route::post('/mst/children/save', 'Rest\ChildController@save');
Route::get('/mst/children/download', 'Rest\ChildController@download');
Route::delete('/mst/children/{child}', 'Rest\ChildController@delete');
Route::post('/mst/children/import', 'Rest\ChildController@import');

Route::get('/grades', 'Rest\GradeController@list');
Route::get('/grades/desc', 'Rest\GradeController@desc');



//Route::get('/depart', 'Rest\DepartController@index');
Route::get('/depart/bus', 'Rest\DepartController@bus');
Route::get('/depart/daytime', 'Rest\DepartController@daytime');
Route::get('/depart/evening', 'Rest\DepartController@evening');

Route::get('/disease', 'Rest\DiseaseController@index');
Route::get('/eveningtime', 'Rest\EveningTimeController@index');
Route::get('/bus', 'Rest\BusController@index');


Route::get('/city/user', 'Rest\CityUserController@list');
Route::get('/city/user/accesslogs', 'Rest\CityUserController@accessloglist');
Route::post('/city/user/create', 'Rest\CityUserController@create');
Route::put('/city/user/{user}', 'Rest\CityUserController@update');
Route::delete('/city/user/{user}', 'Rest\CityUserController@delete');
Route::post('/login/user', 'Rest\CityUserController@login');
Route::get('/city/information', 'Rest\CityInformationController@list');
Route::post('/city/information/create', 'Rest\CityInformationController@create');
Route::put('/city/information/{information}', 'Rest\CityInformationController@update');
Route::delete('/city/information/{information}', 'Rest\CityInformationController@delete');
Route::get('/city/absence', 'Rest\CityDiseaseController@list');
Route::post('/city/absence/create', 'Rest\CityDiseaseController@create');
Route::put('/city/absence/{disease}', 'Rest\CityDiseaseController@update');
Route::delete('/city/absence/{disease}', 'Rest\CityDiseaseController@delete');

Route::get('/city/event', 'Rest\CityOutEventController@list');
Route::get('/city/event/bizyears', 'Rest\CityOutEventController@bizyears');
Route::get('/city/event/monthlist', 'Rest\CityOutEventController@monthlist');
Route::post('/city/event/create', 'Rest\CityOutEventController@create');
Route::put('/city/event/{event}', 'Rest\CityOutEventController@update');
Route::delete('/city/event/{event}', 'Rest\CityOutEventController@delete');
