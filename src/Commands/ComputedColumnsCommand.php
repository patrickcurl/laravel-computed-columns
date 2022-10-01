<?php

namespace ComputedColumns\ComputedColumns\Commands;

use Illuminate\Console\Command;

class ComputedColumnsCommand extends Command
{
    public $signature = 'laravel-computed-columns';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
