<?php

namespace App\Models;

use App\Jobs\SendEbondEmailJob;
use App\Jobs\SendEbondSMSJob;
use App\Services\SMSTemplateParser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maize\Searchable\HasSearch;

class Ebond extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function getSearchableAttributes(): array
    {
        return [
            'e_bond_no',
            'erp_order_id',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function salesAgreement(): BelongsTo
    {
        return $this->belongsTo(SalesAgreement::class, 'sales_agreement_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'e_bond_no', 'e_bond_no');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isPenalty()
    {
        return $this->penalty;
    }

    public function isPaid()
    {
        return $this->remaining_amount <= 0;
    }

    public function isDueDate()
    {
        $date = Carbon::parse($this->due_date);
        $currentDate = Carbon::now();

        return $date->lessThanOrEqualTo($currentDate);
    }

    public function updateRemainingAmount()
    {
        $payments = OrderPayment::where('e_bond_no', $this->e_bond_no)->where('failed', false)->sum('collected_payment');
        $remainingAmount = $this->bond_amount - $payments;

        $this->update([
            'remaining_amount' => $remainingAmount < 0 ? 0 : $remainingAmount
        ]);
    }

    // Method to get all records where due_date is one day after now
    public static function getDueDateEbondsOneDayAfter()
    {
        $oneDayBefore = Carbon::tomorrow();

        return self::whereDate('due_date', '=', $oneDayBefore)
            ->where('remaining_amount', '>', 0)
            ->get();
    }

    // Method to get all records where due_date is today
    public static function getDueDateEbondsToday()
    {
        $today = Carbon::today();

        return self::whereDate('due_date', '=', $today)
            ->where('remaining_amount', '>', 0)
            ->get();
    }

    // Method to get all records where due_date is past one or more days after the due_date
    public static function getDueDateEbondsPastDue()
    {
        $oneDayAfter = Carbon::yesterday();

        return self::whereDate('due_date', '=', $oneDayAfter)
            ->where('remaining_amount', '>', 0)
            ->get();
    }

    public function sendCreatedNotification()
    {
        dispatch(new SendEbondSMSJob($this, 'created'));
        dispatch(new SendEbondEmailJob($this, 'created'));
    }

    public function sendBeforeDueDateNotification()
    {
        dispatch(new SendEbondSMSJob($this, 'beforeDueDate'));
        dispatch(new SendEbondEmailJob($this, 'beforeDueDate'));
    }

    public function sendDueDateNotification()
    {
        dispatch(new SendEbondSMSJob($this, 'onDueDate'));
        dispatch(new SendEbondEmailJob($this, 'onDueDate'));
    }

    public function sendAfterDueDateNotification()
    {
        dispatch(new SendEbondSMSJob($this, 'afterDueDate'));
        dispatch(new SendEbondEmailJob($this, 'afterDueDate'));
    }

    public function sendPenaltyNotification()
    {
        dispatch(new SendEbondSMSJob($this, 'penalty'));
        dispatch(new SendEbondEmailJob($this, 'penalty'));
    }
}
