app.controller('customerController', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.customersCollection = {};

    $scope.customersCollection = {name: '', email: '', created: '', admin: ''};
    Data.get('customers').then(function (results) {
        $scope.customersCollection = angular.fromJson(results);
    });        

});
