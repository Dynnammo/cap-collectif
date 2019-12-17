<?php

namespace Capco\AdminBundle\Resolver;

use Capco\AppBundle\Enum\UserRole;
use Capco\AppBundle\Toggle\Manager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FeaturesCategoryResolver
{
    protected static $categories = [
        'pages.homepage' => ['conditions' => [], 'features' => []],
        'pages.blog' => ['conditions' => ['blog'], 'features' => []],
        'pages.events' => [
            'conditions' => ['calendar'],
            'features' => [
                UserRole::ROLE_SUPER_ADMIN => ['allow_users_to_propose_events'],
                UserRole::ROLE_ADMIN => []
            ]
        ],
        'pages.themes' => ['conditions' => ['themes'], 'features' => []],
        'pages.projects' => ['conditions' => [], 'features' => ['projects_form', 'project_trash']],
        'pages.registration' => [
            'conditions' => [],
            'features' => ['user_type', 'zipcode_at_register']
        ],
        'pages.members' => ['conditions' => ['members_list'], 'features' => []],
        'pages.login' => ['conditions' => [], 'features' => []],
        'pages.footer' => ['conditions' => [], 'features' => []],
        'pages.cookies' => ['conditions' => [], 'features' => []],
        'pages.privacy' => ['conditions' => [], 'features' => []],
        'pages.legal' => ['conditions' => [], 'features' => []],
        'pages.charter' => ['conditions' => [], 'features' => []],
        'pages.shield' => ['conditions' => [], 'features' => ['shield_mode']],
        'settings.global' => ['conditions' => [], 'features' => []],
        'settings.performance' => ['conditions' => [], 'features' => []],
        'settings.modules' => [
            'conditions' => [],
            'features' => [
                UserRole::ROLE_ADMIN => [
                    'blog',
                    'calendar',
                    'consultation_plan',
                    'privacy_policy',
                    'display_map',
                    'versions',
                    'themes',
                    'districts',
                    'members_list',
                    'profiles',
                    'reporting',
                    'newsletter',
                    'share_buttons',
                    'search',
                    'display_pictures_in_depository_proposals_list',
                    'external_project',
                    'read_more',
                    'secure_password',
                    'restrict_connection',
                    'login_franceconnect',
                    'public_api',
                    'developer_documentation'
                ],
                UserRole::ROLE_SUPER_ADMIN => [
                    'disconnect_openid',
                    'votes_evolution',
                    'server_side_rendering',
                    'export',
                    'indexation',
                    'new_feature_questionnaire_result',
                    'display_pictures_in_event_list',
                    'app_news',
                    'unstable__multilangue',
                    'login_openid',
                    'login_saml',
                    'login_paris',
                    'unstable__admin_editor',
                ]
            ]
        ],
        'settings.notifications' => ['conditions' => [], 'features' => []],
        'settings.appearance' => ['conditions' => [], 'features' => []]
    ];

    protected $manager;
    protected $authorizationChecker;

    public function __construct(
        Manager $manager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->manager = $manager;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function isCategoryEnabled(string $category): bool
    {
        if (!isset(self::$categories[$category])) {
            return false;
        }

        return $this->manager->hasOneActive(self::$categories[$category]['conditions']);
    }

    public function isAdminEnabled(/* User */ $admin): bool
    {
        if (method_exists($admin, 'getFeatures')) {
            return $this->manager->hasOneActive($admin->getFeatures());
        }

        return true;
    }

    public function getTogglesByCategory(string $category): array
    {
        $toggles = [];

        // If all the category have the same structure remove those conditions about the category name.
        if (
            isset(self::$categories[$category]) &&
            ('settings.modules' === $category || 'pages.events' === $category)
        ) {
            foreach (self::$categories[$category]['features'] as $access => $features) {
                if ($this->authorizationChecker->isGranted($access)) {
                    foreach ($features as $feature) {
                        $toggles[$feature] = [
                            'active' => $this->manager->isActive($feature),
                            'access' => $access
                        ];
                    }
                }
            }
        } elseif (isset(self::$categories[$category])) {
            foreach (self::$categories[$category]['features'] as $feature) {
                if ($this->authorizationChecker->isGranted(UserRole::ROLE_ADMIN)) {
                    $toggles[$feature] = [
                        'active' => $this->manager->isActive($feature)
                    ];
                }
            }
        }

        return $toggles;
    }

    public function findCategoryForToggle(string $toggle): ?string
    {
        foreach (self::$categories as $name => $category) {
            if (
                ('settings.modules' === $name || 'pages.events' === $name) &&
                \in_array(
                    $toggle,
                    array_merge(
                        $category['features'][UserRole::ROLE_ADMIN],
                        $category['features'][UserRole::ROLE_SUPER_ADMIN]
                    ),
                    true
                )
            ) {
                return $name;
            }
            if (\in_array($toggle, $category['features'], true)) {
                return $name;
            }
        }

        return null;
    }

    public function getEnabledPagesCategories(): array
    {
        $categories = [];
        foreach (self::$categories as $name => $cat) {
            if (
                0 === strrpos($name, 'pages.') &&
                $this->manager->hasOneActive($cat['conditions'])
            ) {
                $categories[] = $name;
            }
        }

        return $categories;
    }

    public function getEnabledSettingsCategories(): array
    {
        $categories = [];
        foreach (self::$categories as $name => $cat) {
            if (
                0 === strrpos($name, 'settings.') &&
                $this->manager->hasOneActive($cat['conditions'])
            ) {
                $categories[] = $name;
            }
        }

        return $categories;
    }

    public function getGroupNameForCategory(string $category): ?string
    {
        if (0 === strrpos($category, 'settings.')) {
            return 'admin.group.parameters';
        }
        if (0 === strrpos($category, 'pages.')) {
            return 'admin.group.pages';
        }

        return null;
    }
}
