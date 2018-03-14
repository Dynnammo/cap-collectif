@proposal_follow @proposal_follow_graphql
Feature: Proposals

@database
Scenario: GraphQL client wants to get list of users who following a proposal
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query getFollowers ($proposalId: ID!){
      proposal: node(id: $proposalId) {
        ... on Proposal {
          followers {
            id
          }
        }
      }
    }",
    "variables": {
      "proposalId": "proposal10"
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "proposal": {
        "followers": [
          {
            "id": "user1"
          },
          {
            "id": "user2"
          }
        ]
      }
    }
  }
  """

@database
Scenario: GraphQL client wants to get list of proposals followed by the current user
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query getFollowingProposal {
      viewer {
        followingProposals {
          id
        }
      }
    }"
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "viewer": {
        "followingProposals": [
          {
            "id": "proposal1"
          },
          {
            "id": "proposal2"
          }
        ]
      }
    }
  }
  """

@database
Scenario: I'm on a proposal and GraphQL want to know the total number of proposal's followers
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query ($proposalId: ID!, $count: Int, $cursor: String) {
      proposal: node(id: $proposalId) {
        id
        ... on Proposal {
          followerConnection(first: $count, after: $cursor) {
            edges {
              cursor
              node {
                id
              }
            }
            pageInfo {
              hasNextPage
              endCursor
            }
            totalCount
          }
        }
      }
    }",
    "variables": {
      "proposalId": "proposal1",
      "count": 32,
      "cursor": null
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "proposal": {
        "id": "proposal1",
        "followerConnection": {
          "edges": [
            {
              "cursor": @string@,
              "node": {
                "id": @string@
              }
            },
            @...@
          ],
          "pageInfo": {
            "hasNextPage": true,
            "endCursor": @string@
          },
          "totalCount": 66
        }
      }
    }
  }
  """

@database
Scenario: I'm on qqa proposal and I want to load 32 followers from a cursor
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query ($proposalId: ID!, $count: Int, $cursor: String) {
      proposal: node(id: $proposalId) {
        id
        ... on Proposal {
          followerConnection(first: $count, after: $cursor) {
            edges {
              cursor
              node {
                id
              }
            }
            pageInfo {
              hasNextPage
              endCursor
            }
            totalCount
          }
        }
      }
    }",
    "variables": {
      "proposalId": "proposal1",
      "count": 32,
      "cursor": "YXJyYXljb25uZWN0aW9uOjMx"
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "proposal": {
        "id": "proposal1",
        "followerConnection": {
          "edges": [
            {
              "cursor": @string@,
              "node": {
                "id": @string@
              }
            },
            @...@
          ],
          "pageInfo": {
            "hasNextPage": true,
            "endCursor": @string@
          },
          "totalCount": 66
        }
      }
    }
  }
  """
