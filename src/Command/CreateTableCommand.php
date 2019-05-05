<?php

namespace App\Command;

use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateTableCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-table';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create all Tables')
            ->setHelp('This commmand creates tables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i=1; $i < 101; $i++) {
            $table = new Table();
            $table->setNumber($i);
            $this->em->persist($table);
        }

        $this->em->flush();
    }
}