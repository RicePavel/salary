

myApp.controller('timetableController', function($scope, $http) {
    $scope.test = "123";
    
    $scope.changeDate = function() {
        
    };
    
    $('.monthSelect').change(function() {
        $scope.days = getDays(getMonth(), getYear());
        $scope.$apply();
    });
    
    $('.yearSelect').change(function() {
        $scope.days = getDays(getMonth(), getYear());
        $scope.$apply();
    });
    
    $scope.days = getDays(getMonth(), getYear());
    
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
        return Number($('.monthSelect').val());
    }
    
    function getYear() {
        return Number($('.yearSelect').val());
    }
    
});


