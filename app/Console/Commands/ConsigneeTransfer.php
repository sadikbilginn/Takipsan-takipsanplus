<?php

namespace App\Console\Commands;

use App\Company;
use App\Consignee;
use App\Helpers\OptionTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ConsigneeTransfer extends Command
{
    use OptionTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consignee:sync {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eski veritabanından sevk firma bilgileri yeni veritabanına aktarır.';

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
     *
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

                    $consignmentFirm = $this->get_service('post', "http://fason.takipsanrfid.com/Services/Web_Services/URL/consignmentFirmList.php", ['firmId' => $this->argument('company_id')]);
                    if($consignmentFirm && count($consignmentFirm->firmList) > 0){

                        $bar = $this->output->createProgressBar(count($consignmentFirm->firmList));
                        $bar->setFormat(" \n $value->name verileri çekiliyor. \n %current%/%max% [%bar%] %percent%% \n veriler yükleniyor..");
                        $bar->start();

                        foreach ($consignmentFirm->firmList as $key2 => $value2){

                            $con = Consignee::where('old_id', $value2->id)->first();
                            if(!$con){

                                $file_name = 'takipsan.jpg';

                                if($value2->ConsignmentFirmIconPath != ''){
                                    $url            = $value2->ConsignmentFirmIconPath;
                                    $urlExp         = explode('/', $url);
                                    $mediaExt       = explode('.', end($urlExp));
                                    $file_name      = md5(date('d-m-Y H:i:s') . end($urlExp)) . "." . end($mediaExt);
                                    $consingnee_img = public_path(config('settings.media.consignees.full_path') . $file_name);

                                    // Logo Kaydet
                                    file_put_contents($consingnee_img, file_get_contents($url));
                                }

                                // Firma Kaydet
                                $consingnee                   = new Consignee;
                                $consingnee->name             = $value2->consignmentFirmName;
                                if($file_name != ''){
                                    $consingnee->logo         = $file_name;
                                }
                                $consingnee->status           = 1;
                                $consingnee->created_user_id  = 1;
                                $consingnee->old_id           = $value2->id;
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

                }
            }

            //Artisan::call('consignmentTransfer:sync');
        }

        catch (\Exception $exception){

            dd([$exception, $consignmentFirm]);

        }

    }
}
