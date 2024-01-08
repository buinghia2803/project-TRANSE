<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteFolderTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delete-folder-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command delete folder temp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Storage::delete(Storage::allFiles(FOLDER_TEMP));
    }
}
