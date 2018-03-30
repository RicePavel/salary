

myApp.controller('timetableController', function($scope, $http) {
    $scope.test = "123";
    
    $scope.changeDate = function() {
        
    };
    
    $scope.workers = [];
    $scope.timetableWorkerArray = [];
    $scope.timetableModel = {};
    $scope.years = [];
    
    setWorkers();
    setDataForAdd();
    
    $scope.days = getDays(getMonth(), getYear());
    
    $('.monthSelect').change(function() {
        $scope.days = getDays(getMonth(), getYear());
        $scope.$apply();
    });
    
    $('.yearSelect').change(function() {
        $scope.days = getDays(getMonth(), getYear());
        $scope.$apply();
    });
    
    $scope.addWorker = function() {
        var addWorkerId = $scope.workerIdForAdding;
        $scope.workerIdForAdding = 0;
        if (addWorkerId) {
            var fio = '';
            for (var i = 0; i < $scope.workers.length; i++) {
                var worker = $scope.workers[i];
                if (worker.worker_id == addWorkerId) {
                    fio = worker.fio;
                }
            }
            $scope.timetableWorkerArray.push({
                timetable_worker_id: '',
                worker_id: addWorkerId,
                worker: {
                    fio: fio
                }            
            });
            $('#myModal').modal('hide');
        } else {
            alert('выберите пользователя!');
        }
    }
    
    $scope.deleteTimetableWorker = function(index) {
        if (confirm('подтвердите удаление')) {
            $scope.timetableWorkerArray.splice(index, 1);
        }
    }
    
    
    function setDataForAdd() {
        var url = '?r=timetable/add_info';
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var result = $.parseJSON(response.data);
            $scope.years = result.years;
            $scope.timetableModel.year = String(result.currentYear);
            $scope.timetableModel.month = String(result.currentMonth);
            $scope.days = getDays(getMonth(), getYear());
        }, function error() {});
    }
    
    function getDays(month, year) {
        var dayNames = ['', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        var arr = [];
        month = month - 1;
        var date = new Date(year, month, 1);
        while (date.getMonth() === month) {
            var dayOfWeek = (date.getDay() === 0 ? 7 : date.getDay());
            var holiday = false;
            if (dayOfWeek === 6 || dayOfWeek === 7) {
                holiday = true;
            }
            arr.push({
                dayOfMonth: date.getDate(), 
                dayOfWeek: dayOfWeek,
                dayOfWeekName: dayNames[dayOfWeek],
                holiday: holiday
            });
            date.setDate(date.getDate() + 1);
        }
        return arr;
    }
    
    function getMonth() {
        //return Number($('.monthSelect').val());
        return Number($scope.timetableModel.month);
    }
    
    function getYear() {
        //return Number($('.yearSelect').val());
        return Number($scope.timetableModel.year);
    }
    
    function setWorkers() {
        var url = '?r=worker/list&type=json';
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var data = $.parseJSON(response.data);
            $scope.workers = data;
        }, function error(response) {});
    }
    
});


