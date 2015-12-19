<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;


class RepoFixtureCommand extends Command {

    const DESCRIPTION = 'Set-up or tear down a Subversion repository for use as a fixture to test the svn-ancestry application.';
    const ARG_DESCRIPTION = '[<setup|teardown>]';
    
    protected function configure()
    {
        $this->setName('repo-fixture')
            ->setDescription(self::DESCRIPTION)
            ->setDefinition(array(
				  new InputArgument('action', InputArgument::OPTIONAL, 'Action to take ' . self::ARG_DESCRIPTION),
        ))
        ->setHelp($this->getHelpText());
    }


    protected function execute(InputInterface $input, OutputInterface  $output)
    {
        $headerStyle = new OutputFormatterStyle('white', 'black', array('bold'));
        $output->getFormatter()->setStyle('header', $headerStyle);

        if (!$input->hasArgument('action')){
    	    $this->writeHint($output);
            $output->writeln('<header>Please specify either: '. self::ARG_DESCRIPTION .'</header>');
	    throw new \InvalidArgumentException('No action specified.');
	}

        if ($input->getArgument('action') == 'teardown'){
            $this->tearDownRepo($output);
        } elseif ($input->getArgument('action') == 'setup'){
            $this->setUpRepo($output);
        } else {
            $this->writeHint($output);            
            throw new \InvalidArgumentException('No valid action specified.');
        }

    }

    protected function writeHint(OutputInterface $output)
    {
        $output->writeln('<header>Please specify either: '. self::ARG_DESCRIPTION .'</header>');
    }


    protected function tearDownRepo(OutputInterface $output)
    {
        $output->writeln('<header>Tearing down repo...</header>');
    }
 
    protected function setUpRepo(OutputInterface $output)
    {
        $output->writeln('<header>Setting up repo...</header>');
    }
 

    protected function getHelpText()
    {
        $description = self::DESCRIPTION;
        return <<<EOT
$description

Usage:

Setup the repository fixture:
	  <info>console {$this->getName()} setup</info>

Tear down the repository fixture:
	<info>console {$this->getName()} teardown</info>
EOT;
    }

}

