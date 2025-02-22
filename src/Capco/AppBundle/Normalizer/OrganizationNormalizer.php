<?php

namespace Capco\AppBundle\Normalizer;

use Capco\AppBundle\Entity\Organization\Organization;
use Capco\AppBundle\Search\ContributionSearch;
use Capco\AppBundle\Toggle\Manager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;

class OrganizationNormalizer implements
    NormalizerInterface,
    SerializerAwareInterface,
    CacheableSupportsMethodInterface
{
    use SerializerAwareTrait;

    private const RELATED_GROUPS = [
        'ElasticsearchProposalNestedAuthor',
        'ElasticsearchProjectNestedAuthor',
        'ElasticsearchEventNestedAuthor',
        'ElasticsearchProposalNestedProject',
        'ElasticsearchEventNestedProject',
        'ElasticsearchNestedAuthor',
    ];

    private UrlGeneratorInterface $router;
    private ObjectNormalizer $normalizer;
    private Manager $manager;
    private ContributionSearch $contributionSearch;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        Manager $manager,
        ContributionSearch $contributionSearch
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->manager = $manager;
        $this->contributionSearch = $contributionSearch;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /** @var $object Organization */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Organization;
    }

}
