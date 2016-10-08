<?php

namespace PaymentApi\Command;

class Reset extends Command
{
    const NAME = 'db:reset';
    const DESCRIPTION = 'Reset the database';

    public function run()
    {
        $this->output("Resetting database...\n");
        $mysql = sprintf(
            'mysql -u%s -p%s -h%s',
            $this->app->config()->dbUsername(),
            $this->app->config()->dbPassword(),
            $this->app->config()->dbHost()
        );
        shell_exec(
            "echo 'DROP DATABASE IF EXISTS `payment_api`' | {$mysql} 2>/dev/null"
        );
        shell_exec(
            "echo 'CREATE DATABASE IF NOT EXISTS `payment_api`' | {$mysql} 2>/dev/null"
        );
        $this->output("Done.\n");
    }
}
