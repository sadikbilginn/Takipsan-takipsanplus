<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'read_type_id',
        'device_type',
        'name',
        'reader',
        'reader_mode',
        'estimated_population',
        'search_mode',
        'session',
        'string_set',
        'gpio_start',
        'gpio_stop',
        'gpio_error',
        'printer_address',
        'ip_address',
        'package_timeout',
        'common_power',
        'antennas',
        'auto_print',
        'auto_model_name',
        'created_user_id',
        'updated_user_id',
        'status',
        'barcode_ip_address',
        'barcode_port',
        'barcode_status',
    ];

    public function readType()
    {
        return $this->belongsTo('App\ReadType');
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
