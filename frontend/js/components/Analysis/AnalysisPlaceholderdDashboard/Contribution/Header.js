// @flow
import * as React from 'react';
import { FormattedMessage } from 'react-intl';
import Collapsable from '~ui/Collapsable';

const Header = () => (
  <>
    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="admin.fields.proposal.map.zone" />
      </Collapsable.Button>
    </Collapsable>

    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="admin.fields.proposal.theme" />
      </Collapsable.Button>
    </Collapsable>

    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="admin.fields.proposal.category" />
      </Collapsable.Button>
    </Collapsable>

    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="admin.fields.proposal.status" />
      </Collapsable.Button>
    </Collapsable>

    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="admin.label.step" />
      </Collapsable.Button>
    </Collapsable>

    <Collapsable align="right">
      <Collapsable.Button>
        <FormattedMessage id="argument.sort.label" />
      </Collapsable.Button>
    </Collapsable>
  </>
);

export default Header;
