<?php

namespace PaymentApi\Command;

use PaymentApi\Structure\Config;

class Migrate extends Command
{
    const NAME = 'db:migrate';
    const DESCRIPTION = 'Run database migrations';

    /**
     * Run the migrations.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     */
    public function run()
    {
        $this->output("Running migrations...\n");
        shell_exec(
            sprintf(
                'mysql -u%s -p%s -h%s -D %s < %s 2>/dev/null',
                $this->app->config()->dbUsername(),
                $this->app->config()->dbPassword(),
                $this->app->config()->dbHost(),
                $this->app->config()->dbDatabase(),
                Config::pathTo('database/migrate.sql')
            )
        );
        $this->output("Migrated.\n");
    }
}
