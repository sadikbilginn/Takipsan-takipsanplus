<?php
/**
 * Created by Mehmet Karabulut
 * Date :  22.07.2019
 * Time :  09:54
 */

namespace App\Helpers;


trait ElasticSearchTrait
{

    /**
     * Arama için gelen string veriden tırnak işaretleri vb...
     * alanları temizleyerek sql injection vs.. gibi konuların önüne geçer.
     *
     * @$keyword  string
     * @return string
     */
    public function keywordSearchFilter($keyword):string
    {

        $badWords = ["'",'"'];

        return str_replace($badWords, '', $keyword);
    }

    /**
     * Wildcard search verilerini search için düzenler.
     *
     * @keyword string
     * @params array
     * @return array
     */
    public function createWildcardKeyword($keyword, $params):array
    {

        $wildcard = [];

        foreach (explode(' ', $keyword) as $keyword){
            foreach ($params as $key => $value){

                $wildcard[] = [
                    'wildcard' => [
                        $value => "*".$this->keywordSearchFilter($keyword)."*"
                    ]
                ];

                //Benzer kayıtlar arama ekleniyor.
//                $wildcard[] = [
//                    'fuzzy' => [
//                        $value => $this->keywordSearchFilter($keyword)
//                    ]
//                ];
            }
        }

        return $wildcard;
    }

    /**
     * Arama için gelen verileri wildcard search'e uygun hale getirir.
     *
     * @index string
     * @keyword string
     * @params array
     * @from integer
     * @size integer
     * @return array
     */
    public function createWildcardSearch($index, $keyword = '', $params = [], $from = null, $size = null):array
    {

        $param = [
            'index' => $index,
            'body'  => [
                'query' => [
                    'bool' => [
                        'should' => $this->createWildcardKeyword($keyword, $params)
                    ]
                ],
                "sort" => [
                    [
                        "id" =>  [
                            "order" => "desc"
                        ]
                    ]
                ]
            ]
        ];

        if(is_numeric($from) && is_numeric($size))
        {
           $param['body']['from'] = $from;
           $param['body']['size'] = $size;
        }

        return $param;
    }

}