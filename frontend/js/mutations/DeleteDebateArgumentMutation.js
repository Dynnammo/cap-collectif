// @flow
import { graphql } from 'react-relay';
// eslint-disable-next-line import/no-unresolved
import type { RecordSourceSelectorProxy } from 'relay-runtime/store/RelayStoreTypes';
import environment from '../createRelayEnvironment';
import commitMutation from './commitMutation';
import type {
  DeleteDebateArgumentMutationVariables,
  DeleteDebateArgumentMutationResponse,
} from '~relay/DeleteDebateArgumentMutation.graphql';

const mutation = graphql`
  mutation DeleteDebateArgumentMutation($input: DeleteDebateArgumentInput!, $connections: [ID!]!) {
    deleteDebateArgument(input: $input) {
      errorCode
      deletedDebateArgumentId @deleteEdge(connections: $connections)
    }
  }
`;

const commit = (variables: {
  ...DeleteDebateArgumentMutationVariables,
  debateId: string,
}): Promise<DeleteDebateArgumentMutationResponse> =>
  commitMutation(environment, {
    mutation,
    variables,
    updater: (store: RecordSourceSelectorProxy) => {
      const payload = store.getRootField('deleteDebateArgument');
      if (!payload) return;
      const argument = payload.getValue('deletedDebateArgumentId');
      if (!argument || typeof argument !== 'string') return;
      const argumentProxy = store.get(argument);
      if (!argumentProxy) {
        throw new Error('Expected argument to be in the store');
      }
      const debateProxy = store.get(variables.debateId);
      if (!debateProxy) {
        throw new Error('Expected debate to be in the store');
      }

      const allArgumentsProxy = debateProxy.getLinkedRecord('arguments', { first: 0 });
      if (!allArgumentsProxy) return;
      const previousValue = parseInt(allArgumentsProxy.getValue('totalCount'), 10);
      allArgumentsProxy.setValue(previousValue - 1, 'totalCount');
    },
  });

export default { commit };
