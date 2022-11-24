<?php

namespace App\Console\Commands;

use App\User;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class UserTransElasticsearch extends Command
{
    public $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users-elastic:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Veri tabanı üzerindeki kullanıcıları elastic search ile senkronize eder.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = ClientBuilder::create()->build();
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

            $start = 1;
            $count = 1000;

            $c_count = User::where('id', '>',  5460992)->count();
            $users_count = ceil($c_count/$count);

            $bar = $this->output->createProgressBar($c_count);
            $bar->start();

            for ($i=0; $i<$users_count; $i++)
            {
                $users = User::where('id', '>',  5460992)->offset($start)->limit($count)->get();

                foreach ($users as $user){

                    $data = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ];

                    $params = [
                        'index' => 'takipsan',
                        'type' => 'users',
                        'id' => $user->id,
                        'body' => $data
                    ];

                     $this->client->index($params);

                     $bar->advance();
                }

                $start+=$count;
            }

            $bar->finish();

        }

        catch (\Exception $exception){

            dd([$exception]);

        }
    }
}
