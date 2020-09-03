<?php

namespace Metal\NewsletterBundle\Command;

use Doctrine\ORM\EntityManager;
use Fetch\Server;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandleBounceEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:newsletter:handle-bounce-emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */

//
//        $server = new Server('imap.yandex.ru');
//        $server->setAuthentication('sendmail@product.ru', '1qwerty2');
//        $server->setMailBox('INBOX');
//
//        $messages = $server->getOrderedMessages(SORTDATE, true, 5);
//
//        foreach ($messages as $message) {
//            var_dump('subject', $message->getSubject());
//            $attachments = $message->getAttachments();
//            if (!$attachments) {
//                $message->moveToMailBox('PROCESSED');
//
//                continue;
//            }
//            foreach ($attachments as $attachment) {
//                var_dump('filename', $attachment->getFileName());
//                var_dump('attachment');
//                echo '<pre>',quoted_printable_decode($attachment->getData()),'</pre>';
//            }
//            var_dump('-------------------------');
//        }
//        exit;


        set_time_limit(3000);


        /* connect to gmail with your credentials */
        $hostname = '{imap.yandex.ru:143/novalidate-cert}INBOX';
        $username = 'sendmail@product.ru'; # e.g somebody@gmail.com
        $password = '1qwerty2';


        /* try to connect */
        $inbox = imap_open($hostname, $username, $password, 0, 2) or die('Cannot connect to Yandex: '.imap_last_error());


        /* get all new emails. If set to 'ALL' instead
         * of 'NEW' retrieves all the emails, but can be
         * resource intensive, so the following variable,
         * $max_emails, puts the limit on the number of emails downloaded.
         *
         */
        $emails = imap_sort($inbox, SORTDATE, 1);

        /* useful only if the above search is set to 'ALL' */
        $max_emails = 50;


        /* if any emails found, iterate through each email */
        if ($emails) {
            $count = 1;

            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach ($emails as $email_number) {

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);

                /* get mail message, not actually used here.
                   Refer to http://php.net/manual/en/function.imap-fetchbody.php
                   for details on the third parameter.
                 */
//                $message = imap_fetchbody($inbox, $email_number, 2);

                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);


                $headers = imap_fetchheader($inbox, $email_number, FT_PREFETCHTEXT);
                $body = imap_body($inbox, $email_number);
                $filename = microtime(true).".eml";

                file_put_contents('./'.$filename, $headers."\n".$body);
                continue;

                $attachments = array();

                /* if any attachments found... */
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                            /* 3 = BASE64 encoding */
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            } /* 4 = QUOTED-PRINTABLE encoding */
                            elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode(
                                    $attachments[$i]['attachment']
                                );
                            }
                        }
                    }
                }

                /* iterate through each attachment and save it */
                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $filename = microtime(true).".dat";


                        /* prefix the email number to the filename in case two emails
                         * have the attachment with the same file name.
                         */
                        $fp = fopen("./".$email_number."-".$filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                    }
                }

                if ($count++ >= $max_emails) {
                    break;
                }
            }
        }

        imap_close($inbox);

        $output->writeln(sprintf('%s: Finish command.', date('d.m.Y H:i:s')));
    }
}
