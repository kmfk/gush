<?php

/**
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Gush\Feature\GitHubFeature;

/**
 * Creates an issue
 *
 * @author Luis Cordova <cordoval@gmail.com>
 */
class IssueCreateCommand extends BaseCommand implements GitHubFeature
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:create')
            ->setDescription('Creates an issue')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command creates a new issue for either the current or the given organization
and repository:

    <info>$ gush %command.full_name%</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapter = $this->getAdapter();
        $emptyValidator = function ($string) {
            if (trim($string) == '') {
                throw new \Exception('This value can not be empty');
            }

            return $string;
        };

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        $title = $dialog->askAndValidate(
            $output,
            'Issue title: ',
            $emptyValidator
        );

        $body = $dialog->askAndValidate(
            $output,
            'Enter description: ',
            $emptyValidator
        );

        $issue = $adapter->openIssue($title, $body);

        $url = $adapter->getIssueUrl($issue['number']);
        $output->writeln("Created issue {$url}");

        return self::COMMAND_SUCCESS;
    }
}
