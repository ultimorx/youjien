<?php

namespace App\Models;


use App\Models\Attendance;
use App\Models\Child;
use App\Models\Grade;
use App\Models\Calendar;

use App\Util\Date;
use App\Util\Sess;

// use Illuminate\Http\Request;
//use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class Roster extends SoftDeleteModel
{
    const BUS_ID_DEFAULT = 0;

    // DB切替処理のsession取得の検証コード
    // public function __construct($attributes = array())
    // {
    //   parent::__construct($attributes);
    //   $test = \Sess::get_test();
    //   $kindergarten_id = \Sess::get_kindergarten_id();
    //   \Log::info('test : '.$test);
    //   $dd = debug_backtrace();
    //   \Log::info('Model_Roster dd : ', $dd[1]);
    //   \Log::info('Model_Roster RRR', [$this->connection, $kindergarten_id]);
    //   \Log::info(' ');
    // }


    protected $attributes = [
        'bus_id' => 0
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function contract_mornings()
    {
        return $this->hasMany(ContractMorning::class);
    }

    public function contract_evenings()
    {
        return $this->hasMany(ContractEvening::class);
    }

    public function contract_arrive_bus()
    {
        return $this->hasMany(ContractArriveBus::class);
    }

    public function contract_depart_bus()
    {
        return $this->hasMany(ContractDepartBus::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * 年度指定で出席簿取得
     */
    public static function bizyear($bizyear)
    {
        return self::query()
            ->where('bizyear', '=', $bizyear)
            ->orderBy('classroom_id', 'asc')
            ->orderBy('number', 'asc')
            ->orderBy('child_id', 'asc')
            ->get();
    }

    /**
     * クラス指定で出席簿取得
     * 生年月日順
     */
    public static function sort_birthday($classroom_id)
    {
        return self::query()
            ->select('rosters.*')
            ->where('rosters.classroom_id', '=', $classroom_id)
            ->join('children', 'children.id', '=', 'rosters.child_id')
            ->orderBy('children.birthday', 'asc')
            ->get();
    }

    /**
     * 契約者一覧
     */
    public static function contractlist($date, $is_morning=false, $is_evening=false)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $query = Roster::with([
                'classroom',
                'classroom.grade'
            ])
            ->select('rosters.*')
            ->join('children', 'children.id', '=', 'rosters.child_id')
                // 転出日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_out_date', '=', Child::MOVE_OUT_DATE_DEFAULT)
                    ->orWhere('children.move_out_date', '>', $date)
                    ;
                })
                // 転入日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_in_date', '=', Child::MOVE_IN_DATE_DEFAULT)
                    ->orWhere('children.move_in_date', '<=', $date)
                    ;
                })
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                //年度
                ->where('classrooms.bizyear', '=', $bizyear)
            // ->join('grades', 'grades.id', '=', 'classrooms.grade_id')
            ->orderBy('rosters.classroom_id')
            ->orderBy('rosters.number')
            ->orderBy('rosters.id')
            ;

            if($is_morning) {
                $query
                    ->join('contract_mornings', 'contract_mornings.roster_id', '=', 'rosters.id')
                    ->where('contract_mornings.month', '=', $month)
                ;
            }

            if($is_evening) {
                $query
                    ->join('contract_evenings', 'contract_evenings.roster_id', '=', 'rosters.id')
                    ->where('contract_evenings.month', '=', $month)
                ;
            }

        return $query->get();
    }

    /**
     * 早朝一覧
     */
    public static function earlylist($date, $morning_time_id)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $attendances = Attendance::query()->where('date', '=', $date);
        $query = Self::with([
                'attendance' => function ($query) use ($date) {
                    $query->where('date', '=', $date);
                },
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'contract_mornings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                }
            ])
            ->select('rosters.*')
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
            })
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.bizyear', '=', $bizyear)
            ->join('children', 'children.id', '=', 'rosters.child_id')
                // 転出日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_out_date', '=', Child::MOVE_OUT_DATE_DEFAULT)
                    ->orWhere('children.move_out_date', '>', $date)
                    ;
                })
                // 転入日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_in_date', '=', Child::MOVE_IN_DATE_DEFAULT)
                    ->orWhere('children.move_in_date', '<=', $date)
                    ;
                })
            ->join('contract_mornings', 'contract_mornings.roster_id', '=', 'rosters.id')
                ->where('contract_mornings.month', '=', $month)
                ->where('contract_mornings.morning_time_id', '=', $morning_time_id)

            // 休日 start
            ->join('calendars', 'calendars.date', '=', 'attendances.date')
            ->join('grades', 'grades.id', '=', 'classrooms.grade_id')
                ->where(function($query) {
                    $query
                    ->where(function($query) {
                        $query
                        ->where('grades.age_type', '=', Grade::IJYOUJI_AGE_TYPE)
                        ->where('calendars.ijyouji', '=', Calendar::DAYOFF_FALSE)
                        ;
                    })
                    ->orWhere(function($query) {
                        $query
                        ->where('grades.age_type', '=', Grade::MIMANJI_AGE_TYPE)
                        ->where('calendars.mimanji', '=', Calendar::DAYOFF_FALSE)
                        ;
                    })
                    ;
                })
            // 休日 end

            ->orderBy('classrooms.order', 'asc')
            ->orderBy('rosters.number', 'asc')
        ;

        return $query->get();
    }

    /**
     * クラス名簿
     */
    public static function classrooms($date, $classroom_id)
    {
        $month = Date::month($date);
        $attendances = Attendance::query()->where('date', '=', $date);

        $query = Self::with([
                'attendance' => function ($query) use ($date) {
                    $query->where('date', '=', $date);
                },
                'child',
                'classroom',
                'classroom.grade',
                'contract_mornings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                },
                'contract_evenings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                },
                'contract_depart_bus' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                }
            ])
            ->select('rosters.*')
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
            })
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.id', '=', $classroom_id)
            ->join('children', 'children.id', '=', 'rosters.child_id')
                // 転出日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_out_date', '=', Child::MOVE_OUT_DATE_DEFAULT)
                    ->orWhere('children.move_out_date', '>', $date)
                    ;
                })
                // 転入日
                ->where(function($query) use($date) {
                    $query
                    ->where('children.move_in_date', '=', Child::MOVE_IN_DATE_DEFAULT)
                    ->orWhere('children.move_in_date', '<=', $date)
                    ;
                })

            // 休日 start
            ->join('calendars', 'calendars.date', '=', 'attendances.date')
            ->join('grades', 'grades.id', '=', 'classrooms.grade_id')
                ->where(function($query) {
                    $query
                    ->where(function($query) {
                        $query
                        ->where('grades.age_type', '=', Grade::IJYOUJI_AGE_TYPE)
                        ->where('calendars.ijyouji', '=', Calendar::DAYOFF_FALSE)
                        ;
                    })
                    ->orWhere(function($query) {
                        $query
                        ->where('grades.age_type', '=', Grade::MIMANJI_AGE_TYPE)
                        ->where('calendars.mimanji', '=', Calendar::DAYOFF_FALSE)
                        ;
                    })
                    ;
                })
            // 休日 end

            ->orderBy('rosters.number', 'asc')
        ;

        return $query->get();
    }

    /**
     * 早朝一覧
     * クラス名簿
     */
    // public static function get_attendances($month=null, $date=null, $morning_time_id=null, $classroom_id=null)
    // {
    //     // $attendances = DB::table('attendances')->where('date', '=', $date);
    //     $attendances = Attendance::query()->where('date', '=', $date);
    //     // $query = DB::table('rosters')
    //     $query = Self::query()
    //         ->select(
    //             'rosters.id as roster_id',
    //             'rosters.number as number',
    //             'grades.name as grade_name',
    //             'classrooms.name as classroom_name',
    //             'children.name as children_name',
    //             'children.kana as children_kana',
    //             'children.gender as children_gender',
    //             'attendances.attendance as attendance',
    //             'attendances.date as attendance_date',
    //             'attendances.late as attendance_late',
    //             'attendances.early as attendance_early',
    //             'attendances.absence_type as attendance_absence_type',
    //             'attendances.diseases_id as attendance_diseases_id',
    //             'attendances.morning_using as attendance_morning_using',
    //             'attendances.evening_time_id as attendance_evening_time_id',
    //             'attendances.outtime as attendance_outtime',
    //             'attendances.bus_id as attendance_bus_id',
    //             'attendances.pick_up as attendance_pick_up',
    //             'attendances.id as attendance_id'
    //         )
    //         ->joinSub($attendances, 'attendances', function ($join) {
    //             $join->on('rosters.id', '=', 'attendances.roster_id');
    //             })
    //         ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
    //         ->join('grades', 'grades.id', '=', 'classrooms.grade_id')
    //         ->join('children', 'children.id', '=', 'rosters.child_id')
    //         ->orderBy('classrooms.order');
    //
    //     if($month || $morning_time_id)
    //     {
    //         $query->join('contract_mornings', 'contract_mornings.roster_id', '=', 'rosters.id');
    //     }
    //     if($month)
    //     {
    //         $query->where('contract_mornings.month', '=', $month);
    //     }
    //     if($morning_time_id)
    //     {
    //         $query->where('contract_mornings.morning_time_id', '=', $morning_time_id);
    //     }
    //     if($classroom_id)
    //     {
    //         $query->where('classrooms.id', '=', $classroom_id);
    //     }
    //
    //     return $query->get();
    // }

    /**
     * 早朝一覧
     * クラス名簿
     */
    public static function get_attendances($date, $morning_time_id)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $attendances = Attendance::query()->where('date', '=', $date);
        $query = Self::with([
                'attendance' => function ($query) use ($date) {
                    $query->where('date', '=', $date);
                },
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'contract_mornings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                }
            ])
            ->select(
                // 'rosters.id as roster_id',
                'rosters.*'
            )
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
                })
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
            ->join('grades', 'grades.id', '=', 'classrooms.grade_id')
            ->join('children', 'children.id', '=', 'rosters.child_id')
            ->orderBy('classrooms.order')

            ;

        if($month || $morning_time_id)
        {
            $query->join('contract_mornings', 'contract_mornings.roster_id', '=', 'rosters.id');
        }
        if($month)
        {
            $query->where('contract_mornings.month', '=', $month);
        }
        if($morning_time_id)
        {
            $query->where('contract_mornings.morning_time_id', '=', $morning_time_id);
        }
        if($classroom_id)
        {
            $query->where('classrooms.id', '=', $classroom_id);
        }
// dd($query->toSql());
        return $query->get();
    }

    /**
     * 降園者の一覧 バス
     *   出欠：出席
     *   バス：あり
     *
     * @param  $date
     * @return orm
     */
    public static function buslist($date)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $attendances = Attendance::query()
            // 日付指定、延長あり
            ->where('date', '=', $date)
            ->where('attendance', Attendance::ATTENDANCE_TRUE)
            ->where('bus_id', '<>', Attendance::BUS_ID_DEFAULT)
        ;

        // 該当月の契約がある園児をバスごとにまとめる
        $group = Self::with([
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'attendance' => function ($query) use ($date) {
                    $query->whereDate('date', $date)
                        ->where('attendance', Attendance::ATTENDANCE_TRUE)
                        ->where('bus_id', '<>', Attendance::BUS_ID_DEFAULT)
                        ;
                },
                'attendances.bus'
            ])
            ->select('rosters.*')
            // 日付指定
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
            })
            // 年度指定
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.bizyear', '=', $bizyear)
            ->orderBy('classrooms.order', 'asc')
            ->orderBy('rosters.number', 'asc')
            ->get()
            ->groupBy('attendance.bus_id'); // bus_idが配列のキー
        $buslist = Bus::query()
            ->orderBy('order')
            ->get()
            ->each(function ($bus) use ($group) {
                $bus->rosters = $group[$bus->id] ?? null; // 利用者を紐付け
            })->reject(function ($bus) {
                return empty($bus->rosters); // 利用者がいないバスを除外
            });

        return $buslist;
    }

    /**
     * 降園者の一覧 お迎え
     *   出欠：出席
     *   バス：なし
     *   延長：なし
     *
     * @param  $date
     * @return orm
     */
    public static function daytimelist($date)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $attendances = Attendance::query()
            ->where('date', '=', $date)
            ->where('attendance', Attendance::ATTENDANCE_TRUE)
            ->where('bus_id', '=', Attendance::BUS_ID_DEFAULT)
            ->where('evening_time_id', '=', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
        ;
        $query = Self::with([
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'attendance' => function ($query) use ($date) {
                    $query->whereDate('date', $date)
                        ->where('attendance', Attendance::ATTENDANCE_TRUE)
                        ->where('bus_id', '=', Attendance::BUS_ID_DEFAULT)
                        ->where('evening_time_id', '=', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
                        ;
                },
            ])
            ->select('rosters.*')
            // 日付指定
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
            })
            // 年度指定
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.bizyear', '=', $bizyear)
            ->orderBy('classrooms.order', 'asc')
            ->orderBy('rosters.number', 'asc')
        ;

        return $query->get();
    }

    /**
     * 降園者の一覧 延長
     *   出欠：出席
     *   延長：あり
     *
     * @param  $date
     * @param  $evening_time_id
     * @return orm
     */
    public static function eveninglist($date, $evening_time_id)
    {
        $month = Date::month($date);
        $bizyear = Date::bizyear($date);

        $attendances = Attendance::query()
            // 日付指定、延長あり
            ->where('date', '=', $date)
            ->where('attendance', Attendance::ATTENDANCE_TRUE)
            ->where('evening_time_id', '<>', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
        ;
        $query = Self::with([
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'attendance' => function ($query) use ($date) {
                    $query->whereDate('date', $date)
                        ->where('attendance', Attendance::ATTENDANCE_TRUE)
                        ->where('evening_time_id', '<>', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
                        ;
                }
            ])
            ->select('rosters.*')
            // 延長保育契約あり
            ->join('contract_evenings', 'contract_evenings.roster_id', '=', 'rosters.id')
                ->where('contract_evenings.month', '=', $month)
                ->where('contract_evenings.evening_time_id', '=', $evening_time_id)
            // 日付指定
            ->joinSub($attendances, 'attendances', function ($join) {
                $join->on('rosters.id', '=', 'attendances.roster_id');
            })
            // 年度指定
            ->join('classrooms', 'classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.bizyear', '=', $bizyear)
            ->orderBy('classrooms.order', 'asc')
            ->orderBy('rosters.number', 'asc')
        ;

        return $query->get();
    }

    /**
     * 園児の契約
     */
    public static function contract_month($roster_id, $month)
    {
        $query = Self::with([
                'child',
                'classroom',
                'classroom.grade',
                'contract_mornings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                },
                'contract_evenings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                },
                'contract_depart_bus' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                }
            ])
            ->select(
                'rosters.*',
                'contract_mornings.morning_time_id',
                'morning_times.time as morning_time',
                'contract_evenings.evening_time_id',
                'evening_times.time as evening_time',
                'buses.name as depart_bus_name',
                'contract_arrive_buses.month as contract_arrive_bus_month',
                'contract_depart_buses.month as contract_depart_bus_month'
                )
            ->leftJoin('contract_mornings', function ($join) use($month)  { // 外部結合
                $join
                ->on('contract_mornings.roster_id', '=', 'rosters.id')
                ->where('contract_mornings.month', '=', $month)
                ;
            })
            ->leftJoin('contract_evenings', function ($join) use($month) { // 外部結合
                $join
                ->on('contract_evenings.roster_id', '=', 'rosters.id')
                ->where('contract_evenings.month', '=', $month)
                ;
            })
            ->leftJoin('contract_arrive_buses', function ($join) use($month) { // 外部結合
                $join
                ->on('contract_arrive_buses.roster_id', '=', 'rosters.id')
                ->where('contract_arrive_buses.month', '=', $month)
                ;
            })
            ->leftJoin('contract_depart_buses', function ($join) use($month) { // 外部結合
                $join
                ->on('contract_depart_buses.roster_id', '=', 'rosters.id')
                ->where('contract_depart_buses.month', '=', $month)
                ;
            })
            ->leftJoin('morning_times', 'morning_times.id', '=', 'contract_mornings.morning_time_id')
            ->leftJoin('evening_times', 'evening_times.id', '=', 'contract_evenings.evening_time_id')
            ->leftJoin('buses', 'buses.id', '=', 'rosters.bus_id')
            ->where('rosters.id', '=', $roster_id)
            ->orderBy('rosters.number', 'asc')
        ;

        return $query->first();
    }

}
