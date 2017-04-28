app
    .controller('loginController', function ($scope, AuthService, $state) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {
      email: 'foo@bar.com',
      password: 'foobar'
    };

    $scope.doLogin = function () {
      AuthService.login($scope.login.email, $scope.login.password).then( function () {
         // return to saved returnTo state before redirection to login
            if ($scope.returnTo && $scope.returnTo.state) {
                $state.go(
                    $scope.returnTo.state.name,
                    $scope.returnTo.params
                );
                // maintain the inherited rootscope variable returnTo
                // but make the returnTo state of it null,
                // so it can be used again after a new login.
                $scope.returnTo.state = null;
                $scope.returnTo.params = null;
                return;
            }
         // or go to the default state after login
            $state.go('add-review');
        });
    };
});
