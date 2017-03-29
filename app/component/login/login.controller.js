app.controller('loginController', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};

    $scope.doLogin = function (customer) {
        var credential = {
            user: customer.email, 
            password: customer.password, 
            token:''
        };

        Data.post('login', {customer: customer}, {credential: credential}).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                credential.token = results.token;
                $rootScope.setActUser(results, credential); 
                $location.path('dashboard');
            }
        });
    };
});
