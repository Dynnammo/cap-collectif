import LoginActions from '../actions/LoginActions';
import LocalStorageService from './LocalStorageService.js';

function status(response) {
  if (response.status >= 200 && response.status < 300) {
    return response;
  }
  throw new Error(response.statusText);
}

function json(response) {
  return response.json();
}

class AuthService {

  login() {
    if (LocalStorageService.isValid('jwt') && LocalStorageService.isValid('user')) {
      LoginActions.cachedLoginUser();
      return Promise.resolve();
    }
    return fetch(window.location.protocol + '//' + window.location.host + '/get_api_token', {
      method: 'get',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    })
    .then(status)
    .then(json)
    .then((data) => {
      LoginActions.loginUser(data.token, data.user);
    })
    .catch(() => {
      LoginActions.logoutUser();
    });
  }

  logout() {
    LoginActions.logoutUser();
  }

}

export default new AuthService();
