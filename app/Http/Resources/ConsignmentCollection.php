<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConsignmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return[
            'status' => 'ok',
            'test' => 'testMesaj',
            'ad' => $this->ad,
            'soyad' => $this->soyad
            //'url' => route('station.index', ['consignment' => $consignment->id])
        ];
    }
}
