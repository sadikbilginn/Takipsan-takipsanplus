<?php

namespace App\Console\Commands;

use App\Company;
use App\Device;
use App\Helpers\LogActivity;
use App\Helpers\OptionTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CompanyTransfer extends Command
{
    use OptionTrait;
    use LogActivity;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:sync {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eski veritabanından firma bilgileri yeni veritabanına aktarır.';

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


            $companies = $this->get_service('post', "http://fason.takipsanrfid.com/Services/Web_Services/URL/productionList.php", ['firmId' => $this->argument('company_id')]);
            if($companies && count($companies->productionList) > 0){

                $bar = $this->output->createProgressBar(count($companies->productionList));
                $bar->setFormat(" \n Firma verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                $bar->start();

                foreach ($companies->productionList as $key => $value){

                    $url        = $value->productionLogo;
                    $urlExp     = explode('/', $url);
                    $mediaExt   = explode('.', end($urlExp));
                    $file_name  = md5(date('d-m-Y H:i:s') . end($urlExp)) . "." . end($mediaExt);
                    $company_img            = public_path(config('settings.media.companies.full_path') . $file_name);

                    // Firma Kaydet
                    $company                    = new Company;
                    $company->name              = $value->productionName;
                    $company->title             = $value->productionName;
                    $company->phone             = '+90 850 441 6789';
                    $company->email             = 'info@takipsan.com';
                    $company->address           = 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE';
                    $company->latitude          = $value->latitude;
                    $company->longitude         = $value->longitude;
                    if($file_name != ''){
                        $company->logo          = $file_name;
                    }
                    $company->status            = 1;
                    $company->created_user_id   = 1;
                    $company->old_id             = $value->productionId;
                    $company->save();

                    // Logo Kaydet
                    file_put_contents($company_img, file_get_contents($url));

                    //Cihazları Kaydet
                    $devices = explode('#', $value->deviceList);

                    foreach ($devices as $key2 => $value2){

                        $device_type = '';

                        if($value2 == 1){
                            $device_type = 'box_station';
                        }elseif($value2 == 2){
                            $device_type = 'donkey_station';
                        }elseif($value2 == 3){
                            $device_type = 'tunnel_station';
                        }

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
                        $device->package_timeout        = '6';
                        $device->device_type            = $device_type;
                        $device->name                   = config('settings.devices.'.$device_type.'.name');
                        $device->status                 = 1;
                        $device->created_user_id        = 1;
                        $device->save();
                    }

                    $this->createLog('Company','portal.log_create_company', ['name' => $company->name], $company->id);

                    $bar->advance();
                }

                $bar->setFormat("veriler yüklendi.");
                $bar->finish();
            }

            //Artisan::call('consigneeTransfer:sync', ['company_id' => $this->argument('company_id')]);
        }

        catch (\Exception $exception){

            dd([$exception]);

        }

    }

}
