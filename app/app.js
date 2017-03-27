var app = angular.module('myApp', ['ngRoute', 'ngAnimate', 'toaster', 'ngMap']);

app.config(['$routeProvider',
  function ($routeProvider) {
        $routeProvider.
        when('/login', {
            title: 'Login',
            templateUrl: 'app/component/login/login.view.html',
            controller: 'loginController'
        })
            .when('/logout', {
                title: 'Logout',
                templateUrl: 'app/component/login/login.view.html',
                controller: 'logoutCtrl'
            })
            .when('/customer', {
                title: 'Customer',
                templateUrl: 'app/component/customer/customer.view.html',
                controller: 'customerController'
            })
            .when('/customers', {
                title: 'Customers',
                templateUrl: 'app/component/customer/customers.view.html',
                controller: 'customerController'
            })
            .when('/dashboard', {
                title: 'Dashboard',
                templateUrl: 'app/component/dashboard/dashboard.view.html',
                controller: 'dashboardController'
            })
            .when('/', {
                title: 'Login',
                templateUrl: 'app/component/login/login.view.html',
                controller: 'loginController',
                role: '0'
            })
            .otherwise({
                redirectTo: '/login'
            });
  }])
    .run(function ($rootScope, $location, Data) {

        $rootScope.logout = function () {
            Data.get('logout').then(function (results) {
                Data.toast(results);
                $location.path('login');
            });
        }

        $rootScope.dashboard = function() {
             $location.path('dashboard');
        }

        $rootScope.editCustomers = function() {
             $location.path('customers');
        }

        $rootScope.setActUser = function(data) {
            $rootScope.actUser = {
                authenticated: true, 
                id: data.id, 
                name: data.name, 
                email: data.email,
                admin: data.admin 
            };           
// local storage-ba menteni                 
        }

        $rootScope.$on("$routeChangeStart", function (event, next, current) {
            $rootScope.authenticated = false;
            $rootScope.admin = false;
// local storage-ból kivenni            
            Data.get('session').then(function (results) {
                if (results.id) {
                    $rootScope.authenticated = true;
                    $rootScope.id = results.id;
                    $rootScope.name = results.name;
                    $rootScope.email = results.email;
                    $rootScope.admin = results.admin;
                } else {
                    var nextUrl = next.$$route.originalPath;
                    if (nextUrl == '/customers' || nextUrl == '/login' || nextUrl == '/customer') {

                    } else {
                        $location.path("/login");
                    }
                }
            });
        });
    });
