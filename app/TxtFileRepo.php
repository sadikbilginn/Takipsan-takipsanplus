<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TxtFileRepo extends Model{

    protected $fillable = [
        'xmlid',
        'recordType',
        'buid',
        'poVersionNumber',
        'poStatusType',
        'dateLastAmended',
        'departmentCode',
        'colourCode',
        'departmentDesc',
        'strokeDesc',
        'colourDesc',
        'story',
        'commodityCode',
        'season',
        'phase',
        'deliveryDate',
        'cargoReadyDate',
        'franchiseOrder',
        'manufacturerCode',
        'manufacturerDesc',
        'factoryCode',
        'factoryDescription',
        'freightManagerDesc',
        'paymentMethod',
        'paymentTerms',
        'incotermType',
        'contractNo',
        'countryId',
        'countryDescription',
        'region',
        'portLoadingCode',
        'freightId',
        'freightDesc',
        'paymentCurrency',
        'shipmentMethod',
        'reprocessorIndicator',
        'orderNotes',
        'finalWarehouseId',
        'finalWarehouseDesc',
        'destination',
        'status',
    ];

    public function xml_file_repos()
    {
        return $this->belongsTo('App\XmlFileRepo');
    }
    
}
