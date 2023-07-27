@php
/**
 *  mst/calendar.blade, mst/calendar.js
 *  sheet/contract/child.blade, sheet/list_child.js
 *  sheet/contract/count.blade, sheet/list.js
 *  sheet/contract/list.blade, sheet/list_link.js
 *  sheet/contract/month.blade, sheet/list.js
 */

use App\Models\Bizyear;
use App\Util\Date;

if(defined('IS_COMPONENTS_PARAM_READY_AND_ACTIVE')) :
    $bizyears = Bizyear::ready_and_actives();
else :
    $bizyears = Bizyear::actives();
endif;
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
    </div>
    <button type="button" class="btn btn-primary" id="yearmonth-search">再読込</button>
</form>
