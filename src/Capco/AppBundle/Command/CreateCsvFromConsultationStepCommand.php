<?php

namespace Capco\AppBundle\Command;

use Capco\AppBundle\GraphQL\GraphQLToCsv;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Writer;

class CreateCsvFromConsultationStepCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('capco:export:consultation')
            ->setDescription('Create csv file from consultation step data');
    }

    private function getContributionsGraphQLqueryByConsultationStep($constulationStep)
    {
        return '
fragment relatedInfos on Contribution {
   related {
     id
     kind
   }
}
fragment voteInfos on YesNoPairedVote {
   id
   ...authorInfos
   value
   createdAt
   expired
}
fragment trashableInfos on TrashableContribution {
  trashed
  trashedAt
  trashedReason
}
fragment authorInfos on ContributionWithAuthor {
  author {
    id
    type {
      name
    }
  }
}
fragment reportInfos on Reporting {
  ...relatedInfos
  id
  ...authorInfos
  type
  body
  createdAt
}
fragment argumentInfos on Argument {
  ...relatedInfos
  id
  type
  body
  createdAt
  updatedAt
  url
  expired
  published
  ...trashableInfos
  votesCount
}
fragment sourceInfos on Source {
  ...relatedInfos
  id
  body
  createdAt
  updatedAt
  expired
  published
  ...trashableInfos
  votesCount
}
{
  contributions(consultation:'.$constulationStep->getId().') {
    id
  	...authorInfos
    section {
      title
    }
    title
    body
    createdAt
    updatedAt
    url
    expired
    published
  	...trashableInfos
    votesCount
    votesCountOk
    votesCountMitige
    votesCountNok
    argumentsCount
    argumentsCountFor
    argumentsCountAgainst
    sourcesCount
    versionsCount
    arguments {
  		...argumentInfos
    }
    sources {
  		...sourceInfos
    }
    reportings {
  		...reportInfos
    }
    versions {
      ...relatedInfos
      id
  		...authorInfos
      title
      body
      comment
      createdAt
      updatedAt
      url
      expired
      published
  		...trashableInfos
      votesCount
      votesCountOk
      votesCountMitige
      votesCountNok
      argumentsCount
      argumentsCountFor
      argumentsCountAgainst
      sourcesCount
      arguments {
  			...argumentInfos
      }
      sources {
  			...sourceInfos
      }
      reportings {
  			...reportInfos
      }
      votes {
        ...voteInfos
      }
   }
   votes {
      ...voteInfos
   }
 }
}';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $steps = $this->getContainer()->get('doctrine')
            ->getRepository('CapcoAppBundle:Steps\ConsultationStep')
            ->findAll();
        $csvGenerator = new GraphQLToCsv();

        foreach ($steps as $step) {
            if ($step->getProject()) {
                $requestString = $this->getContributionsGraphQLqueryByConsultationStep($step);
                $fileName = $step->getProject()->getSlug().'_'.$step->getSlug().'.csv';
                $writer = Writer::createFromPath('/var/www/web/export/'.$fileName, 'w');
                $writer->setDelimiter(',');
                $writer->setNewline("\r\n");
                $writer->setOutputBOM(Writer::BOM_UTF8);
                $csvGenerator->generate(
                  $requestString,
                  $this->getContainer()->get('overblog_graphql.request_executor'),
                  $writer
                );
                $output->writeln('The export file "'.$fileName.'" has been created.');
            }
        }
    }
}
