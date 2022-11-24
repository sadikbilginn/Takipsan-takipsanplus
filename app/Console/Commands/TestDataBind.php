<?php

namespace App\Console\Commands;

use App\Company;
use App\Consignee;
use App\Consignment;
use App\Device;
use App\Helpers\LogActivity;
use App\Helpers\OptionTrait;
use App\Item;
use App\Order;
use App\Package;
use App\User;
use Illuminate\Console\Command;
use Faker\Generator as Faker;

class TestDataBind extends Command
{
    use OptionTrait;
    use LogActivity;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testdata:get';

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


            $companies = $this->companyData();
            if($companies && count($companies) > 0){

                $bar = $this->output->createProgressBar(count($companies));
                $bar->setFormat(" \n Firma verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                $bar->start();

                foreach ($companies as $key => $value){

                    // Firma Kaydet
                    $company                    = new Company;
                    $company->name              = $value['name'];
                    $company->title             = $value['title'];
                    $company->phone             = $value['phone'];
                    $company->email             = $value['email'];
                    $company->address           = $value['address'];
                    $company->latitude          = $value['latitude'];
                    $company->longitude         = $value['longitude'];
                    $company->logo              = $value['logo'];
                    $company->consignment_close = $value['consignment_close'];
                    $company->status            = $value['status'];
                    $company->created_user_id   = $value['created_user_id'];
                    $company->old_id            = 0;
                    $company->save();

                    foreach ($this->devices() as $key2 => $value2){

                        $device                         = new Device;
                        $device->company_id             = $company->id;
                        $device->reader                 = 'impinj';
                        $device->reader_mode            = 'DenseReaderM8';
                        $device->estimated_population   = 150;
                        $device->search_mode            = 'SingleTarget';
                        $device->session                = 1;
                        $device->string_set             = '';
                        $device->common_power           = 1;
                        $device->antennas               = '{"read":"22","write":"22"}';
                        $device->package_timeout        = $value2['package_timeout'];
                        $device->device_type            = $value2['device_type'];
                        $device->name                   = $value2['name'];
                        $device->status                 = $value2['status'];
                        $device->created_user_id        = $value2['created_user_id'];
                        $device->save();
                    }

                    $this->createLog('Company','portal.log_create_company', ['name' => $company->name], $company->id);

                    $bar->advance();
                }

                $bar->setFormat("veriler yüklendi.");
                $bar->finish();
            }

            $companies = Company::where('status', 1)->get();
            if($companies){
                foreach ($companies as $key => $value){

                    $faker = \Faker\Factory::create('tr_TR');
                    $cuser = \App\User::create([
                        'company_id'        => $value->id,
                        'username'          => str_slug($value->name, '.'),
                        'name'              => $value->name,
                        'email'             => "mehmet.karabulut@takipsan.com",
                        'email_verified_at' => now(),
                        'password'          => bcrypt('12121212'),
                        'remember_token'    => str_random(10),
                    ]);
                    $cuser->roles()->attach([1]);

                    $cuser = \App\User::create([
                        'company_id'        => $value->id,
                        'username'          => $key > 0 ? "test". $key : "test",
                        'name'              => "Mehmet Karabulut",
                        'email'             => "mehmet.karabulut@takipsan.com",
                        'email_verified_at' => now(),
                        'password'          => bcrypt('12121212'),
                        'remember_token'    => str_random(10),
                    ]);
                    $cuser->roles()->attach([2]);


                    $consignmentFirm = $this->consignee();
                    if($consignmentFirm && count($consignmentFirm) > 0){

                        $bar = $this->output->createProgressBar(count($consignmentFirm));
                        $bar->setFormat(" \n $value->name verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                        $bar->start();

                        foreach ($consignmentFirm as $key2 => $value2){

                            $con = Consignee::where('name', $value2['name'])->first();
                            if(!$con){

                                // Firma Kaydet
                                $consingnee                   = new Consignee;
                                $consingnee->name             = $value2['name'];
                                $consingnee->logo             = $value2['logo'];
                                $consingnee->phone            = $value2['phone'];
                                $consingnee->address          = $value2['address'];
                                $consingnee->address          = $value2['address'];
                                $consingnee->auth_name        = $value2['auth_name'];
                                $consingnee->auth_phone       = $value2['auth_phone'];
                                $consingnee->status           = $value2['status'];
                                $consingnee->created_user_id  = $value2['created_user_id'];
                                $consingnee->old_id           = 0;
                                if($consingnee->save()){
                                    $consingnee->companies()->attach([$value->id]);
                                }
                            }else{
                                $con->companies()->attach([$value->id]);
                            }

                            $bar->advance();
                        }

                        $bar->setFormat("veriler yüklendi.");
                        $bar->finish();
                    }

                    $consignment_number = rand(5, 10);

                    if($consignment_number && $consignment_number > 0){

                        $bar = $this->output->createProgressBar($consignment_number);
                        $bar->setFormat(" \n $value->name verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                        $bar->start();

                        $con_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') .' -6 month'));

                        for ($i=1; $i<=$consignment_number; $i++){

                            $device     = Device::where('company_id', $value->id)->get()->random(1);
                            $user       = User::where('company_id', $value->id)->get()->random(1);
                            $consignee  = $value->consignees()->inRandomOrder()->first();
                            $item_count = rand(1000, 100000);

                            $con_date = date('Y-m-d H:i:s', strtotime("$con_date +4 day"));

                            $faker = \Faker\Factory::create('tr_TR');

                            // Her sevkiyata 1 sipariş ekle
                            $order = new Order;
                            $order->consignee_id    = $consignee->id;
                            $order->order_code      = $this->autoGenerateOrderCode();
                            $order->po_no           = $this->autoGeneratePoCode($faker->countryCode. ' - '.$faker->unique()->randomNumber(5));
                            $order->name            = $faker->countryCode;
                            $order->item_count      = $item_count;
                            $order->created_at      = $con_date;
                            $order->created_user_id = $user[0]->id;
                            $order->status          = 0;
                            if ($order->save()){

                                $this->createLog('Order','portal.log_create_order', ['name' => $order->po_no], $order->id);

                                // Firma Kaydet
                                $consignment                        = new Consignment;
                                $consignment->order_id              = $order->id;
                                $consignment->company_id            = $value->id;
                                $consignment->consignee_id          = $consignee->id;
                                $consignment->name                  = $order->po_no;
                                $consignment->plate_no              = "35 TKPSN 42";
                                $consignment->item_count            = $item_count;
                                $consignment->delivery_date         = $con_date;
                                $consignment->old_id                = 0;
                                $consignment->created_user_id       = $user[0]->id;
                                $consignment->created_at            = $con_date;
                                $consignment->status                = 0;
                                $consignment->save();

                                $this->createLog('Consignment','portal.log_create_consignment', ['name' => $consignment->name, 'date' => $consignment->delivery_date], $consignment->id);
                            }

                            $consignmentDetail = rand(100, 800);

                            if($consignmentDetail && $consignmentDetail > 0){

                                $bar2 = $this->output->createProgressBar($consignmentDetail);
                                $bar2->setFormat(" \n $value->name - $consignment->name sevkiyatı verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                                $bar2->start();

                                for ($j=1; $j<=$consignmentDetail; $j++){

                                    $size = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
                                    $modelno = rand(1, 20);


                                    // Package Save
                                    $package                        = new Package;
                                    $package->company_id            = $value->id;
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
                                        $item->company_id            = $value->id;
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

            dd([$exception]);

        }

    }

    public function companyData()
    {
        return [
            [
                'name'              => 'Yılmaz Tekstil',
                'title'             => 'Yılmaz Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'info@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.394517',
                'longitude'         => '27.035748',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1
            ],
            [
                'name'              => 'Can Tekstil',
                'title'             => 'Can Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'info@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1
            ],
            [
                'name'              => 'Özgür Tekstil',
                'title'             => 'Özgür Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'info@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'name'              => 'Svs Tekstil',
                'title'             => 'Svs Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'info@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1
            ]
        ];
    }

    public function devices()
    {
        return [
            [
                'device_type'           => 'box_station',
                'name'                  => config('settings.devices.box_station.name'),
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1
            ],
            [
                'device_type'           => 'donkey_station',
                'name'                  => config('settings.devices.donkey_station.name'),
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1
            ],
            [
                'device_type'           => 'tunnel_station',
                'name'                  => config('settings.devices.tunnel_station.name'),
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1
            ],
            [
                'device_type'           => 'box_station2',
                'name'                  => config('settings.devices.box_station2.name'),
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1
            ]
        ];
    }

    public function consignee()
    {
        return [
            [
                'name'                  => 'Zara',
                'logo'                  => 'zara.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Bershka',
                'logo'                  => 'bershka.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'H&M',
                'logo'                  => 'hm.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'M&S',
                'logo'                  => 'ms.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Pull & Bear',
                'logo'                  => 'pb.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Stradivarius',
                'logo'                  => 'stradivarius.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'F&F',
                'logo'                  => 'ff.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'LPP',
                'logo'                  => 'lpp.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'OYSHO',
                'logo'                  => 'oysho.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Mossimo Dutti',
                'logo'                  => 'mossimodutti.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Decathlon',
                'logo'                  => 'decathlon.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'UNIQLO',
                'logo'                  => 'uniqlo.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Levis',
                'logo'                  => 'levis.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'C&A',
                'logo'                  => 'ca.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Lefties',
                'logo'                  => 'lefties.png',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Target',
                'logo'                  => 'target.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Tesco',
                'logo'                  => 'tesco.jpg',
                'phone'                 => '+90 850 441 6789',
                'address'               => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'auth_name'             => 'Mehmet Karabulut',
                'auth_phone'            => '+90 850 441 6789',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]
        ];
    }

}
