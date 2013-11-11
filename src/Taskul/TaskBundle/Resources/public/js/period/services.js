(function(){
'use strict';

angular.module('myApp.services', ['ngResource'])
  .factory('TaskPeriods', ['$resource', function($resource){

    var routePeriodsList = decodeURIComponent(Routing.generate($('html').attr('lang')+'__RG__api_get_task_periods', { idTask: ':idtask','_format':'json' }));

    return $resource(routePeriodsList,
      {},
      {list: {method:'GET', params:{idtask:''}, transformResponse: function (data) {return angular.fromJson(data).periods; }, isArray:false}}
    );

  }]);

})();