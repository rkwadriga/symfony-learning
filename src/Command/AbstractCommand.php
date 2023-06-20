<?php

declare(strict_types=1);
/**
 * Created 2023-06-20 15:23:47
 * Author rkwadriga
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->validate($input, $io)) {
            return Command::FAILURE;
        }

        return $this->exec($input, $io);
    }

    abstract protected function exec(InputInterface $input, SymfonyStyle $io): int;

    protected function validate(InputInterface $input, SymfonyStyle $io): bool
    {
        foreach ($this->getDefinition()->getOptions() as $option) {
            if ($option->isValueRequired() && $input->getOption($option->getName()) === null) {
                $value = $io->ask(sprintf('Please, past the %s', $option->getDescription()));
                if ($value === null) {
                    $io->error(sprintf('Option "%s" is required', $option->getName()));

                    return false;
                }

                $input->setOption($option->getName(), $value);
            }
        }

        return true;
    }
}