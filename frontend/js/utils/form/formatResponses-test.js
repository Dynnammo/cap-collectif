// @flow
import formatResponses from '~/utils/form/formatResponses';
import { allTypeQuestions, allTypeResponses } from './mocks';

const result = [
  {
    idQuestion: 'UXVlc3Rpb246NDg3',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'select',
    validationRule: null,
    value: 'Paris',
    constraintes: null,
  },
  {
    idQuestion: 'UXVlc3Rpb246NDk4',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'text',
    value: 'Bien',
    constraintes: null,
    validationRule: undefined,
  },
  {
    idQuestion: 'UXVlc3Rpb246NDk3',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'textarea',
    value: 'Blanc',
    constraintes: null,
    validationRule: undefined,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTAw',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'editor',
    value: '<p>Louveteau</p>',
    constraintes: null,
    validationRule: undefined,
  },
  {
    idQuestion: 'UXVlc3Rpb246NDk5',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'number',
    value: '10',
    constraintes: null,
    validationRule: undefined,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTAx',
    otherValue: null,
    required: false,
    hidden: false,
    constraintes: null,
    validationRule: undefined,
    type: 'medias',
    value: [
      {
        id: '3de332be-6d0a-11ea-9d72-0242ac110006',
        name: 'randomImage.jpg',
        size: '17.5 Ko',
        url:
          'https://demo.cap-collectif.com/media/default/0001/01/40dacc5a6c1bbf642d7b41728632cfa3d50f0edb.jpeg',
      },
    ],
  },
  {
    idQuestion: 'UXVlc3Rpb246NTAy',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'button',
    validationRule: null,
    value: 'Je suis vert',
    constraintes: null,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTAz',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'radio',
    validationRule: null,
    value: ['Dark Vador'],
    constraintes: null,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTA0',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'checkbox',
    validationRule: null,
    value: ['Tennis', 'Football'],
    constraintes: null,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTA1',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'ranking',
    validationRule: null,
    value: ['Pomme', 'Poire', 'Fraise'],
    constraintes: null,
  },
  {
    idQuestion: 'UXVlc3Rpb246NTA2',
    otherValue: null,
    required: false,
    hidden: false,
    type: 'majority',
    validationRule: undefined,
    value: '2',
    constraintes: null,
  },
];

describe('formatResponses', () => {
  it('should format correctly', () => {
    const formattedResponses = formatResponses(allTypeQuestions, allTypeResponses);
    expect(formattedResponses).toEqual(result);
  });
});
