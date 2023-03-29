<?php

namespace App\Console\Commands;

use App\Http\Controllers\DarazController;
use Illuminate\Console\Command;

class FetchAllDarazBrandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'fetch-daraz:brands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all brands from Daraz API and store them in a file';


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
     * @return int
     */
    public function handle(DarazController $darazController)
    {
        $darazController->fetchAllBrandsAndStoreInFile();

        $this->info('All brands have been fetched and stored in a file.');
    }
}
