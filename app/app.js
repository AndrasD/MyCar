var app = angular.module('myApp', ['ui.router', 'lbServices', 'ngAnimate', 'toaster', 'ngMap']);

app
.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
    $stateProvider
        .stat('login', {
            url: '/login',
            templateUrl: 'app/component/authenticate/login.view.html',
            controller: 'loginController',
            authenticate: true
        })
        .state('logout', {
            url: '/logout',
            controller: 'logoutController'
        })
        .state('customer', {
            url: '/customer/:id',
            templateUrl: 'app/component/customer/customer.view.html',
            controller: 'customerController',
            authenticate: true
        })
        .state('customers', {
            url: '/customers',
            templateUrl: 'app/component/customer/customers.view.html',
            controller: 'customerController',
            authenticate: true        
        })
        .state('dashboard', {
            url: '/dashboard',
            templateUrl: 'app/component/dashboard/dashboard.view.html',
            controller: 'dashboardController',
            authenticate: true
    });
    $urlRouterProvider.otherwise('login');
}])
.run(['$rootScope', '$state', 'LoopBackAuth', 'AuthService', function ($rootScope, $state, LoopBackAuth, AuthService) {

    $rootScope.$on("$stateChangeStart", function (event, toState, toParams) {
        // redirect to login page if not logged in
        if (toState.authenticate && !LoopBackAuth.accessTokenId) {
            event.preventDefault(); //prevent current page from loading

            // Maintain returnTo state in $rootScope that is used
            // by authService.login to redirect to after successful login.
            $rootScope.returnTo = {
                state: toState,
                params: toParams
            };

            $state.go('login');
        }
    });

    // Get data from localstorage after pagerefresh
    // and load user data into rootscope.
    if (LoopBackAuth.accessTokenId && !$rootScope.currentUser) {
        AuthService.refresh(LoopBackAuth.accessTokenId);
    }
    
}]);
