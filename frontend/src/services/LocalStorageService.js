export const LocalStorageService = {
  getToken: function() {
    return localStorage.getItem('token')
  },
  setToken: function(token) {
    localStorage.setItem('token', token)
  },
  getUser: function() {
    var user = localStorage.getItem('user')
    if (user) user = JSON.parse(user)
    return user
  },
  setUser: function (user) {
    localStorage.setItem('user', user ? JSON.stringify(user) : null)
  },
}
