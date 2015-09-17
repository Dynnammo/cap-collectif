<?php

namespace Capco\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Capco\UserBundle\Entity\User;
use Capco\AppBundle\Entity\OpinionType;
use Capco\AppBundle\Entity\Opinion;
use Capco\AppBundle\Entity\OpinionAppendix;
use Capco\AppBundle\Entity\AppendixType;
use Capco\AppBundle\Entity\Consultation;
use Capco\AppBundle\Entity\ConsultationAbstractStep;
use Capco\AppBundle\Entity\ConsultationStep;
use Capco\AppBundle\Entity\ConsultationType;

class CreatePJLFromCsvCommand extends ContainerAwareCommand
{

    private $opinionTypes = [];
    private $username = 'Gouvernement';
    private $password = 'KvN+j\E43&2U%KAF';

    private $siteParameters = [
        'admin.mail.notifications.send_address' => 'coucou@cap-collectif.com',
    ];

    protected function findOpinionTypeByTitle($title, $parentTitle = false, $rootTitle = false)
    {
        foreach ($this->opinionTypes as $type) {
            if ($type->getTitle() == $title) {
                if (!$parentTitle) {
                    return $type;
                }

                $parent = $type->getParent();
                if ($parent->getTitle() == $parentTitle) {
                    if (!$rootTitle) {
                        return $type;
                    }
                    $root = $parent->getParent();

                    if ($root->getTitle() == $rootTitle) {
                        return $type;
                    }
                }
            }
        }
        throw new \Exception("Unknown opinion title: " . $title, 1);

    }

    protected function configure()
    {
        $this
        ->setName('import:pjl-from-csv')
        ->setDescription('Import from CSV file');
    }

    protected function generateDefaultContent()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $userType = $em->getRepository('CapcoUserBundle:UserType')
               ->findOneBySlug('institution');



        $media = $this->getContainer()
                      ->get('sonata.media.manager.media')
                      ->create();

        $media->setBinaryContent(realpath(dirname(__FILE__)).'/../../../../pjl/marianne.jpg');
        $media->setEnabled(true);
        $media->setName('gouvernement pic');

         $this->getContainer()
              ->get('sonata.media.manager.media')
              ->save($media, 'default', 'sonata.media.provider.image');

        $user = new User();
        $user->setMedia($media);
        $user->setUserName($this->username);
        $user->setPlainPassword($this->password);
        $user->setEnabled(true);
        $user->setUserType($userType);
        $em->persist($user);

        foreach ($this->siteParameters as $key => $value) {
            $param = $em->getRepository('CapcoAppBundle:SiteParameter')
                        ->findOneByKeyname($key)
                    ;
            $param->setValue($value);
        }

        $em->flush();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generateDefaultContent();
        $this->import($input, $output);
    }

    protected function import(InputInterface $input, OutputInterface $output)
    {
        $opinionTypesData = $this->getOpinionTypes();
        $opinions = $this->getOpinions();
        $motives = $this->getMotives();

        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('CapcoUserBundle:User')
                   ->findOneByUsername($this->username);

        $progress = new ProgressBar($output, count($opinionTypesData) + count($opinions));
        $progress->start();

        $consultation = new Consultation();
        $consultation->setTitle('PJL Numérique');

        $consultationAbsStep = new ConsultationAbstractStep();
        $consultationAbsStep->setPosition(0);

        $consultationStep = new ConsultationStep();
        $consultationStep->setTitle('Consultation');
        $consultationStep->setStartAt(new \DateTime());
        $consultationStep->setEndAt((new \DateTime())->modify('+3 weeks'));

        $consultationType = new ConsultationType();
        $consultationType->setTitle('PJL');

        $consultationStep->setConsultationType($consultationType);
        $consultationAbsStep->setStep($consultationStep);
        $consultation->addStep($consultationAbsStep);

        $em->persist($consultation);
        $em->persist($consultationType);
        $em->flush();

        $position = 0;

        foreach ($opinionTypesData as $row) {

            $opinionType = new OpinionType();
            $opinionType->setTitle($row['title']);
            $opinionType->setSubtitle($row['subtitle']);
            $opinionType->setPosition($position);
            $opinionType->setColor('gray');
            $opinionType->setDefaultFilter('positions');
            $opinionType->setIsEnabled($row['contribuable']);
            $opinionType->setVersionable($row['contribuable']);
            $opinionType->setVotesHelpText('Pensez-vous que cette proposition permet d\'atteindre les objectifs présentés dans l\'exposé des motifs ?');

            if (!empty($row['parent'])) {
                $parent = $this->findOpinionTypeByTitle($row['parent']);
                if (!$parent) {
                    throw new \Exception("Parent does not exist", 1);
                }
                $opinionType->setParent($parent);
            } else {
                $opinionType->setConsultationType($consultationType);
            }

            $em->persist($opinionType);
            $em->flush();
            $this->opinionTypes[] = $opinionType;
            $position++;
            $progress->advance(1);
        }

        foreach ($opinions as $row) {

            $opinionType = $this->findOpinionTypeByTitle($row['opinionType'], $row['opinionType_parent'], $row['opinionType_root']);
            $opinion = $em->getRepository('CapcoAppBundle:Opinion')
                          ->findOneByTitle($row['opinion']);

            if (!is_object($opinion)) {
                $opinion = new Opinion();
                $opinion->setTitle($row['opinion']);
                $opinion->setOpinionType($opinionType);
                $opinion->setAuthor($user);
                $opinion->setStep($consultationStep);
            }

            $paragraphe = $row['paragraphe'];

            if (!empty($row['link'])) {
                $pos = strpos($paragraphe, $row['link']);
                if ($pos === false) {
                    throw new \Exception("Unable to find link", 1);
                }

                $string = '<span data-modal-title="'.$row['modal_title'].'" data-modal-current="'.$row['modal_current'].'" data-modal-next="'.$row['modal_next'].'">' . $row['link'] . '</span>';
                $paragraphe = substr_replace($paragraphe, $string, $pos, strlen($row['link']));
            }

            $content = $opinion->getBody();
            $content .= '<p>' . $paragraphe . '</p>';
            $opinion->setBody($content);

            $em->persist($opinion);
            $em->flush();
            $progress->advance(1);
        }

        $appendixType = new AppendixType();
        $appendixType->setTitle('Exposé des motifs');
        $appendixType->setHelpText('L\'Exposé des motifs');

        foreach ($motives as $row) {

            $opinion = $em->getRepository('CapcoAppBundle:Opinion')
                          ->findOneByTitle($row['opinion']);

            if (!is_object($opinion)) {
                throw new \Exception("Unknown title", 1);
            }

            if (count($opinion->getAppendices()) === 0) {
                $motif = new OpinionAppendix();
                $motif->setAppendixType($appendixType);
                $motif->setBody('<p>' . $row['motif'] . '</p>');
                $opinion->addAppendice($motif);
            } else {
                $motif = $opinion->getAppendices()[0];
                $content = $motif->getBody();
                $content .= '<p>' . $row['motif'] . '</p>';
                $motif->setBody($content);
            }

            $em->flush();
            $progress->advance(1);
        }

        $progress->finish();
    }

    protected function getOpinions()
    {
        return $this->getContainer()
                    ->get('import.csvtoarray')
                    ->convert('pjl/opinions.csv');
    }

    protected function getMotives()
    {
        return $this->getContainer()
                    ->get('import.csvtoarray')
                    ->convert('pjl/motifs.csv');
    }

    protected function getOpinionTypes()
    {
        return $this->getContainer()
                    ->get('import.csvtoarray')
                    ->convert('pjl/opinionTypes.csv');
    }
}
