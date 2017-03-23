app.controller('customerController', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.editCustomerId = 0;
    $scope.customersCollection = [{name: '', email: '', created: '', admin: ''}];

    $scope.sortType     = 'name';     // set the default sort type
    $scope.sortReverse  = false;      // set the default sort reverse
    $scope.searchCustomers  = '';     // set the default search/filter term

    Data.get('customers').then(function (results) {
        $scope.customersCollection = angular.fromJson(results);
    });       

    $scope.editCustomer = function (customerId) {
       $scope.editCustomerId = customerId; 
    };

});
