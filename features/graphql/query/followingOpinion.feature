@opinion_follow @opinion_follow_graphql
Feature: Opinions

@database
Scenario: GraphQL client wants to get list of users who following an opinion
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query getFollowers ($opinionId: ID!,$count: Int, $cursor: String){
      opinion: node(id: $opinionId) {
        ... on Opinion {
          followers(first: $count, after: $cursor) {
            edges {
              cursor
              node {
                id
              }
            }
          }
        }
      }
    }",
    "variables": {
      "opinionId": "opinion6",
      "count": 2,
      "cursor": null
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "opinion": {
        "followers": {
          "edges": [
            {
              "cursor": @string@,
              "node": {
                "id": "user10"
              }
            },{
              "cursor": @string@,
              "node": {
                "id": "user11"
              }
            }
          ]
        }
      }
    }
  }
  """

@database
Scenario: GraphQL client wants to get list of opinions followed by the current user
  Given I am logged in to graphql as user
  And I send a GraphQL POST request:
  """
  {
    "query": "query getFollowingOpinion($count: Int, $cursor: String) {
      viewer {
        followingOpinions(first: $count, after: $cursor) {
          edges {
            cursor
            node {
              id
            }
          }
        }
      }
    }",
    "variables": {
      "count": 5,
      "cursor": null
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "viewer": {
        "followingProposals": {
          "edges": [
            {
              "cursor": @string@,
              "node": {
                "id": "opinion6"
              }
            },
            {
              "cursor": @string@,
              "node": {
                "id": "opinion7"
              }
            }
          ]
        }
      }
    }
  }
  """

@database
Scenario: I'm on a opinion and GraphQL want to know the total number of opinion's followers
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query ($opinionId: ID!, $count: Int, $cursor: String) {
      opinion: node(id: $opinionId) {
        id
        ... on Opinion {
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
      "opinionId": "opinion6",
      "count": 5,
      "cursor": null
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "opinion": {
        "id": "opinion6",
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
          "totalCount": 37
        }
      }
    }
  }
  """

@database
Scenario: I'm on qqa opinion and I want to load 20 followers from a cursor
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
  """
  {
    "query": "query ($opinionId: ID!, $count: Int, $cursor: String) {
      opinion: node(id: $opinionId) {
        id
        ... on Opinion {
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
      "opinionId": "opinion6",
      "count": 20,
      "cursor": "YXJyYXljb25uZWN0aW9uOjMa"
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "opinion": {
        "id": "opinion6",
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
          "totalCount": 37
        }
      }
    }
  }
"""
