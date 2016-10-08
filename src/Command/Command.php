<?php

namespace PaymentApi\Command;

use PaymentApi\Structure\App;

abstract class Command
{
    /** @var App */
    protected $app;

    /** @var resource */
    protected $output;

    /**
     * @param App    $app
     * @param string $output
     */
    public function __construct(App $app, string $output = 'php://output')
    {
        $this->app = $app;
        $this->output = fopen($output, 'w+');
    }

    /**
     * Close the output resource.
     */
    public function __destruct()
    {
        fclose($this->output);
    }

    abstract public function run();

    /**
     * @param string $content
     */
    protected function output(string $content)
    {
        fwrite($this->output, $content);
    }
}
