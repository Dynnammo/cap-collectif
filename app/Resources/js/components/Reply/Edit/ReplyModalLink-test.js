// @flow
/* eslint-env jest */
import React from 'react';
import { shallow } from 'enzyme';
import { ReplyModalLink } from './ReplyModalLink';
import { $refType, $fragmentRefs } from '../../../mocks';

describe('<ReplyModalLink />', () => {
  const reply = {
    $refType,
    createdAt: '',
    publishedAt: '',
    id: 'replay1',
    private: false,
    draft: false,
    viewerCanUpdate: true,
    viewerCanDelete: true,
    $fragmentRefs,
  };
  const notContribuableReply = {
    ...reply,
    viewerCanUpdate: false,
    viewerCanDelete: false,
  };

  it('render a reply in a contribuable questionnaire', () => {
    const wrapper = shallow(<ReplyModalLink reply={reply} />);
    expect(wrapper).toMatchSnapshot();
  });

  it('render a draft reply in a contribuable questionnaire', () => {
    const wrapper = shallow(<ReplyModalLink reply={{ ...reply, draft: true }} />);
    expect(wrapper).toMatchSnapshot();
  });
  it('render a private reply in a contribuable questionnaire', () => {
    const wrapper = shallow(<ReplyModalLink reply={{ ...reply, private: true }} />);
    expect(wrapper).toMatchSnapshot();
  });

  it('render a reply in a closed questionnaire', () => {
    const wrapper = shallow(<ReplyModalLink reply={notContribuableReply} />);
    expect(wrapper).toMatchSnapshot();
  });

  it('render a draft reply in a closed questionnaire', () => {
    const wrapper = shallow(<ReplyModalLink reply={{ ...notContribuableReply, draft: true }} />);
    expect(wrapper).toMatchSnapshot();
  });
});
