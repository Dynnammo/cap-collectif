// @flow
import React from 'react';
import { graphql, createFragmentContainer } from 'react-relay';
import styled, { type StyledComponent } from 'styled-components';
import { connect } from 'react-redux';
import UserBlockProfile from '../../Ui/BackOffice/UserBlockProfile';
import EarthIcon from '../../Ui/Icons/EarthIcon';
import type { AdminRightNavbar_query } from '~relay/AdminRightNavbar_query.graphql';
import type { FeatureToggles, GlobalState } from '~/types';
import colors from '../../../utils/colors';
import CookieMonster from '~/CookieMonster';
import Menu from '~ds//Menu/Menu';
import Button from '~ds/Button/Button';
import Icon, { ICON_NAME, ICON_SIZE } from '~ds/Icon/Icon';

export type Props = {|
  query: AdminRightNavbar_query,
  features: FeatureToggles,
  localesData: Array<{| locale: string, path: string |}>,
  currentLocale: string,
|};

const Navbar: StyledComponent<{}, {}, HTMLUListElement> = styled.ul`
  right: 0;
  position: absolute;
  list-style: none;

  #admin-beamer-navbar + ul {
    display: none;
  }
`;

const CustomNavbarItem: StyledComponent<{}, {}, typeof Button> = styled(Button)`
  position: relative;
  float: left;
  height: 56px;
  width: 55px;
  border-left: 1px solid ${colors.borderColor};
  padding: 8px;
`;

const Placeholder: StyledComponent<{}, {}, HTMLDivElement> = styled.div`
  width: 24px;
`;

const AdminRightNavbar = ({ localesData, currentLocale, features, query }: Props) => (
  <Navbar>
    <CustomNavbarItem id="admin-beamer-navbar">
      <div
        className="dropdown-toggle js-notifications-trigger beamerTrigger ml-5"
        data-toggle="dropdown">
        <i className="fa fa-bell fa-fw" aria-hidden="true" />
      </div>
    </CustomNavbarItem>
    {features.multilangue && (
      <Menu placement="bottom-start" as="li">
        <Menu.Button>
          <CustomNavbarItem
            id="admin-multilangue-dropdown-navbar"
            rightIcon={
              <Icon name={ICON_NAME.ARROW_DOWN_O} size={ICON_SIZE.SM} color={colors.black} />
            }
            variant="tertiary"
            variantSize="small"
            variantColor="hierarchy">
            <EarthIcon color={colors.black} />
          </CustomNavbarItem>
        </Menu.Button>
        <Menu.List id="admin-multilangue-dropdown">
          {localesData &&
            localesData.map(localeData => (
              <Menu.ListItem
                style={{
                  color: colors.black,
                  paddingLeft: '10px',
                  textDecoration: 'none',
                }}
                as="a"
                href={localeData.path}
                key={localeData.locale}
                onClick={() => {
                  CookieMonster.setLocale(localeData.locale);
                }}>
                {localeData.locale === currentLocale ? (
                  <i className="cap-android-done mr-10" />
                ) : (
                  <Placeholder />
                )}
                <span style={{ marginLeft: '5px' }}>{localeData.locale}</span>
              </Menu.ListItem>
            ))}
        </Menu.List>
      </Menu>
    )}
    <Menu placement="bottom-start" as="li">
      <Menu.Button id="admin-profile-dropdown-navbar">
        <CustomNavbarItem
          rightIcon={
            <Icon name={ICON_NAME.ARROW_DOWN_O} size={ICON_SIZE.SM} color={colors.black} />
          }
          variant="tertiary"
          variantSize="small"
          variantColor="hierarchy">
          <i className="fa fa-user fa-fw" aria-hidden="true" style={{ color: colors.black }} />
        </CustomNavbarItem>
      </Menu.Button>
      <Menu.List>
        <UserBlockProfile query={query} />
      </Menu.List>
    </Menu>
  </Navbar>
);

const mapStateToProps = (state: GlobalState) => ({
  features: state.default.features,
});

export default createFragmentContainer(
  connect<any, any, _, _, _, _>(mapStateToProps)(AdminRightNavbar),
  {
    query: graphql`
      fragment AdminRightNavbar_query on Query {
        ...UserBlockProfile_query
      }
    `,
  },
);
