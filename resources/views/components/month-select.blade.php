@php
/**
 *  sheet/contract/child.blade, sheet/list_child.js
 */

use App\Models\Bizyear;
use App\Util\Date;

$bizmonths = Date::BIZ_MONTHS;
@endphp

<div class="d-none">
@include('components/date-select')
</div>

<form class="form-inline mb-2" id="select-form">
    <div class="form-group">
        <label for="yearmonth-select-list">対象</label>
        <span class="ml-2 mr-2">{{ $bizyear }}年度</span>
        <select id="month-select-list" class="form-control" name="month">
            @foreach($bizmonths as $month)
            <option value="{{ $month }}">{{ $month }}月</option>
            @endforeach()
        </select>
    </div>
    <button type="button" class="btn btn-primary" id="yearmonth-search">再読込</button>
</form>
