<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) elements.at New Media Solutions GmbH (http://www.elements.at)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace ExportToolkit\Console\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends AbstractCommand
{
    use \Processmanager\ExecutionTrait;

    protected function configure()
    {
        $this
            ->setName('export-toolkit:export')
            ->setDescription('Executes a specific export toolkit configuration')
            ->addOption(
                'config-name', null,
                InputOption::VALUE_REQUIRED,
                'the name of the configuration which should be used'
            )
            ->addOption(
                'monitoring-item-id', null,
                InputOption::VALUE_REQUIRED,
                'Contains the monitoring item if executed via the Pimcore backend'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $monitoringItemId = $input->getOption('monitoring-item-id');
        if (!$monitoringItemId) { //executed directly from export toolkit
            $lockKey = 'exporttoolkit_'.$input->getOption('config-name');
            \Pimcore\Model\Tool\Lock::acquire($lockKey);
        }

        $this->initProcessManager($input->getOption('monitoring-item-id'), ['autoCreate' => true, 'name' => $input->getOption('config-name')]);
        $service = new \ExportToolkit\ExportService();
        $service->executeExport($input->getOption('config-name'));

        if (!$monitoringItemId) {
            \Pimcore\Model\Tool\Lock::release($lockKey);
        }
    }
}