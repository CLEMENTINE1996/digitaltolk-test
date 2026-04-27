import api from './api'

export default {
  login: function(credentials) {
    return api.create('/api/login', credentials)
  },
  logout: function() {
    localStorage.clear()
  }
}