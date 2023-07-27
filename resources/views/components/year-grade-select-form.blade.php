@php
/**
 *  mst/children.blade, mst/children.js
 *  mst/rosters.blade, mst/roster.js
 */

use App\Models\Grade;
use App\Models\Bizyear;
use App\Util\Date;
use App\Util\Str;

$grades = Grade::list();
$bizyears = Bizyear::ready_and_actives();
$bizmonths = Date::BIZ_MONTHS;

$today = date('Y-m-d');
$current_bizyear = Date::bizyear($today);
@endphp

<div class="d-none">
@include('components/date-select')
</div>

<form class="form-inline mb-2" id="search" v-cloak>
    <div class="form-group">
        <label for="select-bizyear">年度</label>
        <select id="select-bizyear" class="form-control">
            @foreach($bizyears as $bizyear)
            <option value="{{ $bizyear->bizyear }}" {{ ($bizyear->bizyear == $current_bizyear)? "selected": "" }}>
                 {{ $bizyear->bizyear }}年度
            </option>
            @endforeach()
        </select>
        <label for="select-grade" class="ml-3">学年</label>
        <select id="select-grade" class="form-control">
            <option value="" selected>全て</option>
            @foreach($grades as $grade)
            <option value="{{ Grade::create_search_param($grade->age, $grade->age+1, $grade->id, $grade->name) }}">
                 {{ $grade->name }}
            </option>
            @endforeach()
            <option value="{{ Grade::create_search_param(3, 6, 0, '以上児') }}">以上児</option>
            <option value="{{ Grade::create_search_param(0, 2, 0, '未満児') }}">未満児</option>
        </select>
    </div>
    <span class="ml-3 unit_person">@{{ count }}</span>
</form>
