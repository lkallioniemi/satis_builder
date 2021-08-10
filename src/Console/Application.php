<?php

namespace Frc\Satis\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    protected function getDefaultCommands(): array
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\BuildCommand,
        ]);
    }
}
