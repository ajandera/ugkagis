<?php
/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

namespace App\Model\Email\Manager;

use App\Model\Email\Entity\Email;
use App\Model\Email\Storage\IEmailDao;
use App\Model\StorageManager\StorageManager;
use App\Model\Settings\Manager\SettingManager;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;
use App\Model\Documents\Manager\DocumentsManager;
use Nette\Mail\SmtpMailer;

/**
 * Class EmailManager
 * @package App\Model\Email\Manager
 */
class EmailManager
{
    /** @var  IEmailDao */
    private $emailDao;

    /** @var  StorageManager */
    private $storageManager;

    /** @var  SettingManager */
    private $settingsManager;

    /** @var DocumentsManager */
    private $documentsManager;

    public function __construct(
        IEmailDao $emailDao,
        StorageManager $storageManager,
        SettingManager $settingManager,
        DocumentsManager $documentsManager
    )
    {
        $this->emailDao = $emailDao;
        $this->storageManager = $storageManager;
        $this->settingsManager = $settingManager;
        $this->documentsManager = $documentsManager;
    }

    /**
     * @param int $id
     * @return Email
     */
    public function getEmailById($id)
    {
        return $this->emailDao->findById($id);
    }

    /**
     * @param Email $email
     * @return bool
     */
    public function sendEmail(Email $email)
    {
        $smtpHost = $this->settingsManager->getSettingByKey('smtp_host')->getValue();
        $smtpPort = $this->settingsManager->getSettingByKey('smtp_port')->getValue();
        $smtpUser = $this->settingsManager->getSettingByKey('smtp_user')->getValue();
        $smtpPassword = $this->settingsManager->getSettingByKey('smtp_password')->getValue();
        $smtpSecure = $smtpPort === 465 ? 'ssl' : 'tls';
        $reply = $smtpUser;
        $senderName = "UGKAGIS";

        if ($smtpHost === null
            || $smtpPort === null
            || $smtpUser === null
            || $smtpPassword === null
            || $smtpSecure === null
            || $smtpHost == ""
            || $smtpPort == ""
            || $smtpUser == ""
            || $smtpPassword == ""
            || $smtpSecure == ""
        ) {
            $mailer = new SendmailMailer();
        } else {
            $mailer = new SmtpMailer([
                'host' => $smtpHost,
                'username' => $smtpUser,
                'password' => $smtpPassword,
                'secure' => $smtpSecure,
                //'port' => $smtpPort
            ]);
        }

        $emailToSend = new Message();
        $emailToSend->setFrom($reply, $senderName);
        $emailToSend->addReplyTo($reply);

        if ($email->getBcc() != null) {
            if (is_array($email->getEmail())) {
                foreach ($email->getEmail() as $to) {
                    $emailToSend->addBcc($to);
                }
            } else {
                $emailToSend->addBcc($email->getEmail());
            }

            if (is_array($email->getBcc())) {
                foreach ($email->getBcc() as $bcc) {
                    $emailToSend->addBcc($bcc);
                }
            } else {
                $emailToSend->addBcc($email->getBcc());
            }
        } else {
            if (is_array($email->getEmail())) {
                foreach ($email->getEmail() as $to) {
                    $emailToSend->addTo($to);
                }
            } else {
                $emailToSend->addTo($email->getEmail());
            }
        }

        $emailToSend->setSubject($email->getSubject());
        $emailToSend->setHtmlBody($email->getBody());

        if ($email->getAttachment() !== null) {
            foreach ($email->getAttachment() as $attachment) {
                $name = explode('/', $attachment);
                $file = str_replace(" ", "_", $name[count($name) - 1]);
                $emailToSend->addAttachment($file, file_get_contents($attachment));
            }
        }

        try {
            $mailer->send($emailToSend);
            $email->setSend(1);
            $email->setError(null);
            $this->emailDao->save($email);
            return true;
        } catch (SendException $e) {
            $email->setError($e->getMessage());
            $this->emailDao->save($email);
            return $e->getMessage();
        }
    }

    /**
     * @param array $condition
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return Email[]
     */
    public function getEmailsBy(array $condition = [], array $order = [], $limit = null, $offset = null)
    {
        return $this->emailDao->findBy($condition, $order, $limit, $offset);
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $body
     * @param array $bcc
     * @param int $priority
     * @param array $attachment
     * @param null $user
     */
    public function createEmail($email, $subject, $body, array $bcc = [], $priority = 1, array $attachment = [], $user = null)
    {
        $mail = new Email();
        $mail->setSubject($subject);
        $mail->setCreated();
        $mail->setBody($body);
        $mail->setEmail($email);
        $mail->setPriority($priority);
        $mail->setSend(0);
        if ($user !== null) {
            $mail->setUsers($user);
        }

        if($attachment) {
            $mail->setAttachment($attachment);
        }

        foreach ($bcc as $emailAddress) {
            $mail->setBcc($emailAddress);
        }

        $this->emailDao->save($mail);
    }
}
