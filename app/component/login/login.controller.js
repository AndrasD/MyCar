app.controller('loginController', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};
    $rootScope.actUser = {authenticated:'', admin:'', id:'', name:'', email:'', password:''};

    $scope.doLogin = function (customer) {
        Data.post('login', {customer: customer}).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $rootScope.actUser = {
                    authenticated: true, 
                    admin: results.admin, 
                    id: results.id, 
                    name: results.name, 
                    email: customer.email, 
                    password: customer.password
                };
                $location.path('dashboard');
            }
        });
    };
});
