@query_users
Feature: Get all users

@read-only
Scenario: GraphQL admin want to get users including superadmin
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
    """
    {
      "query": "{
        users(first: 5, superAdmin: true) {
          totalCount
          edges {
            node {
              _id
            }
          }
        }
      }"
    }
    """
  Then the JSON response should match:
    """
    {
      "data": {
        "users": {
          "totalCount": @integer@,
          "edges": [
            {
              "node": {
                "_id": "user200"
              }
            },
            {
              "node": {
                "_id": "user199"
              }
            },
            {
              "node": {
                "_id": "user198"
              }
            },
            {
              "node": {
                "_id": "user197"
              }
            },
            {
              "node": {
                "_id": "user196"
              }
            }
          ]
        }
      }
    }
    """

@read-only
Scenario: Graphql anonymous want to search user author of event
  Given I send a GraphQL POST request:
  """
  {
      "query": "query UserListFieldQuery($displayName: String, $authorOfEventOnly: Boolean) {
          userSearch(displayName: $displayName, authorsOfEventOnly: $authorOfEventOnly) {
            id
            displayName
          }
      }",
    "variables": {"displayName":"xlac","authorOfEventOnly":true}
  }
  """
  Then the JSON response should match:
  """
  {"data":{"userSearch":[{"id":"VXNlcjp1c2VyMw==","displayName":"xlacot"}]}}
  """

@read-only
Scenario: GraphQL anonymous want to get users's comment votes count
  Given I send a GraphQL POST request:
  """
  {
    "query": "{
      users(first: 5) {
        edges {
          node {
            _id
            commentVotes {
              totalCount
            }
          }
        }
      }
    }"
  }
  """
  Then the JSON response should match:
    """
    {
      "data": {
        "users": {
          "edges": [
            {
              "node": {
                "_id": "adminCapco",
                "commentVotes": {
                  "totalCount": 0
                }
              }
            },
            {
              "node": {
                "_id": "user_drupal",
                "commentVotes": {
                  "totalCount": 0
                }
              }
            },
            {
              "node": {
                "_id": "user10",
                "commentVotes": {
                  "totalCount": 2
                }
              }
            },
            {
              "node": {
                "_id": "user100",
                "commentVotes": {
                  "totalCount": 0
                }
              }
            },
            {
              "node": {
                "_id": "user101",
                "commentVotes": {
                  "totalCount": 0
                }
              }
            }
          ]
        }
      }
    }
    """