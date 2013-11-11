(function(){
'use strict';

angular.module('myApp.controllers', [])
  .controller('PhoneListCrtl', ['$scope', 'TaskPeriods', '$routeParams', function($scope, TaskPeriods, $routeParams) {
    // Instantiate an object to store your scope data in (Best Practices)
    $scope.taskPeriods = TaskPeriods.list({idtask: $routeParams.idtask});
  }])
  .controller('MyCtrl3', [function() {

  }]);
})();