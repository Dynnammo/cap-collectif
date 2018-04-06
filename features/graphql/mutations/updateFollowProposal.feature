@proposal_follow @proposal_follow_graphql
Feature: Update follow Proposals

@database
Scenario: GraphQL client wants to update follow a proposal with current user
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "mutation ($input: UpdateFollowProposalInput!) {
      updateFollowProposal(input: $input) {
        proposal {
          id
          followerConfiguration{
            notifiedOf
          }
        }
      }
    }",
    "variables": {
      "input": {
        "proposalId": "proposal1",
        "notifiedOf": "DEFAULT"
      }
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "updateFollowProposal": {
        "proposal": {
          "id": "proposal1",
          "followerConfiguration":{
            "notifiedOf":"DEFAULT"
          }
        }
      }
    }
  }
  """
