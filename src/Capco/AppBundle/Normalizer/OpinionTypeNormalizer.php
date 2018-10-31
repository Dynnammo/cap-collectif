<?php
namespace Capco\AppBundle\Normalizer;

use Capco\AppBundle\Entity\OpinionType;
use Capco\AppBundle\Resolver\OpinionTypesResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class OpinionTypeNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;
    private $router;
    private $normalizer;
    private $resolver;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        OpinionTypesResolver $resolver
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->resolver = $resolver;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        $groups = array_key_exists('groups', $context) ? $context['groups'] : [];

        if (\in_array('Elasticsearch', $groups)) {
            return;
        }
        if (\in_array('OpinionTypeLinks', $groups)) {
            $availableTypes = $this->resolver->getAvailableLinkTypesForConsultationStepType(
                $object->getConsultationStepType()
            );

            $serializedTypes = [];
            foreach ($availableTypes as $type) {
                $serializedTypes[] = $this->normalizer->normalize($type, $format, $context);
            }

            $data = $this->normalizer->normalize($object, $format, $context);
            $data['availableLinkTypes'] = $serializedTypes;

            return $data;
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OpinionType;
    }
}
