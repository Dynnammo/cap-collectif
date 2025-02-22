// @flow
export const items = [
  {
    id: 12,
    title: 'Accueil',
    link: '/',
    hasEnabledFeature: true,
    children: [],
    active: true,
  },
  {
    id: 5,
    title: 'Actualités',
    link: '/blog',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
  {
    id: 11,
    title: 'Thèmes',
    link: '/themes',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
  {
    id: 4,
    title: 'Évènements',
    link: '/events',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
  {
    id: 10,
    title: 'Projets participatifs',
    link: '/projects',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
  {
    id: 23,
    title: 'Custom page',
    link: '/custom-page',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
  {
    id: 6,
    title: 'Comment ça marche',
    link: '/pages/comment-%C3%A7a-marche',
    hasEnabledFeature: true,
    children: [],
    active: false,
  },
];

export const itemWithChildren = {
  id: 9,
  title: 'À propos',
  link: '',
  hasEnabledFeature: true,
  children: [
    {
      id: 8,
      title: 'Contact',
      link: '/contact',
      hasEnabledFeature: true,
      active: false,
    },
    {
      id: 3,
      title: 'Liste des inscrits',
      link: '/members',
      hasEnabledFeature: true,
      active: false,
    },
    {
      id: 2,
      title: 'Lien externe',
      link: '/www.google.fr',
      hasEnabledFeature: true,
      active: false,
    },
  ],
  active: false,
};
