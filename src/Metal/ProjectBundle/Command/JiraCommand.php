<?php
namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class JiraCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:jira')
            ->addOption('date-from', null, InputOption::VALUE_OPTIONAL, 'С какой даты.')
            ->addOption('date-to', null, InputOption::VALUE_OPTIONAL, 'По какую дату')
            ->addOption('jira-login', null, InputOption::VALUE_REQUIRED)
            ->addOption('jira-password', null, InputOption::VALUE_REQUIRED)
            ->addOption('bitbacket-login', null, InputOption::VALUE_REQUIRED)
            ->addOption('bitbacket-password', null, InputOption::VALUE_REQUIRED)
            ->addOption(
                'projects',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'metalloprokat, igram, katushkin-new',
                array('metalloprokat')
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $browser = $this->getContainer()->get('buzz.browser.grabber');

        $skips = array(
            'Merge with staging'
        );

        $dateFrom = (new \DateTime($input->getOption('date-from') ?: 'now'))->modify('midnight');
        $dateTo = (new \DateTime($input->getOption('date-to') ?: 'now'))->modify('midnight');

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($input->getOption('bitbacket-login').':'.$input->getOption('bitbacket-password'))
        );

        $browser->getClient()->setTimeout(20);

        $result = array();

        foreach ($input->getOption('projects') as $project) {
            $url = 'https://api.bitbucket.org/2.0/repositories/Koc/'.$project.'/commits';

            do {

                sleep(3);
                $data = json_decode($browser->get($url, $headers)->getContent(), true);

                foreach ((array)$data['values'] as $commit) {
                    if (in_array($commit['message'], $skips)) {
                        continue;
                    }

                    if (stripos($commit['author']['raw'], $input->getOption('bitbacket-login')) === false) {
                        continue;
                    }

                    $date = (new \DateTime($commit['date']))->modify('+3 hours');

                    $curDate = clone $date;
                    $curDate->modify('midnight');

                    if ($curDate->format('Y-m-d') > $dateTo->format('Y-m-d')) {
                        continue;
                    }

                    if ($curDate->format('Y-m-d') < $dateFrom->format('Y-m-d')) {
                        break 2;
                    }

                    preg_match('/^#(\w+-\d+)/ui', trim($commit['message']), $projectMatches);

                    if (!isset($projectMatches[1])) {
                        continue;
                    }

                    $result[$date->format('Y-m-d')][] = array(
                        'ticket' => $projectMatches[1],
                        'comment' => trim(preg_replace('/^#(\w+-\d+)/ui', '', trim($commit['message']))),
                        'time' => $date
                    );
                }

                $url = $data['next'];
            } while(!empty($data['next']));
        }

        $ticketsTime = $this->recalculateDayTime($result);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action?', false);


        foreach ($ticketsTime as $date => $ticketTime) {
            foreach ($ticketTime as $ticket => $data) {

                $output->writeln($date);
                $output->writeln($ticket);
                $output->writeln($data['comment']);
                $output->writeln($data['time']);

                $sendData = array(
                    'self' => sprintf('http://projects.brouzie.com/rest/api/2.0/issue/%s/worklog', $ticket),
                    'author' => array(
                        'self' => 'http://projects.brouzie.com/rest/api/2.0/user?username='.$input->getOption('jira-login'),
                        'name' => $input->getOption('jira-login'),
                    ),
                    //2012-02-15T17:34:37.937-0600
                    'started' => $date.'T00:00:00.000-0000',
                    'comment' => $data['comment'],
                    'timeSpent' => $data['time'].'h',
                );

                if (!$helper->ask($input, $output, $question)) {
                    continue;
                }

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic '. base64_encode($input->getOption('jira-login').':'.$input->getOption('jira-password'))
                );

                $browser->post(
                    sprintf('http://projects.brouzie.com/rest/api/2/issue/%s/worklog', $ticket),
                    $headers,
                    json_encode($sendData)
                );

            }
        }
    }

    private function recalculateDayTime(array $data)
    {
        $ticketsTime = array();
        foreach ($data as $date => $tickets) {
            usort($tickets, function($a, $b) {
                if ($a['time'] === $b['time']) {
                    return 0;
                }

                return ($a['time'] < $b['time']) ? -1 : 1;
            });

            $lastTime = (new \DateTime($date))->setTime(9, 0, 0);
            foreach ($tickets as $index => $ticket) {
                $newTime = new \DateTime($ticket['time']->format('d.m.Y H:i:s'));
                $diff = $newTime->diff($lastTime);
                if (!isset($ticketsTime[$date][$ticket['ticket']]['time'])) {
                    $ticketsTime[$date][$ticket['ticket']]['time'] = 0;
                    $ticketsTime[$date][$ticket['ticket']]['comment'] = '';
                }

                $ticketsTime[$date][$ticket['ticket']]['comment'] .= ($ticketsTime[$date][$ticket['ticket']]['comment'] ? '; ' : '') . $ticket['comment'];

                $lastTime = $newTime;

                $ticketsTime[$date][$ticket['ticket']]['time'] += sprintf('%d.%d', $diff->h, $this->calculateMinutes($diff));

                if (count($tickets) - 1 === $index ) {
                    $sum = $this->getSum($ticketsTime[$date]);
                    if ($sum < 8) {
                        $ticketsTime[$date][$this->getMinValueIndex($ticketsTime[$date])]['time'] += 8 - $sum;
                    } elseif ($sum > 8) {
                        $ticketsTime[$date][$this->getMaxValueIndex($ticketsTime[$date])]['time'] -= 8 - $sum;
                    }
                }
            }
        }

        return $ticketsTime;
    }

    private function getSum(array $array)
    {
        $sum = 0;
        foreach ($array as $el) {
            $sum += $el['time'];
        }

        return $sum;
    }

    private function getMaxValueIndex(array $array)
    {
        $max = 0;
        $index = null;
        foreach ($array as $key => $el) {
            if (!$max) {
                $index = $key;
            }

            if ($max < (float)$el['time']) {
                $index = $key;
            }
        }

        return $index;
    }

    private function getMinValueIndex(array $array)
    {
        $max = 0;
        $index = null;
        foreach ($array as $key => $el) {
            if (!$max) {
                $index = $key;
            }

            if ($max > (float)$el['time']) {
                $index = $key;
            }
        }

        return $index;
    }

    private function calculateMinutes(\DateInterval $diff)
    {
        $minutes = $diff->i;
        if ($diff->i < 10) {
            return 1;
        }

        return round(10 * $minutes / 100);
    }
}
