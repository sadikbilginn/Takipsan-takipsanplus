<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyTargetDb extends Model
{
    protected $fillable = [
        'consignment_id',
        'order',
        'user_id',
        'po_number',
        'vendor_id',
        'department',
        'class',
        'item',
        'dpc_no',
        'item_description',
        'vendor_style',
        'color',
        'size',
        'item_barcode',
        'vcp_quantity',
        'ssp_quantity',
        'total_item_qty',
        'item_unit_cost',
        'item_unit_retail',
        'country_origin',
        'hs_tariff',
        'shipping_documents',
        'assortment_item',
        'component_department',
        'component_class',
        'component_item',
        'component_item_desciprtion',
        'component_style',
        'component_assort_qty',
        'component_item_total_qty',
        'item_changed_date',
    ];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function packages()
    {
        return $this->hasMany('App\Package');
    }

    public function items()
    {
        return $this->hasMany('App\Item');
    }

    public function created_user()
    {
        return $this->belongsTo('App\User');
    }
}
