<?php

namespace Capco\AppBundle\Controller\Site;

use Capco\AppBundle\Entity\Event;
use Capco\AppBundle\Form\EventRegistrationType;
use Capco\AppBundle\Helper\EventHelper;
use Capco\AppBundle\SiteParameter\Resolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    /**
     * @Route("/events", name="app_event", defaults={"_feature_flags" = "calendar"} )
     * @Template("CapcoAppBundle:Event:index.html.twig")
     */
    public function indexAction()
    {
        return [
            'props' => [
                'eventPageTitle' => $this->get(Resolver::class)->getValue('events.jumbotron.title'),
                'eventPageBody' => $this->get(Resolver::class)->getValue('events.content.body'),
            ],
        ];
    }

    /**
     * @Route("/events/{slug}", name="app_event_show", defaults={"_feature_flags" = "calendar"})
     * @ParamConverter("event", options={"mapping": {"slug": "slug"}, "repository_method" = "getOne"})
     * @Template("CapcoAppBundle:Event:show.html.twig")
     */
    public function showAction(Request $request, Event $event)
    {
        $eventHelper = $this->container->get(EventHelper::class);

        if (!$eventHelper->isRegistrationPossible($event)) {
            return [
                'event' => $event,
            ];
        }

        $user = $this->getUser();
        $registration = $eventHelper->findUserRegistrationOrCreate($event, $user);
        $form = $this->createForm(EventRegistrationType::class, $registration, [
            'registered' => $registration->isConfirmed(),
        ]);

        if ('POST' === $request->getMethod()) {
            $registration->setIpAddress($request->getClientIp());
            $registration->setUser($user);
            $form->handleRequest($request);
            $registration->setConfirmed(!$registration->isConfirmed());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($registration);
                $em->flush();

                // We create a session for flashBag
                $flashBag = $this->get('session')->getFlashBag();

                if ($registration->isConfirmed()) {
                    $flashBag->add(
                        'success',
                        $this->get('translator')->trans(
                            'event_registration.create.register_success'
                        )
                    );
                } else {
                    $flashBag->add(
                        'info',
                        $this->get('translator')->trans(
                            'event_registration.create.unregister_success'
                        )
                    );
                }

                return $this->redirect(
                    $this->generateUrl('app_event_show', ['slug' => $event->getSlug()])
                );
            }
        }

        return [
            'form' => $form->createView(),
            'event' => $event,
        ];
    }

    /**
     * @Template("CapcoAppBundle:Event:lastEvents.html.twig")
     */
    public function lastEventsAction(int $max = 3, int $offset = 0)
    {
        $events = $this->get('capco.event.repository')->getLast($max, $offset);

        return ['events' => $events];
    }
}
