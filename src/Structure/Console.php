<?php

namespace PaymentApi\Structure;

use OutOfBoundsException;
use PaymentApi\Command\Command;
use PaymentApi\Command\Migrate;
use PaymentApi\Command\Reset;
use PaymentApi\Command\Seed;

class Console
{
    /** @var array */
    private $argv = [];

    /** @var App */
    private $app;

    /** @var string[] */
    private $commands = [
        Migrate::class,
        Seed::class,
        Reset::class,
    ];

    /**
     * @param array $argv
     * @param App   $app
     */
    public function __construct(array $argv, App $app)
    {
        $this->argv = $argv;
        $this->app = $app;
    }

    /**
     * @throws \OutOfBoundsException
     */
    public function execute()
    {
        if (!$this->commandName() || $this->commandName() === 'help') {
            $this->help();

            return;
        }

        foreach ($this->commands as $commandClass) {
            if (constant("{$commandClass}::NAME") === $this->commandName()) {
                $this->makeCommand($commandClass)->run();

                return;
            }
        }

        throw new OutOfBoundsException(
            "There is no command called `{$this->commandName()}`."
        );
    }

    /**
     * @return string
     */
    private function commandName(): string
    {
        if (isset($this->argv[1])) {
            return (string) $this->argv[1];
        }

        return '';
    }

    /**
     * Print a help message.
     */
    private function help()
    {
        print "Available commands:\n";
        foreach ($this->commands as $commandClass) {
            printf(
                "\t%s\t\t%s\n",
                constant("{$commandClass}::NAME"),
                constant("{$commandClass}::DESCRIPTION")
            );
        }
    }

    /**
     * @param string $commandClass
     *
     * @return Command
     */
    private function makeCommand(string $commandClass): Command
    {
        return new $commandClass($this->app);
    }
}
