// @flow
import * as React from 'react';
import { Row } from 'react-bootstrap';
import { graphql, QueryRenderer } from 'react-relay';
import { connect } from 'react-redux';
import Loader from '~ui/FeedbacksIndicators/Loader';
import environment, { graphqlError } from '~/createRelayEnvironment';
import type {
  EventListPageQueryResponse,
  EventListPageQueryVariables,
} from '~relay/EventListPageQuery.graphql';
import config from '~/config';
import EventListPageContainer, { getInitialValues } from './EventListPageContainer';
import EventListPageHeader from './EventListPageHeader';
import withColors from '../Utils/withColors';
import type { GlobalState } from '~/types';
import { TranslationLocaleEnum } from '~/utils/enums/TranslationLocale';

type Props = {|
  +eventPageTitle: ?string,
  +eventPageBody: ?string,
  +backgroundColor: ?string,
  +isAuthenticated: boolean,
  +locale: string,
|};

export const EventListPage = ({
  backgroundColor,
  isAuthenticated,
  locale,
  eventPageTitle,
  eventPageBody,
}: Props) => {
  const initialValues = getInitialValues();
  const { project } = initialValues;
  const isFuture =
    initialValues.status === 'all' ? null : initialValues.status === 'ongoing-and-future';

  const urlSearch = new URLSearchParams(window.location.search);
  const theme = urlSearch.get('theme') ?? null;

  return (
    <div className="event-page">
      <QueryRenderer
        environment={environment}
        query={graphql`
          query EventListPageQuery(
            $cursor: String
            $count: Int!
            $locale: TranslationLocale
            $search: String
            $theme: ID
            $project: ID
            $userType: ID
            $isFuture: Boolean
            $author: ID
            $isRegistrable: Boolean
            $orderBy: EventOrder!
            $isAuthenticated: Boolean!
          ) {
            ...EventListPageContainer_query
              @arguments(
                cursor: $cursor
                count: $count
                locale: $locale
                search: $search
                theme: $theme
                project: $project
                userType: $userType
                author: $author
                isRegistrable: $isRegistrable
                isFuture: $isFuture
                orderBy: $orderBy
                isAuthenticated: $isAuthenticated
              )
            ...EventListPageHeader_queryViewer @arguments(isAuthenticated: $isAuthenticated)
          }
        `}
        variables={
          ({
            count: config.isMobile ? 25 : 50,
            cursor: null,
            search: null,
            theme,
            userType: null,
            project,
            isFuture,
            locale: TranslationLocaleEnum[locale],
            author: null,
            isRegistrable: null,
            orderBy: { field: 'START_AT', direction: 'ASC' },
            isAuthenticated,
          }: EventListPageQueryVariables)
        }
        render={({
          error,
          props,
        }: {
          ...ReactRelayReadyState,
          props: ?EventListPageQueryResponse,
        }) => {
          if (error) {
            return graphqlError;
          }
          if (props) {
            return (
              <div>
                <section className="jumbotron--bg-1 ">
                  <EventListPageHeader eventPageTitle={eventPageTitle} queryViewer={props} />
                </section>
                <section className="section--alt">
                  <EventListPageContainer
                    query={props}
                    eventPageBody={eventPageBody}
                    backgroundColor={backgroundColor}
                  />
                </section>
              </div>
            );
          }
          return (
            <Row>
              <Loader />
            </Row>
          );
        }}
      />
    </div>
  );
};

const mapStateToProps = (state: GlobalState) => ({
  isAuthenticated: !!state.user.user,
});
const container = connect<any, any, _, _, _, _>(mapStateToProps)(EventListPage);

export default withColors(container);
