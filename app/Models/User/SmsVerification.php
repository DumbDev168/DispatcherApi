<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsVerification extends Model
{
    use HasFactory;
    protected  $table="sms_verifications";
    public $timestamps = false;
    protected $guarded = ['id'];

    public function user() : BelongsTo{
        return $this->belongsTo(User::class,'phone_number');
    }
}
