<?php

namespace App\Util;

class Date
{
    const WEEK_NAMES = ['日', '月', '火', '水', '木', '金', '土'];
    const BIZ_MONTHS = [4,5,6,7,8,9,10,11,12,1,2,3];
    const WEEK_MON = 1;

    public static function is_sun($week_idx)
    {
        return ($week_idx == 0);
    }
    public static function is_mon($week_idx)
    {
        return ($week_idx == self::WEEK_MON);
    }
    public static function is_sat($week_idx)
    {
        return ($week_idx == 6);
    }

    public static function week($date)
    {
        $week_idx = self::week_idx($date);
        return self::WEEK_NAMES[$week_idx]? self::WEEK_NAMES[$week_idx]: '';
    }

    public static function week_idx($date)
    {
        return (int) date('w', strtotime($date));
    }

    /**
     * @param string $date
     * @return string bizyear
     */
    public static function bizyear($date)
    {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        return ($month > 3)? (int)$year: $year - 1;
    }

    /**
     * 年度と月から実際の年を返却  2020年度1月 → 2021年
     * @param string $bizyear
     * @param string $montn
     * @return string year
     */
    public static function bizyear_to_year($bizyear, $month)
    {
        return ($month > 3)? $bizyear: $bizyear + 1;
    }

    /**
     * 指定した年月の末日
     * @param string $year
     * @param string $montn
     * @return string lastday
     */
    public static function lastday($year, $month)
    {
        return date('t', strtotime($year.'-'.$month.'-1'));
    }

    /**
     * @param string $date
     * @return string month
     */
    public static function month($date)
    {
        return date('n', strtotime($date));
    }

    /**
     * @param string $year
     * @param string $month
     * @return string year-month
     */
    public static function year_month($year, $month)
    {
        return $year . '-' . sprintf('%02d', $month);
    }

    /**
     * @param string $year
     * @param string $month
     * @param string $day
     * @return string year-month-day
     */
    public static function date($year, $month, $day)
    {
        //return $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day);
        $date = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day);
        return date('Y-m-d', strtotime($date));
    }
}
