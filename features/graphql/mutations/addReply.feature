@changeProposalEvaluation
Feature: Proposal Evaluation

@database
Scenario: User wants to add a reply
  Given I am logged in to graphql as admin
  And I send a GraphQL POST request:
   """
   {
    "query": "mutation ($input: AddReplyInput!) {
      addReply(input: $input) {
        reply {
          id
          responses {
            question {
              id
            }
            ... on ValueResponse {
              value
            }
          }
        }
      }
    }",
    "variables": {
      "input": {
        "questionnaireId": 1,
        "responses": [
            {
              "question": "2",
              "value": "Je pense que c'est la ville parfaite pour organiser les JO"
            },
            {
              "question": "13",
              "value": "{\"labels\":[\"Athlétisme\",\"Sports collectifs\"],\"other\":\"Embêter Maxime\"}"
            }
        ]
      }
    }
  }
  """
  Then the JSON response should match:
  """
  {
    "data": {
      "addReply": {
          "reply": {
              "id": @uuid@,
              "responses": [
                {"question": {"id":"2" }, "value": "Je pense que c\u0027est la ville parfaite pour organiser les JO"},
                {"question": {"id":"13"}, "value": "{\"labels\":[\"Athl\u00e9tisme\",\"Sports collectifs\"],\"other\":\"Emb\u00eater Maxime\"}"},
                {"question": {"id":"14"}, "value": "\"{\\\"labels\\\":[],\\\"other\\\":null}\""},
                {"question": {"id":"15"}, "value": "\"{\\\"labels\\\":[],\\\"other\\\":null}\""},
                {"question": {"id":"16"}, "value": "\"{\\\"labels\\\":[],\\\"other\\\":null}\""},
                {"question": {"id":"18"}, "value": "\"{\\\"labels\\\":[],\\\"other\\\":null}\""},
                {"question": {"id":"19"}, "value": "\"{\\\"labels\\\":[],\\\"other\\\":null}\""}
              ]
          }
       }
     }
  }
  """
