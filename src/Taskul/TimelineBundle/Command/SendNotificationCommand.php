<?php
namespace Taskul\TimelineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notification:send')
            ->setDescription('Envía emails con las notificaciones que existan de una cuenta')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine');
        $t = $this->getContainer()->get('translator');
        $mailer = $this->getContainer()->get('mailer');
        $repository = $em->getRepository('TimelineBundle:NotificationMessage');
        $taskRepository = $em->getRepository('TaskBundle:Task');
        $userRepository = $em->getRepository('UserBundle:User');
        $friendRequestRepository = $em->getRepository('FriendBundle:FriendRequest');

        $query = $repository->createQueryBuilder('p')
        ->where('p.read = 0')
        ->orderBy('p.to', 'ASC')
        ->addOrderBy('p.idEntity')
        ->getQuery();

        $notificaciones = $query->getResult();

        $sortNotis = array();
        foreach($notificaciones as $noti) {
                    $sortNotis[$noti->getTo()->getId()][$noti->getIdEntity()][$noti->getContext()]['count'] = (isset($sortNotis[$noti->getTo()->getId()][$noti->getIdEntity()][$noti->getContext()]['count'])?$sortNotis[$noti->getTo()->getId()][$noti->getIdEntity()][$noti->getContext()]['count']++:1);

                    $sortNotis[$noti->getTo()->getId()][$noti->getIdEntity()][$noti->getContext()]['url'] = $noti->getNotiUrl();
        }
        /*
        Array
        (
            [3] => Array User ID
                (
                    [0] => Array
                        (
                            [TASK] => Array Context
                                (
                                    [count] => 1
                                    [url] => /app_dev.php/es/api/get_notification/803/TASK/191
                                )

                            [COMMENT] => Array
                                (
                                    [count] => 1
                                    [url] => /app_dev.php/es/api/get_notification/804/TASK/191
                                )

                        )

                )

            [28] => Array
                (
                    [188] => Array
                        (
                            [TASK] => Array
                                (
                                    [count] => 1
                                    [url] => /app_dev.php/es/api/get_notification/807/TASK/188
                                )

                            [COMMENT] => Array
                                (
                                    [count] => 1
                                    [url] => /app_dev.php/es/api/get_notification/808/TASK/188
                                )

                        )

                )

        )*/
        foreach ($sortNotis as $userid => $n ) {
            foreach ($n as $entityid => $value) {
                $message = array();
                $hash = $from = '';
                foreach ($value as $context => $noti) {
                    switch ($context) {
                        case 'TASK':
                            $task = $taskRepository->find($entityid);
                            $message[] = $t->trans(
                                'messages.task',
                                array('%count%' => $noti['count'],'%name%'=>$task->getName(),'%url%'=>$noti['url']),
                                'TimelineBundle'
                            );
                            break;
                        case 'COMMENT':
                            $task = $taskRepository->find($entityid);
                            $message[] = $t->transChoice(
                                'messages.comment',
                                $noti['count'],
                                array('%count%' => $noti['count'],'%name%'=>$task->getName(),'%url%'=>$noti['url']),
                                'TimelineBundle'
                            );
                            break;
                        case 'DOCUMENT':
                            $task = $taskRepository->find($entityid);
                            $message[] = $t->transChoice(
                                'messages.file',
                                $noti['count'],
                                array('%count%' => $noti['count'],'%name%'=>$task->getName(),'%url%'=>$noti['url']),
                                'TimelineBundle'
                            );
                            break;
                        case 'FRIENDREQUEST':
                        $friendRequest = $friendRequestRepository->find($entityid);
                            $message[] = $t->transChoice(
                                'messages.friendrequest',
                                $noti['count'],
                                array('%count%' => $noti['count'],'%url%'=>$noti['url']),
                                'TimelineBundle'
                            );
                            $hash = $friendRequest->getHash();
                            $from = $friendRequest->getFrom();
                            break;
                        case 'MESSAGE':
                            $message[] = $t->transChoice(
                                'messages.message',
                                $noti['count'],
                                array('%count%' => $noti['count'],'%url%'=>$noti['url']),
                                'TimelineBundle'
                            );
                            break;
                    }
                }

                $user = $userRepository->find($userid);

                if($user){
                    $to = $user->getEmail();
                    $params = array(
                        'hash' => $hash,
                        'notifications' => $message,
                        'from' => $from,
                    ); // template's parameters
                    $locale = 'es';                    // the language to use to generate the message.

                    // create a swift message from the 'super-template' reference
                    $message = '';
                    // then send the email
                    $mailer->send($message);
                }
            }

        }

    }
}
