<?php

namespace App\Console\Commands;

use App\Http\Controllers\DarazController;
use App\Models\Category;
use Illuminate\Console\Command;

class InsertCategoryFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'insert-daraz:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert all daraz Category to database';


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
    public function handle(DarazController $darazController, Category $category)
    {
        $darazController->insertCategory($category);
        $this->info('All categories have been inserted into database .');
    }
}
