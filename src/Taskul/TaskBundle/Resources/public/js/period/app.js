(function () {
   "use strict";
    angular.module('myApp', ['myApp.services', 'myApp.directives', 'myApp.controllers'], ['$routeProvider','$locationProvider', function($routeProvider,$locationProvider){
        var routePeriodsIndex = decodeURIComponent(Routing.generate($('html').attr('lang')+'__RG__api_index_task_period', { idTask: ':idtask' }));
        $routeProvider.when(routePeriodsIndex, {templateUrl: 'period-list', controller: 'PhoneListCrtl'})
        .when(routePeriodsIndex+'/add', {templateUrl: 'view2', controller: 'MyCtrl3'});
        $locationProvider.html5Mode(true);
    }]);
}());