<?php
namespace Capco\AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculateCountersCommand extends ContainerAwareCommand
{
    public $force;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function configure()
    {
        $this->setName('capco:compute:counters')
            ->setDescription('Recalculate the application counters')
            ->addOption(
                'force',
                false,
                InputOption::VALUE_NONE,
                'set this option to force complete recomputation'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $contributionResolver = $container->get('capco.contribution.resolver');
        $this->force = $input->getOption('force');

        // ****************************** Opinion counters **********************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion o set o.versionsCount = (
            select count(DISTINCT ov.id)
            from CapcoAppBundle:OpinionVersion ov
            where ov.enabled = 1 AND ov.isTrashed = 0 AND ov.expired = 0 AND ov.parent = o
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion o set o.argumentsCount = (
          select count(DISTINCT a.id)
          from CapcoAppBundle:Argument a
          WHERE a.isEnabled = 1 AND a.isTrashed = 0 AND a.expired = 0 AND a.opinion = o
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion o set o.sourcesCount = (
          select count(DISTINCT s.id)
          from CapcoAppBundle:Source s
          WHERE s.isEnabled = 1 AND s.isTrashed = 0 AND s.expired = 0 AND s.opinion = o
        )'
        );

        // Currently, you cannot UPDATE a table and select from the same table in a subquery.
        $this->executeQuery(
            'UPDATE opinion AS o
          JOIN
          ( SELECT p.id, COUNT(DISTINCT r.opinion_source) AS cnt
            FROM opinion p
            LEFT JOIN opinion_relation r
            ON r.opinion_source = p.id OR r.opinion_target = p.id
            WHERE p.enabled = 1 AND p.trashed = 0 AND p.expired = 0
            GROUP BY p.id
          ) AS g
          ON g.id = o.id
          SET o.connections_count = g.cnt',
            true
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion a set a.votesCountOk = (
          select count(DISTINCT ov.id)
          from CapcoAppBundle:OpinionVote ov
          where ov.opinion = a AND ov.expired = 0 AND ov.value = 1 group by ov.opinion
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion a set a.votesCountMitige = (
          select count(DISTINCT ov.id)
          from CapcoAppBundle:OpinionVote ov
          where ov.opinion = a AND ov.expired = 0 AND ov.value = 0 group by ov.opinion
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Opinion a set a.votesCountNok = (
          select count(DISTINCT ov.id)
          from CapcoAppBundle:OpinionVote ov
          where ov.opinion = a AND ov.expired = 0 AND ov.value = -1 group by ov.opinion
        )'
        );

        // ******************************** Opinion version counters ****************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:OpinionVersion ov set ov.argumentsCount = (
          select count(DISTINCT a.id)
          from CapcoAppBundle:Argument a
          WHERE a.isEnabled = 1 AND a.isTrashed = 0 AND a.expired = 0 AND a.opinionVersion = ov
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:OpinionVersion ov set ov.sourcesCount = (
          select count(DISTINCT s.id)
          from CapcoAppBundle:Source s
          WHERE s.isEnabled = 1 AND s.isTrashed = 0 AND s.expired = 0 AND s.opinionVersion = ov
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:OpinionVersion ov set ov.votesCountOk = (
          select count(DISTINCT ovv.id)
          from CapcoAppBundle:OpinionVersionVote ovv
          where ovv.opinionVersion = ov AND ovv.expired = 0 AND ovv.value = 1 group by ovv.opinionVersion
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:OpinionVersion ov set ov.votesCountMitige = (
          select count(DISTINCT ovv.id)
          from CapcoAppBundle:OpinionVersionVote ovv
          where ovv.opinionVersion = ov AND ovv.expired = 0 AND ovv.value = 0 group by ovv.opinionVersion
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:OpinionVersion ov set ov.votesCountNok = (
          select count(DISTINCT ovv.id)
          from CapcoAppBundle:OpinionVersionVote ovv
          where ovv.opinionVersion = ov AND ovv.expired = 0 AND ovv.value = -1 group by ovv.opinionVersion
        )'
        );

        // ************************************ Votes counters **********************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Argument a set a.votesCount = (
          select count(DISTINCT av.id)
          from CapcoAppBundle:ArgumentVote av
          where av.argument = a AND av.expired = 0 group by av.argument
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Source s set s.votesCount = (
          select count(DISTINCT sv.id)
          from CapcoAppBundle:SourceVote sv
          where sv.source = s AND sv.expired = 0 group by sv.source
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Comment c set c.votesCount = (
          select count(DISTINCT cv.id)
          from CapcoAppBundle:CommentVote cv
          where cv.comment = c AND cv.expired = 0 group by cv.comment
        )'
        );

        // **************************************** Comments counters ***************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Post p set p.commentsCount = (
          select count(DISTINCT pc.id)
          from CapcoAppBundle:PostComment pc
          where pc.post = p AND pc.isEnabled = 1 AND pc.isTrashed = 0 AND pc.expired = 0 GROUP BY pc.post
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Event e set e.commentsCount = (
          select count(DISTINCT ec.id)
          from CapcoAppBundle:EventComment ec
          where ec.Event = e AND ec.isEnabled = 1 AND ec.isTrashed = 0 AND ec.expired = 0 GROUP BY ec.Event
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Proposal p set p.commentsCount = (
          select count(DISTINCT pc.id)
          from CapcoAppBundle:ProposalComment pc
          where pc.proposal = p AND pc.isEnabled = 1 AND pc.isTrashed = 0 AND pc.expired = 0 GROUP BY pc.proposal
        )'
        );

        // ************************ Consultation step counters ***********************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.opinionCount = (
          select count(DISTINCT o.id)
          from CapcoAppBundle:Opinion o
          where o.step = cs AND o.isEnabled = 1 AND o.isTrashed = 0 AND o.expired = 0 group by o.step
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.opinionVersionsCount = (
          select count(DISTINCT ov.id)
          from CapcoAppBundle:OpinionVersion ov INNER JOIN CapcoAppBundle:Opinion o WITH ov.parent = o
          where o.step = cs AND ov.enabled = 1 AND ov.isTrashed = 0 AND o.isEnabled = 1 AND o.isTrashed = 0 group by o.step
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.trashedOpinionCount = (
          select count(DISTINCT o.id)
          from CapcoAppBundle:Opinion o
          where o.step = cs AND o.isEnabled = 1 AND o.isTrashed = 1 AND o.expired = 0 group by o.step
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.trashedOpinionVersionsCount = (
          select count(DISTINCT ov.id)
          from CapcoAppBundle:OpinionVersion ov INNER JOIN CapcoAppBundle:Opinion o WITH ov.parent = o
          where o.step = cs AND ov.enabled = 1 AND ov.isTrashed = 1 AND o.isEnabled = 1 AND o.expired = 0 group by o.step
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.argumentCount = (
          select count(DISTINCT a.id)
          from CapcoAppBundle:Argument a
          LEFT JOIN CapcoAppBundle:OpinionVersion ov WITH a.opinionVersion = ov
          LEFT JOIN CapcoAppBundle:Opinion o WITH a.opinion = o
          LEFT JOIN CapcoAppBundle:Opinion ovo WITH ov.parent = ovo
          WHERE a.isEnabled = 1 AND a.isTrashed = 0 AND a.expired = 0 AND (
            (a.opinion IS NOT NULL AND o.isEnabled = 1 AND o.step = cs)
            OR
            (a.opinionVersion IS NOT NULL AND ov.enabled = 1 AND ovo.isEnabled = 1 AND ovo.step = cs)
          )
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.trashedArgumentCount = (
          select count(DISTINCT a.id)
          from CapcoAppBundle:Argument a
          LEFT JOIN CapcoAppBundle:OpinionVersion ov WITH a.opinionVersion = ov
          LEFT JOIN CapcoAppBundle:Opinion o WITH a.opinion = o
          LEFT JOIN CapcoAppBundle:Opinion ovo WITH ov.parent = ovo
          WHERE a.isEnabled = 1 AND a.isTrashed = 1 AND (
            (a.opinion IS NOT NULL AND o.isEnabled = 1 AND o.step = cs)
            OR
            (a.opinionVersion IS NOT NULL AND ov.enabled = 1 AND ovo.isEnabled = 1 AND ovo.step = cs)
          )
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.sourcesCount = (
          select count(DISTINCT s.id)
          from CapcoAppBundle:Source s
          LEFT JOIN CapcoAppBundle:OpinionVersion ov WITH s.opinionVersion = ov
          LEFT JOIN CapcoAppBundle:Opinion o WITH s.Opinion = o
          LEFT JOIN CapcoAppBundle:Opinion ovo WITH ov.parent = ovo
          WHERE s.isEnabled = 1 AND s.isTrashed = 0 AND s.expired = 0 AND (
            (s.Opinion IS NOT NULL AND o.isEnabled = 1 AND o.step = cs)
            OR
            (s.opinionVersion IS NOT NULL AND ov.enabled = 1 AND ovo.isEnabled = 1 AND ovo.step = cs)
          )
        )'
        );

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\ConsultationStep cs set cs.trashedSourceCount = (
          select count(DISTINCT s.id)
          from CapcoAppBundle:Source s
          LEFT JOIN CapcoAppBundle:OpinionVersion ov WITH s.opinionVersion = ov
          LEFT JOIN CapcoAppBundle:Opinion o WITH s.Opinion = o
          LEFT JOIN CapcoAppBundle:Opinion ovo WITH ov.parent = ovo
          WHERE s.isEnabled = 1 AND s.isTrashed = 1 AND o.expired = 0 AND (
            (s.Opinion IS NOT NULL AND o.isEnabled = 1 AND o.step = cs)
            OR
            (s.opinionVersion IS NOT NULL AND ov.enabled = 1 AND ovo.isEnabled = 1 AND ovo.step = cs)
          )
        )'
        );

        $consultationSteps = $container->get('capco.consultation_step.repository')->findAll();
        foreach ($consultationSteps as $cs) {
            if ($cs->isOpen() || $this->force) {
                $participants = $contributionResolver->countStepContributors($cs);
                $this->executeQuery(
                    'UPDATE CapcoAppBundle:Steps\ConsultationStep cs
                    set cs.contributorsCount = ' .
                        $participants .
                        '
                    where cs.id = \'' .
                        $cs->getId() .
                        '\''
                );
                $votes = $contributionResolver->countStepVotes($cs);
                $this->executeQuery(
                    'UPDATE CapcoAppBundle:Steps\ConsultationStep cs
                    set cs.votesCount = ' .
                        $votes .
                        '
                    where cs.id = \'' .
                        $cs->getId() .
                        '\''
                );
            }
        }

        // ****************************** Collect step counters **************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\CollectStep cs set cs.proposalsCount = (
          select count(DISTINCT p.id)
          from CapcoAppBundle:Proposal p
          INNER JOIN CapcoAppBundle:ProposalForm pf WITH p.proposalForm = pf
          where pf.step = cs AND p.expired = 0 AND p.draft = 0 AND p.isTrashed = 0 AND p.deletedAt IS NULL AND p.enabled = 1
          group by pf.step
        )'
        );

        $collectSteps = $container->get('capco.collect_step.repository')->findAll();
        foreach ($collectSteps as $cs) {
            if ($cs->isOpen() || $this->force) {
                $participants = $contributionResolver->countStepContributors($cs);
                $this->executeQuery(
                    'UPDATE CapcoAppBundle:Steps\CollectStep cs
                    set cs.contributorsCount = ' .
                        $participants .
                        '
                    where cs.id = \'' .
                        $cs->getId() .
                        '\''
                );
            }
        }

        // ****************************** Questionnaire step counters **************************************

        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\QuestionnaireStep qs set qs.repliesCount = (
          select count(DISTINCT r.id)
          from CapcoAppBundle:Reply r INNER JOIN CapcoAppBundle:Questionnaire q WITH r.questionnaire = q
          where q.step = qs AND r.enabled = 1 group by q.step
        )'
        );

        $questionnaireSteps = $this->entityManager
            ->getRepository('CapcoAppBundle:Steps\QuestionnaireStep')
            ->findAll();
        foreach ($questionnaireSteps as $qs) {
            if ($qs->isOpen() || $this->force) {
                $participants = $contributionResolver->countStepContributors($qs);
                $this->executeQuery(
                    'UPDATE CapcoAppBundle:Steps\QuestionnaireStep qs
                    set qs.contributorsCount = ' .
                        $participants .
                        '
                    where qs.id = \'' .
                        $qs->getId() .
                        '\''
                );
            }
        }

        // ****************************** Selection steps counters **************************************
        $this->executeQuery(
            'UPDATE CapcoAppBundle:Steps\SelectionStep ss set ss.votesCount = (
          select count(DISTINCT pv.id)
          from CapcoAppBundle:ProposalSelectionVote pv INNER JOIN CapcoAppBundle:Proposal p WITH pv.proposal = p
          where pv.selectionStep = ss AND pv.expired = 0 AND p.expired = 0 AND p.draft = 0 AND p.isTrashed = 0 AND p.deletedAt IS NULL AND p.enabled = 1
          group by pv.selectionStep
        )'
        );

        $selectionSteps = $container->get('capco.selection_step.repository')->findAll();
        foreach ($selectionSteps as $ss) {
            if ($ss->isOpen() || $this->force) {
                $anonymousParticipants = $container
                    ->get('capco.user.repository')
                    ->countSelectionStepProposalAnonymousVoters($ss);
                $participants =
                    $contributionResolver->countStepContributors($ss) + $anonymousParticipants;
                $this->executeQuery(
                    'UPDATE CapcoAppBundle:Steps\SelectionStep ss
                    set ss.contributorsCount = ' .
                        $participants .
                        '
                    where ss.id = \'' .
                        $ss->getId() .
                        '\''
                );
            }
        }

        $output->writeln('Calculation completed');
    }

    private function executeQuery(string $sql, bool $executeUpdate = false): void
    {
        $retry = 0;
        $maxRetries = 3;

        try {
            $this->entityManager->beginTransaction();

            $executeUpdate
                ? $this->entityManager->getConnection()->executeUpdate($sql)
                : $this->entityManager->createQuery($sql)->execute();

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            ++$retry;

            if ($retry === $maxRetries) {
                throw $exception;
            }
        }
    }
}
