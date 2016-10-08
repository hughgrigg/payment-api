<?php

namespace PaymentApi\Command;

use PaymentApi\Structure\Config;

class Seed extends Command
{
    const NAME = 'db:seed';
    const DESCRIPTION = 'Seed the database with test data';

    /**
     * Run the migrations.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     */
    public function run()
    {
        $this->output("Seeding database...\n");
        shell_exec(
            sprintf(
                'mysql -u%s -p%s -h%s -D %s < %s 2>/dev/null',
                $this->app->config()->dbUsername(),
                $this->app->config()->dbPassword(),
                $this->app->config()->dbHost(),
                $this->app->config()->dbDatabase(),
                Config::pathTo('database/seed.sql')
            )
        );
        $this->output("Seeded.\n");
    }
}
