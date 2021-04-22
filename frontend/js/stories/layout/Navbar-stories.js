/* @flow */
import * as React from 'react';
import { ThemeProvider } from 'styled-components';
import { storiesOf } from '@storybook/react';
import { boolean, text } from 'storybook-addon-knobs';
import Navbar from '../../components/Navbar/Navbar';
import { items, itemWithChildren } from '../mocks/navbarItems';
import { author as userMock } from '../mocks/users';
import { features as defaultFeatures } from '../../redux/modules/default';
import { NavbarRight } from '~/components/Navbar/NavbarRight';

storiesOf('Layout/MainNavbar', module)
  .add('with 2 items', () => {
    const siteName = text('site name', 'Cap-Collectif');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://cap-collectif.com/uploads/2016/03/logo-complet-site.png',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#ffffff', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#00ACC1', 'Theme'),
      mainNavbarText: text('Navbar item color', '#000000', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#ffffff', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#ffffff', 'Theme'),
    };

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={[items[0], items[1]]}
          siteName={siteName}
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
          currentRouteName="app_homepage"
          currentRouteParams={[]}
          preferredLanguage="fr-FR"
          languageList={[]}
          currentLanguage="fr-FR"
          isMultilangueEnabled
        />
      </ThemeProvider>
    );
  })
  .add('with many items', () => {
    const siteName = text('site name', 'Cap-Collectif');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://cap-collectif.com/uploads/2016/03/logo-complet-site.png',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#ffffff', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#00ACC1', 'Theme'),
      mainNavbarText: text('Navbar item color', '#000000', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#ffffff', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#ffffff', 'Theme'),
    };

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={items}
          currentRouteName="app_homepage"
          currentRouteParams={[]}
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
          siteName={siteName}
          preferredLanguage="en-GB"
          currentLanguage="fr-FR"
          languageList={[]}
          isMultilangueEnabled
        />
      </ThemeProvider>
    );
  })
  .add('with a submenu', () => {
    const siteName = text('site name', 'Cap-Collectif');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://cap-collectif.com/uploads/2016/03/logo-complet-site.png',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#ffffff', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#00ACC1', 'Theme'),
      mainNavbarText: text('Navbar item color', '#000000', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#ffffff', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#ffffff', 'Theme'),
    };

    const newItems = items.slice(0);
    newItems.splice(5, 0, itemWithChildren);

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={newItems}
          siteName={siteName}
          currentRouteName="app_homepage"
          isMultilangueEnabled
          currentLanguage="es-ES"
          currentRouteParams={[]}
          preferredLanguage="fr-FR"
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
          languageList={[]}
        />
      </ThemeProvider>
    );
  })
  .add('not logged', () => {
    const withSearch = boolean('with search', true);
    const siteName = text('site name', 'Cap-Collectif');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://cap-collectif.com/uploads/2016/03/logo-complet-site.png',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#ffffff', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#00ACC1', 'Theme'),
      mainNavbarText: text('Navbar item color', '#000000', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#ffffff', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#ffffff', 'Theme'),
    };

    const contentRight = (
      <NavbarRight
        user={null}
        features={{ ...defaultFeatures, search: withSearch }}
        instanceName="Cap collectif"
        currentLanguage="fr-fr"
        loginWithOpenId={false}
      />
    );

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={items}
          siteName={siteName}
          contentRight={contentRight}
          currentRouteName="app_homepage"
          currentRouteParams={[]}
          preferredLanguage="fr-FR"
          currentLanguage="fr-FR"
          isMultilangueEnabled
          languageList={[]}
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
        />
      </ThemeProvider>
    );
  })
  .add('logged', () => {
    const withSearch = boolean('with search', true, 'Config');
    const siteName = text('site name', 'Cap-Collectif', 'Config');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://cap-collectif.com/uploads/2016/03/logo-complet-site.png',
      'Config',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#ffffff', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#00ACC1', 'Theme'),
      mainNavbarText: text('Navbar item color', '#000000', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#ffffff', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#ffffff', 'Theme'),
    };

    const contentRight = (
      <NavbarRight
        user={userMock}
        features={{ ...defaultFeatures, search: withSearch, profiles: true }}
        instanceName="Cap collectif"
        currentLanguage="fr-fr"
        loginWithOpenId={false}
      />
    );

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={items}
          siteName={siteName}
          contentRight={contentRight}
          currentRouteName="app_homepage"
          currentRouteParams={[]}
          preferredLanguage="fr-FR"
          currentLanguage="en-GB"
          isMultilangueEnabled
          languageList={[]}
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
        />
      </ThemeProvider>
    );
  })
  .add('with custom theme', () => {
    const withSearch = boolean('with search', true, 'Config');
    const siteName = text('site name', 'Cap-Collectif', 'Config');
    const home = 'https://cap-collectif.com/';
    const logo = text(
      'logo url',
      'https://dialoguecitoyen.metropole.nantes.fr/media/cache/default_logo/default/0001/01/6c22377e08184457559a5f0b385556a0380c6297.png',
      'Config',
    );
    const theme = {
      mainNavbarBg: text('Navbar background', '#2293c7', 'Theme'),
      mainNavbarBgActive: text('Navbar active item background', '#ffffff', 'Theme'),
      mainNavbarText: text('Navbar item color', '#ffffff', 'Theme'),
      mainNavbarTextHover: text('Navbar item hover color', '#2293c7', 'Theme'),
      mainNavbarTextActive: text('Navbar item active color', '#2293c7', 'Theme'),
      mainNavbarBgMenu: text('Navbar menu background', '#2293c7', 'Theme'),
    };

    const contentRight = (
      <NavbarRight
        user={userMock}
        features={{ ...defaultFeatures, search: withSearch, profiles: true }}
        instanceName="Cap collectif"
        currentLanguage="fr-fr"
        loginWithOpenId={false}
      />
    );

    return (
      <ThemeProvider theme={theme}>
        <Navbar
          home={home}
          logo={logo}
          items={items}
          siteName={siteName}
          contentRight={contentRight}
          currentRouteName="app_homepage"
          currentRouteParams={[]}
          preferredLanguage="fr-FR"
          currentLanguage="fr-FR"
          isMultilangueEnabled={false}
          localeChoiceTranslations={[
            { code: 'de-DE', message: 'Ich bin französe', label: 'Weiter' },
          ]}
          languageList={[
            { translationKey: 'french', code: 'fr-FR' },
            { translationKey: 'english', code: 'en-GB' },
          ]}
        />
      </ThemeProvider>
    );
  });
