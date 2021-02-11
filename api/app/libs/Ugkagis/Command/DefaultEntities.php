<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App;

use App\Model\Language\Entity\Language;
use App\Model\Language\Storage\Doctrine\LanguageDao;
use App\Model\Users\Storage\Doctrine\UsersDao;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Model\Users\Entity\Users;

/**
 * Class Email
 * @package App\ePro
 */
class DefaultEntities extends Command
{
    /** @var UsersDao */
    private $usersDao;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * DefaultEntities constructor.
     * @param UsersDao $usersDao
     * @param LanguageDao $languageDao
     */
    public function __construct(
        UsersDao $usersDao,
        LanguageDao $languageDao
    )
    {
        parent::__construct();
        $this->usersDao = $usersDao;
        $this->languageDao = $languageDao;
    }

    protected function configure()
    {
        $this->setName("app:create:entities")
            ->setDescription("Create default entities.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $string = Random::generate();
        $password = Passwords::hash($string);
        $user = new Users();
        $user->setName('Admin');
        $user->setCreated();
        $user->setUsername('administrator@administrator.com');
        $user->setPassword($password);
        $user->setCreated();
        $user->setModified();
        $user->setRole(Users::ADMIN);
        $this->usersDao->save($user);
        $output->writeln('<info>[Ok] - creted user: administrator@administrator.com / ' . $string . ' </info>');

        $language = new Language();
        $language->setVisible(true);
        $language->setCode('en');
        $language->setTranslationCode('en_EN');
        $language->setName('English');
        $language->setDefaults(1);
        $language->setCreated();
        $language->setUsers($user);
        $language->setUsersModified($user);
        $language->setModified();
        $this->languageDao->save($language);
        $output->writeln('<info>[Ok] - creted default language</info>');
    }
}
