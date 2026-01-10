<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_name',
        'from_date',
        'to_date',
        'day',
        'remark',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    /**
     * Get all attendances for this holiday
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if a given date is a holiday
     * 
     * @param string|Carbon $date
     * @return bool
     */
    public static function isHoliday($date)
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        return self::where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->exists();
    }

    /**
     * Get holiday for a specific date
     * 
     * @param string|Carbon $date
     * @return Holiday|null
     */
    public static function getHoliday($date)
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        return self::where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();
    }

    /**
     * Get all holidays for the current month
     */
    public function scopeCurrentMonth($query)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('from_date', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('to_date', [$startOfMonth, $endOfMonth])
                ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                    $q2->where('from_date', '<=', $startOfMonth)
                        ->where('to_date', '>=', $endOfMonth);
                });
        });
    }

    /**
     * Get all holidays for a specific date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('from_date', [$startDate, $endDate])
                ->orWhereBetween('to_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('from_date', '<=', $startDate)
                        ->where('to_date', '>=', $endDate);
                });
        });
    }

    /**
     * Get upcoming holidays
     */
    public function scopeUpcoming($query, $limit = 5)
    {
        return $query->where('from_date', '>=', Carbon::today())
            ->orderBy('from_date', 'asc')
            ->limit($limit);
    }

    /**
     * Get the number of days for this holiday
     */
    public function getDurationAttribute()
    {
        if ($this->from_date && $this->to_date) {
            return $this->from_date->diffInDays($this->to_date) + 1;
        }
        return $this->day ?? 1;
    }

    /**
     * Check if the holiday is currently active
     */
    public function isActiveAttribute()
    {
        $today = Carbon::today();
        return $today->between($this->from_date, $this->to_date);
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute()
    {
        if ($this->from_date->eq($this->to_date)) {
            return $this->from_date->format('M d, Y');
        }
        return $this->from_date->format('M d') . ' - ' . $this->to_date->format('M d, Y');
    }
}