<?php
namespace App\Console\Commands;

use App\Helpers\AuthApiHelper;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;


class SyncProductCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sync_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sincroniza os produtos da API para o db da APP";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $auth = new AuthApiHelper();
        $token = $auth->getToken();

        // $this->info("Okay");
    }
}
