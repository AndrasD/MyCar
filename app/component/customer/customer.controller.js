app.controller('customerController', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    var defaultSort = 'name';
    var user = {};

    $scope.disableButton = false;
    $scope.customersCollection = [{}];

    $scope.sortType     = defaultSort;     // set the default sort type
    $scope.sortReverse  = false;      // set the default sort reverse
    $scope.searchCustomers  = '';     // set the default search/filter term

    getCustomers();

    function getCustomers(){
        if (angular.isDefined($rootScope.actUser)) {
            user = $rootScope.actUser;
        } else {
            Data.get('session').then(function (results) {
                if (results.id) {
                  $rootScope.setActUser(results); 
                  user = $rootScope.actUser; 
                }
            });
        }

        Data.post('getOwnCustomers', {user: user}).then(function (results) {
            $scope.customersCollection = angular.fromJson(results);
        });       
    }

    $scope.editCustomer = function(customer) {
        customer.editMode = true;
        $scope.disableButton = true;
        $scope.sort = undefined;
    }

    //delete customer
    $scope.deleteCustomer = function(customer) {
        Data.post('delCustomer', {customer: customer}).then(function (results) {
            if (results.id) {
                getCustomers();
            }
            Data.toast(results);
        });       
    }

    //save customer (create & update)
    $scope.saveCustomer = function(customer) {
        if (customer.editMode) {
            customer.editMode = false;
            $scope.disableButton = false;
            Data.post('updCustomer', {customer: customer}).then(function (results) {
                Data.toast(results);
            });       
        } else {
            customer.editMode = false;
            $scope.disableButton = false;
            Data.post('addCustomer', {customer: customer}).then(function (results) {
                if (results.id) {
                    getCustomers();
                }
                Data.toast(results);
            });       
        }
    }

    //cancel
    $scope.cancel = function(customer) {
//        getCustomers();
        customer.editMode = false;
        $scope.disableButton = false;
    }

});
