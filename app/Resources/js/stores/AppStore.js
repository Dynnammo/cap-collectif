import { createStore } from 'redux';
import LocalStorageService from '../services/LocalStorageService';

export default function configureStore(props) {
  if (props.user === null) {
    LocalStorageService.remove('jwt');
  }
  return createStore(() => props);
}
