<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App;

use App\Model\Email\Manager\EmailManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Email
 * @package App\ePro
 */
class Email extends Command
{
    /** @var  EmailManager */
    private $emailManager;

    public function __construct(EmailManager $emailManager)
    {
        parent::__construct();
        $this->emailManager = $emailManager;
    }

    protected function configure()
    {
        $this->setName("app:process:email")
            ->setDescription("Send email front.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //send Emails front
        $send = 0;
        $error = [];
        $emails = $this->emailManager->getEmailsBy(['send' => 0, 'error' => null], ['priority' => 'ASC'], 10, 0);

        if (empty($emails))  {
            $emails = $this->emailManager->getEmailsBy(['send' => 0, 'error NOT LIKE' => null], ['priority' => 'ASC'], 10, 0);
        }

        shuffle($emails);

        foreach($emails as $email) {
            $send = $this->emailManager->sendEmail($email);
            if ($send === true) {
                $send++;
            } else {
                $error[] = $send;
            }
        }

        if (!empty($error)) {
            $output->writeln('<error>' . implode('\n', $error) . '</error>');
        } else {
            $output->writeln('<info>[Ok] - email send (' . $send . ')</info>');
        }
    }
}
