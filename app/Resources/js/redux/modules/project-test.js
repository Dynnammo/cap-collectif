/* eslint-env jest */

import {
  reducer,
  fetchProjectsSaga,
  PROJECTS_FETCH_SUCCEEDED,
  PROJECTS_FETCH_FAILED,
} from './project';
import { put, select, call } from 'redux-saga/effects';
import Fetcher from '../../services/Fetcher';

describe('Project Reducer', () => {
  it('Should handle PROJECTS_FETCH_SUCCEEDED', () => {
    const initialState = {
      isLoading: false,
      projects: [],
    };
    const result = {
      projects: [
        {},
        {},
      ],
    };
    const newState = reducer(initialState, {
      type: PROJECTS_FETCH_SUCCEEDED,
      project: result,
    });
    expect(newState).toEqual({
      isLoading: false,
      projects: result.projects,
    });
  });
});

describe('Project Sagas', () => {
  it('Should fetchProjects', () => {
    const generator = fetchProjectsSaga();
    const project = {
      project: {
        projects: [],
      },
    };
    expect(generator.next().value).toEqual(select());
    expect(generator.next(project).value).toEqual(call(Fetcher.get, '/projects?'));
    expect(generator.next(project).value).toEqual(
      put({
        type: PROJECTS_FETCH_SUCCEEDED,
        project,
      })
    );

    expect(generator.throw({}).value).toEqual(put({ type: PROJECTS_FETCH_FAILED, error: {} }));
  });
});
