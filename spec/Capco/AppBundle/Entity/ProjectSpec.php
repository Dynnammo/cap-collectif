<?php

namespace spec\Capco\AppBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Capco\AppBundle\Entity\Project');
    }
}
