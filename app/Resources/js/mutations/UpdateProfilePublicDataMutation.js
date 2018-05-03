// @flow
import { graphql } from 'react-relay';
import environment from '../createRelayEnvironment';
import commitMutation from './commitMutation';
import type {
  UpdateProfilePublicDataMutationVariables,
  UpdateProfilePublicDataMutationResponse as Response,
} from './__generated__/UpdateProfilePublicDataMutation.graphql';

export type UpdateProfilePublicDataMutationResponse = Response;

const mutation = graphql`
  mutation UpdateProfilePublicDataMutation($input: UpdateProfilePublicDataInput!) {
    updateProfilePublicData(input: $input) {
      viewer {
        id
        media {
          id
          name
          size
          url
        }
        show_url
        username
        biography
        website
        facebookUrl
        linkedInUrl
        twitterUrl
        profilePageIndexed
        userType {
          id
        }
        neighborhood
      }
    }
  }
`;

const commit = (variables: UpdateProfilePublicDataMutationVariables): Promise<Response> =>
  commitMutation(environment, {
    mutation,
    variables,
  });

export default { commit };
