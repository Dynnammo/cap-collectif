// @flow
/* eslint-env jest */
import React from 'react';
import { shallow } from 'enzyme';
import { OpinionCreateForm } from './OpinionCreateForm';
import IntlData from '../../../translations/FR';

describe('<OpinionCreateForm />', () => {
  const props = {
    handleSubmit: jest.fn(),
    projectId: '1',
    stepId: 1,
    step: {
      titleHelpText: null,
      descriptionHelpText: null,
    },
    opinionType: {
      appendixTypes: [
        {
          type: '1',
          id: '1',
          title: 'appendice',
        },
      ],
    },
    ...IntlData,
  };

  it('renders correctly', () => {
    const wrapper = shallow(<OpinionCreateForm {...props} />);
    expect(wrapper).toMatchSnapshot();
  });
});
