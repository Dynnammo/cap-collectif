/* eslint-env jest */

const UpdateDebateArgumentMutation = /* GraphQL */ `
  mutation UpdateDebateArgumentMutation($input: UpdateDebateArgumentInput!) {
    updateDebateArgument(input: $input) {
      errorCode
      debateArgument {
        debate {
          id
        }
        author {
          id
        }
        body
        type
      }
    }
  }
`;

describe('Internal|UpdateDebateArgument', () => {
  it('try to update argument with wrong argument id', async () => {
    await expect(
      graphql(
        UpdateDebateArgumentMutation,
        {
          input: {
            id: 'wrongId',
            body: "oups je me suis trompé d'id pour l'argument",
            type: 'FOR',
          },
        },
        'internal_user',
      ),
    ).resolves.toMatchSnapshot();
  });
  it('update argument', async () => {
    await expect(
      graphql(
        UpdateDebateArgumentMutation,
        {
          input: {
            id: toGlobalId('DebateArgument', 'debateArgument2'),
            body: "j'ai changé d'avis",
            type: 'AGAINST',
          },
        },
        'internal_theo',
      ),
    ).resolves.toMatchSnapshot();
  });
});
