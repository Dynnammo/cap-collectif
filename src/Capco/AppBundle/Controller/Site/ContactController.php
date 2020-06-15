<?php

namespace Capco\AppBundle\Controller\Site;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="app_contact")
     * @Template("CapcoAppBundle:Contact:list.html.twig")
     */
    public function contactAction(Request $request)
    {
        return [];
    }
}
