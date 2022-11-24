<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class exportMSreport implements FromView, ShouldAutoSize, WithEvents
{
    public $consignmentId;
    private $PackageReportLenght;
    private $PackageReportYLenght;
    private $tablesize;


    function __construct($consignmentId)
    {
        $this->consignmentId = $consignmentId;
    }

    public function view(): View
    {
        $poNumber = DB::table('company_ms_dbs')->where(['consignment_id' => $this->consignmentId])->first();
        $fixVal = DB::table('xml_file_repos')
            ->select('*')
            ->join('txt_file_repos', function ($join) {
                $join->on('txt_file_repos.xmlid', '=', 'xml_file_repos.id');
            })
            ->where('xml_file_repos.poNumber', $poNumber->order)
            ->orderBy('xml_file_repos.id', 'ASC')
            ->get();

        $colourName = $fixVal[0]->colourCode . ' - ' . $fixVal[0]->colourDesc;

        $columnName = DB::table('ms_upc_cartons')->where(['consignment_id' => $this->consignmentId])
            ->select("upc", DB::raw("CONCAT(upc,'<br>',size) AS columName"))->get();
        $arrayTable = [];
        $arrayTh = ["portal.cartons"];
        foreach ($columnName as $column) {
            array_push($arrayTh, $column->columName . '<br>' . $colourName);
        }
        array_push($arrayTh, 'station.target_qty', 'station.invalidQuantity', 'portal.cartons', 'portal.total');
        array_push($arrayTh,);
        $arrayTable ['th'] = $arrayTh;
        $this->PackageReportLenght = count($arrayTable['th']);
        foreach ($columnName as $column) {
            $row = [];
            $model = DB::table('ms_cartons')
                ->select(DB::raw("group_concat(DISTINCT cartonID) as carton "))
                ->where(['upc' => $column->upc])
                ->where(['consignment_id' => $this->consignmentId])
                ->groupBy('upc')
                ->first();
            $row['carton'] = $model->carton;

            $sumofTargetQty = DB::table('ms_cartons')
                ->select(DB::raw("case when sum(singles)>0 then sum(singles) else '0'  end as sumofCount"))
                ->where(['consignment_id' => $this->consignmentId])
                ->where(['upc' => $column->upc])
                ->whereIn('cartonID', explode(',', $model->carton))->first();
            $row['picies'] = (int)$sumofTargetQty->sumofCount;

            $countofCartons = DB::table('ms_cartons')
                ->select(DB::raw("count(*) as count"))
                ->where(['consignment_id' => $this->consignmentId])
                ->where(['upc' => $column->upc])
                ->whereIn('cartonID', explode(',', $model->carton))->first();
            $row['countofCartons'] = (int)$countofCartons->count;
            $rowTotal = 0;
            $rowInvalid = 0;
            foreach ($columnName as $column) {
                $countModel = DB::table('ms_carton_epcs')
                    ->select(DB::raw('count(*) as count'))
                    ->where([
                        'consigment_id' => $this->consignmentId,
                        'upc' => $column->columName,
                        'gittinCheck' => 1
                    ])->whereIn('barcode',
                        DB::table('ms_cartons')->select('barcode')
                            ->where(['consignment_id' => $this->consignmentId])
                            ->where(['upc' => $column->upc])
                            ->whereIn('cartonID', explode(',', $model->carton))
                    )->first();
                $countInvalidModel = DB::table('ms_carton_epcs')
                    ->select(DB::raw('count(*) as count'))
                    ->where([
                        'consigment_id' => $this->consignmentId,
                        'upc' => $column->columName,
                        'gittinCheck' => 0
                    ])->whereIn('barcode',
                        DB::table('ms_cartons')->select('barcode')
                            ->where(['consignment_id' => $this->consignmentId])
                            ->where(['upc' => $column->upc])
                            ->whereIn('cartonID', explode(',', $model->carton))
                    )->first();

                $rowTotal += (int)$countModel->count;
                $rowInvalid += (int)$countInvalidModel->count;
                $row['epcs'][] = (int)$countModel->count;
            }
            $row['countofCartons'] = (int)$countofCartons->count;
            $row['rowTotal'] = $rowTotal + $rowInvalid;
            $row['inValidTotal'] = $rowInvalid;

            $arrayTable[] = $row;
            $this->PackageReportYLenght = count($arrayTable);

        }
//        echo "<pre>";
//        print_r($arrayTable);
//        echo "</pre>";
//        exit();


        $data = [
            'title' => '-' . trans('portal.package_list') . '-' . date('YmdHis'),
            'consignment' => null,
            'consignmentExtra' => null,
            'tableData' => $arrayTable,
            'fixVal' => $fixVal
        ];
        $rowCnt = 0;
        $tableCnt = count($arrayTable) - 1;
        //$tableCnt2 = 20 / 4;
        if ($tableCnt % 4 == 0){

            $this->tablesize = floor ($tableCnt / 4);

        }else{

            $this->tablesize = floor ($tableCnt / 4) + 1;

        }
//        echo '<pre>';
//        print_r($data['tableData']);
//        exit();

        return view('reports.excel.packageMs', $data);

    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getColumnDimension('I')->setAutoSize(true);
                $event->sheet->getColumnDimension('A')->setWidth(13.59);
                $event->sheet->getStyle('A11')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('A9')->getAlignment()->setWrapText(true);

                $event->sheet->getStyle('B1')->getFont()->setSize(18)->setBold(true);
                $event->sheet->mergeCells('B1:I1');
                $event->sheet->mergeCells('H2:I2');
                $event->sheet->mergeCells('H3:I3');
                $event->sheet->mergeCells('H10:I10');
                $event->sheet->mergeCells('H11:I11');
                $event->sheet->mergeCells('H12:I12');
                $event->sheet->mergeCells('H13:I13');
                $event->sheet->mergeCells('A3:A4');
                $event->sheet->mergeCells('B2:D2');
                $event->sheet->mergeCells('B3:D4');
                $event->sheet->mergeCells('B5:D5');
                $event->sheet->mergeCells('B6:D7');
                $event->sheet->mergeCells('F4:I4');
                $event->sheet->mergeCells('F5:I5');
                $event->sheet->mergeCells('A6:A7');
                $event->sheet->mergeCells('B6:D7');
                $event->sheet->mergeCells('E6:I7');
                $event->sheet->mergeCells('B8:I8');
                $event->sheet->mergeCells('B9:C9');
                $event->sheet->mergeCells('E9:I9');
                $event->sheet->mergeCells('B10:C10');
                $event->sheet->mergeCells('E10:F10');
                $event->sheet->mergeCells('A11:A12');
                $event->sheet->mergeCells('B11:C12');
                $event->sheet->mergeCells('E11:F11');
                $event->sheet->mergeCells('E12:F12');
                $event->sheet->mergeCells('B13:C13');
                $event->sheet->mergeCells('E13:F13');
                $event->sheet->getStyle('B1')->getAlignment()->setVertical('center');
                $event->sheet->getStyle('B1')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B6')->getAlignment()->setVertical('center');
                $event->sheet->getStyle('F5')->getAlignment()->setVertical('center');
                $event->sheet->getStyle('B11')->getAlignment()->setVertical('center');
                $event->sheet->getStyle('F4')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('F5')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('C1')->getAlignment()->setHorizontal('center');

                // image eklenmek istediğinde örnek kod
                /*
                $event->sheet->getRowDimension('1')->setRowHeight(90.0,null);
                $drawing = new Drawing();
                 $drawing->setName('logo');
                 $drawing->setDescription('logo');
                 $drawing->setPath(public_path('upload/images/mslogo.png'));
                 $drawing->setCoordinates('A1');
                 $drawing->setHeight(90);
                 $drawing->setOffsetX(80);    // this is how
                 $drawing->setOffsetY(3);    // this is how
                 $event->sheet->addDrawings($drawing);*/

                $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                for ($i = 1; $i < 14; $i++) {
                    for ($it = 'A'; $it < 'J'; $it++) {
                        $cell = $it . (string)$i;
                        $event->sheet->getStyle($cell)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                        if ($i == '1') {
                            $event->sheet->getStyle($cell)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
                        }
                        if ($it == 'A'){
                            $event->sheet->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THICK);
                        }
                        if ($it == 'I'){
                            $event->sheet->getStyle($cell)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THICK);
                        }


                    }
                }

                $event->sheet->removeRow(14);

                $excelPacketInfoStartRowNumber = 14;
                $excelPacketInfoStartRowChar = 'A';
                $excelPacketInfoEndRowChar = 'A';
                for ($i = 1; $i < $this->PackageReportLenght; $i++) {
                    $excelPacketInfoEndRowChar++;
                }

                for ($i = $excelPacketInfoStartRowNumber; $i < ($excelPacketInfoStartRowNumber + (5*$this->tablesize)+$this->tablesize) ; $i++) {
                    for ($it = 'A'; $it<'J'; $it++){
                        $event->sheet->getStyle($it.(string)$i)
                            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                        if ($it == 'A'){
                            $event->sheet->getStyle($it.(string)$i)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THICK);
                        }
                        if ($it == 'I'){
                            $event->sheet->getStyle($it.(string)$i)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THICK);
                        }
                        if ($i == ($excelPacketInfoStartRowNumber + (5*$this->tablesize)+$this->tablesize-1)){
                            $event->sheet->getStyle($it.(string)$i)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

                        }
                    }
                }


            },
        ];
    }

}
