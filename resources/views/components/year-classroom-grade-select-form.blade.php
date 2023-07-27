@php
/**
 *  sheet/contract/attendance/stats.blade, sheet/list_year_classroom_grade.js
 */

use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Bizyear;
use App\Util\Date;

$grades = Grade::list();
$classrooms = Classroom::actives();
$bizyears = Bizyear::actives();
// $is_multi_active = Bizyear::is_multi_active();
$is_multi_active = false;
$bizmonths = Date::BIZ_MONTHS;
$today = date('Y-m-d');
$current_bizyear = Date::bizyear($today);
@endphp

<div class="d-none">
@include('components/date-select')
</div>

<form class="form-inline mb-2" id="classroom-select-form">
    <div class="form-group">
        <label for="classroom-select-list">対象</label>
        <select id="year-select-list" class="form-control" name="year">
            @foreach($bizyears as $bizyear)
            <option value="{{ $bizyear->bizyear }}" {{ ($bizyear->bizyear == $current_bizyear)? "selected": "" }}>{{ $bizyear->bizyear }}年度</option>
            @endforeach()
        </select>
        <select id="classroom-select-list" class="form-control" name="class">
            <!-- <option value="" selected>選択...</option> -->
            @foreach($classrooms as $classroom)
            <option value="{{ $classroom->id }}" data-type="classroom" data-bizyear="{{ $classroom->bizyear }}">
                @if ($is_multi_active)
                {{ $classroom->bizyear }}年度　
                @endif
                 {{ $classroom->name }}
            </option>
            @endforeach()
            @foreach($grades as $grade)
            <option value="{{ $grade->id }}" data-type="grade">
                 {{ $grade->name }}
            </option>
            @endforeach()
        </select>
    </div>
    <button type="button" class="btn btn-primary" id="search">再読込</button>
</form>
