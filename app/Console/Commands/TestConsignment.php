<?php

namespace App\Console\Commands;

use App\Consignment;
use App\Device;
use App\Helpers\LogActivity;
use App\Helpers\OptionTrait;
use App\Item;
use App\Order;
use App\Package;
use App\User;
use Illuminate\Console\Command;

class TestConsignment extends Command
{

    use OptionTrait;
    use LogActivity;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:consignment  {company_id}  {consignment_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

            $company_id         = $this->argument('company_id');
            $consignment_number = $this->argument('consignment_number');
            $consignee_id       = 1;

            if($consignment_number && $consignment_number > 0){

                $con_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') .' -6 month'));

                for ($i=1; $i<=$consignment_number; $i++){

                    $device     = Device::where('company_id', $company_id)->get()->random(1);
                    $user       = User::where('company_id', $company_id)->get()->random(1);

                    $item_count = rand(1000, 100000);

                    $con_date = date('Y-m-d H:i:s', strtotime("$con_date +4 day"));

                    $faker = \Faker\Factory::create('tr_TR');

                    // Her sevkiyata 1 sipariş ekle
                    $order = new Order;
                    $order->consignee_id    = $consignee_id;
                    $order->order_code      = $this->autoGenerateOrderCode();
                    $order->po_no           = $this->autoGeneratePoCode($faker->countryCode. ' - '.$faker->unique()->randomNumber(5));
                    $order->name            = $faker->countryCode;
                    $order->item_count      = $item_count;
                    $order->created_at      = $con_date;
                    $order->created_user_id = $user[0]->id;
                    $order->status          = 1;
                    if ($order->save()){

                        $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                        // Firma Kaydet
                        $consignment                        = new Consignment;
                        $consignment->order_id              = $order->id;
                        $consignment->company_id            = $company_id;
                        $consignment->consignee_id          = $consignee_id;
                        $consignment->name                  = $order->po_no;
                        $consignment->plate_no              = "35 TKPSN 42";
                        $consignment->item_count            = $item_count;
                        $consignment->delivery_date         = $con_date;
                        $consignment->old_id                = 0;
                        $consignment->created_user_id       = $user[0]->id;
                        $consignment->created_at            = $con_date;
                        $consignment->status                = 1;
                        $consignment->save();

                        $this->createLog('Consignment','portal.log_create_consignment', ['name' => $consignment->name, 'date' => $consignment->delivery_date], $consignment->id);
                    }

                    $consignmentDetail = rand(100, 800);

                    if($consignmentDetail && $consignmentDetail > 0){

                        $bar = $this->output->createProgressBar($consignmentDetail);
                        $bar->setFormat(" \n $consignment->name sevkiyatı verileri oluşturuluyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                        $bar->start();

                        for ($j=1; $j<=$consignmentDetail; $j++){

                            $size = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
                            $modelno = rand(1, 20);


                            // Package Save
                            $package                        = new Package;
                            $package->company_id            = $company_id;
                            $package->order_id              = $order->id;
                            $package->consignment_id        = $consignment->id;
                            $package->package_no            = $j;
                            $package->size                  = $size[rand(1, 7)];
                            $package->model                 = 'MODEL '. $modelno;
                            $package->created_at            = $con_date;
                            $package->device_id             = $device[0]->id;
                            $package->created_user_id       = $user[0]->id;
                            $package->status                = 0;
                            $package->save();

                            $products = ceil($item_count / $consignmentDetail);

                            for ($k=1; $k<=$products; $k++){
                                // Item Save
                                $item                        = new Item;
                                $item->company_id            = $company_id;
                                $item->order_id              = $order->id;
                                $item->consignment_id        = $consignment->id;
                                $item->package_id            = $package->id;
                                $item->epc                   = str_random(24);
                                $item->size                  = '-';
                                $item->device_id             = $device[0]->id;
                                $item->created_at            = $con_date;
                                $item->created_user_id       = $user[0]->id;
                                $item->save();

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

            dd([$exception]);

        }

    }

}
