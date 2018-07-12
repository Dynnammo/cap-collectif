<?php

namespace Capco\AppBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeletedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->hasField('deletedAt')) {
            return $targetTableAlias . '.deleted_at > \'' . date('Y-m-d H:i:s') . '\' OR ' . $targetTableAlias . '.deleted_at IS NULL';
        }

        return '';
    }
}
