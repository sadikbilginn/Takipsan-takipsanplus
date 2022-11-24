<?php

namespace App\Console\Commands;
use App\Helpers\OptionTrait;
use Illuminate\Console\Command;

class XmlFileRepo extends Command{

    use OptionTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:fetch';

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
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $xmlFetch = xmlFetch();
        if ($xmlFetch == 'ok'){

            echo "ok <br>" ;

        }
        echo "end";
        //return redirect()->route('xml_file_repo.index');

    }

}
