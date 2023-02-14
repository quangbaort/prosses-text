<?php

namespace App\Console\Commands;

use App\Models\Link;
use App\Models\LinkList;
use App\Models\Text;
use Illuminate\Console\Command;

class ListDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:delete {list_slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Lists';

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
    public function handle()
    {
        $listFolder = $this->argument('list_slug');
        $list = Text::query()->where('folder_id', $listFolder);

        if ($list === null) {
            $this->error('Invalid or non-existent List.');
            return 1;
        }
        $list->delete();
        $this->info('List Deleted.');
        return 0;
    }
}
