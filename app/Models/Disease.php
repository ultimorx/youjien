<?php
// 2022年度改修により、
// 各園DBのdiseasesテーブルを未使用とし、
// 全園共通のdiseasesテーブルで管理を行う。

namespace App\Models;

// class Disease extends SoftDeleteModel // 22.9.12 無効化
class Disease extends CityDisease
{
}
