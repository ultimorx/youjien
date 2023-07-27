@php
/**
 *  sheet/contract/absence.blade, sheet/list_yearmonth_classroom.js
 *  sheet/contract/attendance.blade, sheet/list_yearmonth_classroom.js
 *  sheet/contract/attendance/total.blade, sheet/list_yearmonth_classroom.js
 */

use App\Models\Classroom;
use App\Models\Bizyear;
use App\Util\Date;

$classrooms = Classroom::actives();
$bizyears = Bizyear::actives();
// $is_multi_active = Bizyear::is_multi_active();
$is_multi_active = false;
$bizmonths = Date::BIZ_MONTHS;
$today = date('Y-m-d');
$current_bizyear = Date::bizyear($today);
$current_month = date('n');
@endphp

<div class="d-none">
@include('components/date-select')
</div>

<form class="form-inline mb-2" id="yearmonth-select-form">
    <div class="form-group">
        <label for="yearmonth-select-list">対象</label>
        <select id="year-select-list" class="form-control" name="year">
            @foreach($bizyears as $bizyear)
            <option value="{{ $bizyear->bizyear }}" {{ ($bizyear->bizyear == $current_bizyear)? "selected": "" }}>{{ $bizyear->bizyear }}年度</option>
            @endforeach()
        </select>
        <select id="month-select-list" class="form-control" name="month">
            @foreach($bizmonths as $month)
            <option value="{{ $month }}" {{ ($month == $current_month)? "selected": "" }}>{{ $month }}月</option>
            @endforeach()
        </select>
        <select id="classroom-select-list" class="form-control" name="classroom">
            @if(isset($all_classroom))
            <option value="0">園全体</option>
            @endif()
            @foreach($classrooms as $classroom)
            <option value="{{ $classroom->id }}" data-teacher="{{ $classroom->teacher }}" data-grade-name="{{ $classroom->grade->name }}" data-classroom-name="{{ $classroom->name }}" data-bizyear="{{ $classroom->bizyear }}">
                @if ($is_multi_active)
                {{ $classroom->bizyear }}年度　
                @endif
                 {{ $classroom->name }}
                 <!-- （{{ $classroom->grade->name }}） -->
            </option>
            @endforeach()
        </select>
    </div>
    <button type="button" class="btn btn-primary ml-0" id="yearmonth-search">再読込</button>
</form>
