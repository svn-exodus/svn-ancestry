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

		//Check input.
        if (!$input->hasArgument('action')){
    	    $this->writeActionHint($output);
	    	throw new \InvalidArgumentException('No action specified.');
		}		
		
		//Switch on value of action	
        if ($input->getArgument('action') == 'teardown'){
            $this->tearDownRepo($output);
        } elseif ($input->getArgument('action') == 'setup'){       	
			
			//Check for prerequisite programs.	
			if ($this->hasSvnClient()){
				$output->writeln('<error>SVN is not installed</error>');
    		   	throw new \RuntimeException('Prerequisite commands not found on system.');
    		}
			
			
        	$this->setUpRepo($output);
        } else {
            $this->writeActionHint($output);            
            throw new \InvalidArgumentException('No valid action specified.');
        }

    }

    protected function writeActionHint(OutputInterface $output)
    {
        $output->writeln('<error>Please specify an action. Either: '. self::ARG_DESCRIPTION .'</error>');
    }   		


    protected function tearDownRepo(OutputInterface $output)
    {		
        $output->writeln('<header>Tearing down repo...</header>');
    }
 
    protected function setUpRepo(OutputInterface $output)
    {
        $output->writeln('<header>Setting up repo...</header>');
    }
	
	
	/**
	 * @return boolean TRUE if svn installed on the current machine.
	 */
	protected function hasSvnClient()
	{
		return $this->hasCommand('svn');
	}

	/**
	 * @param string command to test for.
	 * @return boolean TRUE if $command installed on the current machine.
	 */	
	protected function hasCommand($command)
	{
		$outputArray = array();
		exec('type -f '. escapeshellarg($command)  .' &>/dev/null; echo $?', $outputArray);	
		return ($outputArray[0]) ? FALSE : TRUE;
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

