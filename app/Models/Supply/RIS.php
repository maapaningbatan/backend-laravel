<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RIS extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_ris';
    protected $primaryKey = 'ris_id';
    public $timestamps = true;

    protected $fillable = [
        'ris_number','responsibility_center','region','office','fund_cluster',
        'ris_date','purpose','requested_by','received_by','approved_by','status'
    ];

    protected $casts = [
        'ris_date' => 'date',
        'status' => 'string',
    ];

    public static function generateRISNumber($regionCode)
{
    $year = now()->year;
    $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);

    // Find last RIS for this region in the same year+month
    $last = self::where('region', $regionCode)
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderByDesc('id')
        ->value('ris_number');

    $nextIncrement = 1;
    if ($last) {
        $parts = explode('-', $last);
        $lastIncrement = intval(end($parts));
        $nextIncrement = $lastIncrement + 1;
    }

    $incrementStr = str_pad($nextIncrement, 4, '0', STR_PAD_LEFT);

    return "{$year}-{$month}-{$regionCode}-{$incrementStr}";
}
}
