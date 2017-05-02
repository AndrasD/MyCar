app
  .factory('AuthService', ['Reviewer', '$q', '$rootScope', '$state', function(User, $q, $rootScope, $state) {
    
  function login(email, password) {
    return User
      .login({email: email, password: password})
      .$promise
      .then(function(response) {
        $rootScope.currentUser = {
          id: response.user.id,
          name: username,
          tokenId: response.id,
          email: email
        };
      });
  }

  function logout() {
    return User
      .logout()
      .$promise
      .then(function() {
        $rootScope.currentUser = null;
      });
  }

  function refresh(accessTokenId) {
    return User
      .getCurrent(function(userResource) {
        $rootScope.currentUser = {
          id: userResource.id,
          name: userResource.username,
          tokenId: accessTokenId,
          email: userResource.email
        };
      });
  }

  return {
    login: login,
    logout: logout,
    register: register,
    refresh: refresh
  };
  
}]);
