<?php

namespace App\Console\Commands;

use App\Company;
use App\Consignee;
use App\Consignment;
use App\Device;
use App\Helpers\OptionTrait;
use App\Package;
use App\Item;
use App\Order;
use Illuminate\Console\Command;
use App\Helpers\LogActivity;


class ConsignmentTransfer extends Command
{
    use OptionTrait;
    use LogActivity;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consignment:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eski veritabanından sevkiyat bilgileri yeni veritabanına aktarır.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{

            header('Content-Type: text/html; charset=utf-8');
            ini_set('display_errors',1);
            error_reporting(E_ALL);
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', '-1');


            $companies = Company::where('status', 1)->get();
            if($companies){
                foreach ($companies as $key => $value){

                    $device = Device::where('company_id', $value->id)->first();

                    $consignments = $this->get_service('post', "http://fason.takipsanrfid.com/Services/Web_Services/URL/consignmentList.php", ['productionId' => $value->old_id]);
                    if($consignments && count($consignments->consignmentList) > 0){

                        $bar = $this->output->createProgressBar(count($consignments->consignmentList));
                        $bar->setFormat(" \n $value->name verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                        $bar->start();

                        foreach ($consignments->consignmentList as $key2 => $value2){

                            $consignee = Consignee::where('old_id', $value2->targetFirmId)->first();

                            // Her sevkiyata 1 sipariş ekle
                            $order = new Order;
                            $order->consignee_id    = $consignee ? $consignee->id : 0;
                            $order->order_code      = $this->autoGenerateOrderCode();
                            $order->po_no           = $this->autoGeneratePoCode($value2->consignmentName);
                            $order->name            = $value2->consignmentName;
                            $order->item_count      = $value2->expectedProductCount;
                            $order->created_at      = $value2->upsertedDate;
                            $order->created_user_id = 1;
                            $order->status          = 0;
                            if ($order->save()){

                                $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                                // Firma Kaydet
                                $consignment                        = new Consignment;
                                $consignment->order_id              = $order->id;
                                $consignment->company_id            = $value->id;
                                $consignment->consignee_id          = $consignee ? $consignee->id : 0;
                                $consignment->name                  = $order->po_no;
                                $consignment->plate_no              = "35 TKPSN 42";
                                $consignment->item_count            = $value2->expectedProductCount;
                                $consignment->delivery_date         = $value2->deadline;
                                $consignment->old_id                = $value2->consignmentId;
                                $consignment->created_user_id       = 1;
                                $consignment->created_at            = $value2->upsertedDate;
                                $consignment->status                = 0;
                                $consignment->save();

                                $this->createLog('Consignment','portal.log_create_consignment', ['name' => $consignment->name, 'date' => $consignment->delivery_date], $consignment->id);
                            }

                            $consignmentDetail = $this->get_service('post', "http://fason.takipsanrfid.com/Services/Web_Services/URL/consignmentDetail.php", ['consignmentId' => $value2->consignmentId]);
                            if($consignmentDetail && isset($consignmentDetail->productList) && count($consignmentDetail->productList) > 0){

                                $bar2 = $this->output->createProgressBar(count($consignmentDetail->productList));
                                $bar2->setFormat(" \n $value->name - $consignment->name sevkiyatı verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                                $bar2->start();

                                foreach ($consignmentDetail->productList as $key3 => $value3){

                                    // Package Save
                                    $package                        = new Package;
                                    $package->company_id            = $value->id;
                                    $package->order_id              = $order->id;
                                    $package->consignment_id        = $consignment->id;
                                    $package->package_no            = $value3->groupIndex;
                                    $package->size                  = $value3->groupSize;
                                    $package->model                 = $value3->groupModel;
                                    $package->created_at            = $value3->upsertedDate;
                                    $package->device_id             = $device ? $device->id : 0;
                                    $package->created_user_id       = 1;
                                    $package->status                = 0;
                                    $package->save();

                                    if($value3->products && count($value3->products) > 0){
                                        foreach ($value3->products as $key4 => $value4){
                                            // Item Save
                                            $item                        = new Item;
                                            $item->company_id            = $value->id;
                                            $item->order_id              = $order->id;
                                            $item->consignment_id        = $consignment->id;
                                            $item->package_id            = $package->id;
                                            $item->epc                   = $value4->epc;
                                            $item->size                  = $value4->size;
                                            $item->device_id             = $device ? $device->id : 0;
                                            $item->created_at            = $value4->upsertedDate;
                                            $item->created_user_id       = 1;
                                            $item->save();

                                        }
                                    }
                                    $bar2->advance();
                                }

                                $bar2->setFormat("veriler yüklendi.");
                                $bar2->finish();
                            }

                            $bar->advance();
                        }

                        $bar->setFormat("veriler yüklendi.");
                        $bar->finish();
                    }
                }
            }
        }

        catch (\Exception $exception){

            dd([$value2, $exception]);

        }

    }
}
