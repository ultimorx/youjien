@php
/**
 *  attendance/class.blade, attendance.js
 *  children.blade, children.js
 */

use App\Models\Grade;
use App\Models\Classroom;
use App\Models\Bizyear;
use App\Util\Date;

if(defined('IS_COMPONENTS_PARAM_READY_AND_ACTIVE')) :
    $classrooms = Classroom::ready_and_actives();
    $is_multi_active = Classroom::is_multi_ready_and_actives(); // bizyear.run=active or ready && classroom exist
else :
    $classrooms = Classroom::actives();
    $is_multi_active = Bizyear::is_multi_active(); // bizyear.run=active only
endif;

$bizmonths = Date::BIZ_MONTHS;
$today = date('Y-m-d');
$current_bizyear = Date::bizyear($today);
$is_empty_selected = true;
@endphp

<form class="form-inline mb-2" id="classroom-select-form">
    <div class="form-group">
        <label for="classroom-select-list">クラス</label>
        <select id="classroom-select-list" class="form-control" name="class">
            <!-- <option value="" selected>選択...</option> -->
            @foreach($classrooms as $classroom)
            @php
            if ($is_empty_selected && $classroom->bizyear == $current_bizyear) :
                $selected = 'selected';
                $is_empty_selected = false;
            else :
                $selected = '';
            endif;
            @endphp
            <option value="{{ $classroom->id }}" data-teacher="{{ $classroom->teacher }}" data-grade-id="{{ $classroom->grade->id }}" data-grade-name="{{ $classroom->grade->name }}" data-classroom-name="{{ $classroom->name }}" data-grade-age-type="{{ Grade::age_type_name($classroom->grade) }}" {{ $selected }}>
                @if ($is_multi_active)
                {{ $classroom->bizyear }}年度　
                @endif
                 {{ $classroom->name }}
                 （{{ $classroom->grade->name }}）
            </option>
            @endforeach()
        </select>
    </div>
    <button type="button" class="btn btn-primary" id="classroom-search">再読込</button>
</form>
