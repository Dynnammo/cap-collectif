// @flow
import * as React from 'react';
import styled, { type StyledComponent } from 'styled-components';
import { FormattedMessage, useIntl } from 'react-intl';
import { Button, Modal } from 'react-bootstrap';
import { connect } from 'react-redux';
import { useDisclosure } from '@liinkiing/react-hooks';
import CloseButton from '../Form/CloseButton';
import Cookie from './Cookie';
import CookieMonster from '../../CookieMonster';
import type { State } from '../../types';

type Props = {
  analyticsJs: ?string,
  adJs: ?string,
  separator?: string,
  cookieTrad?: ?string,
  isLink: boolean,
};

export const LinkSeparator: StyledComponent<{}, {}, HTMLSpanElement> = styled.span`
  padding: 0 8px;
  @media (max-width: 767px) {
    display: none;
  }
`;

export const CookieBanner: StyledComponent<{}, {}, HTMLDivElement> = styled.div`
  .cookie-banner.active {
    align-items: center;
    display: flex;
    justify-content: space-between;
  }
  .cookie-text {
    padding: 0;
  }
  @media (max-width: 479px) {
    flex-direction: column;
    padding: 16px;
    .cookie-button {
      margin-top: 8px;
      width: 100%;
    }
    #cookie-consent {
      padding: 4px 8px;
    }
  }
  @media (min-width: 480px) and (max-width: 1080px) {
    padding: 32px;
    flex-direction: column;
    .cookie-button {
      margin-top: 24px;
      width: 100%;
    }
  }
  @media (min-width: 1080px) {
    padding: 32px 37px;
    .cookie-button {
      width: 100%;
      min-width: 380px;
      max-width: 400px;
      text-align: right;
      padding-right: 0;
      padding-left: 0;
    }
  }
`;

export const CookieManagerModal = ({
  isLink = false,
  analyticsJs,
  adJs,
  cookieTrad,
  separator,
}: Props) => {
  const intl = useIntl();
  const { isOpen, onOpen, onClose } = useDisclosure(false);
  const cookie = React.useRef(null);

  const changeShowModalState = React.useCallback(() => {
    const hash = window.location.href.split('#');

    if (
      Array.isArray(hash) &&
      hash[1] !== null &&
      typeof hash[1] !== 'undefined' &&
      hash[1] === 'cookiesManagement' &&
      isOpen === false
    ) {
      onOpen();
      const noHashURL = window.location.href.replace(/#.*$/, '');
      window.history.replaceState('', document.title, noHashURL);
    }
  }, [isOpen, onOpen]);

  React.useEffect(() => {
    if (isLink) window.addEventListener('hashchange', changeShowModalState, false);

    return () => {
      if (isLink) window.removeEventListener('hashchange', changeShowModalState, false);
    };
  }, [changeShowModalState, isLink]);

  const saveCookie = () => {
    if (cookie && cookie.current) cookie.current.saveCookiesConfiguration();
    onClose();
  };

  return (
    <div className="cookie-manager">
      {isLink ? (
        <div>
          {separator && <LinkSeparator>{separator} </LinkSeparator>}
          <Button
            variant="link"
            bsStyle="link"
            className="p-0"
            id="cookies-management"
            onClick={onOpen}>
            <FormattedMessage id={cookieTrad || 'cookies-management'} />
          </Button>
        </div>
      ) : (
        <CookieBanner id="cookie-banner" className="cookie-banner">
          <div className="cookie-text">
            <FormattedMessage id="cookies-text" />
          </div>
          <div className="text-center cookie-button">
            <Button
              id="cookie-more-button"
              className="mr-10"
              variant="link"
              bsStyle="link"
              onClick={() => {
                CookieMonster.doNotConsiderFullConsent(true);
              }}
              name="cookie-refused">
              <FormattedMessage id="refused-all-cookies" />
            </Button>
            <Button
              id="cookie-consent"
              bsStyle="default"
              className="btn btn-default btn-sm"
              onClick={() => {
                CookieMonster.considerFullConsent();
              }}>
              <FormattedMessage id="accept-everything" />
            </Button>
          </div>
        </CookieBanner>
      )}
      <div>
        <Modal
          animation={false}
          show={isOpen}
          onHide={onClose}
          bsSize="large"
          id="cookies-modal"
          className="cookie-manager"
          aria-labelledby="contained-modal-title-lg">
          <Modal.Header
            closeButton
            closeLabel={intl.formatMessage({ id: 'close.modal' })}
            className="cookie-manager">
            <Modal.Title id="contained-modal-title-lg" className="cookie-manager">
              <FormattedMessage id="cookies-management" />
            </Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Cookie analyticsJs={analyticsJs} adJs={adJs} ref={cookie} />
          </Modal.Body>
          <Modal.Footer className="cookie-manager">
            <CloseButton buttonId="cookies-cancel" onClose={onClose} />
            <button
              className="ml-15 btn btn-primary"
              id="cookies-save"
              onClick={saveCookie}
              type="submit">
              <FormattedMessage id="global.save" />
            </button>
          </Modal.Footer>
        </Modal>
      </div>
    </div>
  );
};

const mapStateToProps = (state: State) => ({
  analyticsJs: state.default.parameters['snalytical-tracking-scripts-on-all-pages'],
  adJs: state.default.parameters['ad-scripts-on-all-pages'],
});

export default connect<any, any, _, _, _, _>(mapStateToProps)(CookieManagerModal);
