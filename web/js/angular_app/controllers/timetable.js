

myApp.controller('timetableController', function($scope, $http, $timeout) {
        
    $scope.workers = [];
    
    /*
     * [
     *    {
     *       timetable_worker_id: ,
     *       worker_id: ,
     *       fio: 
     *    }
     *  ]
    */
    $scope.timetableWorkerArray = [];
    $scope.timetableModel = {};
    $scope.years = [];
    $scope.months = [];
    
    /*
     * [
     *    {
     *       employment_type_id: ,
     *       name: ,
     *       show: 
     *     }
     *  ]
     */
    $scope.employmentTypes = [];
    
    /* */
    $scope.editableModel = {
        nowEdited: false,
        
        rowNumber: '',
        timetable_worker_id: '',
        year: '',
        month: '',
        day: '',
        
        text: '',
        
        showEmploymentTypesList: false,
        
        notFoundEmploymentType: false,
        notFoundText: ''
    };
    
    /* */
    
    /*
     * 
     * @type Array
     * 
     * [
     *    -rowNumber- : {
     *       timetable_worker_id : ,
     *       days: {
     *          -dayNumber- : {
     *              time: ,
     *              employment_type_id: ,
     *              employment_type_short_name
     *          }
     *       }
     *    }
     * ]
     * 
     */
    $scope.daysInfoArray = [];
    
    /* */
    
    var mode = setMode();
    
    setWorkers();
    if (mode === 'add') {
        setDataForAdd();
    } else {
        setDataForChange();
    }
    
    $scope.addTimetableWorker = function() {
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
            var length = $scope.timetableWorkerArray.length;
            $scope.timetableWorkerArray.push({
                timetable_worker_id: '',
                worker_id: addWorkerId,
                fio: fio
            });
            $('#myModal').modal('hide');
            getDaysInfoRow();
        } else {
            alert('выберите пользователя!');
        }
    };
    
    $scope.deleteTimetableWorker = function(index) {
        if (confirm('подтвердите удаление')) {
            $scope.timetableWorkerArray.splice(index, 1);
            deleteDaysInfoRow(index);
        }
    };
    
    $scope.updateDays = function() {
        $scope.days = getDays(getMonth(), getYear());
    };
    
    $scope.save = function() {
        if (mode === 'add') {
            var url = '?r=timetable/add&type=addByAjax';
        } else {
            var url = '?r=timetable/change&type=byAjax';
        }
        var formData = new FormData();
        for (var key in $scope.timetableModel) {
            formData.append('Model[' + key + ']', $scope.timetableModel[key]);
        }
        var json = $.toJSON($scope.timetableWorkerArray);
        formData.append('timetableWorkerArray', json);
        formData.append('daysInfoArray', $.toJSON($scope.daysInfoArray));
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                var obj = $.parseJSON(response);
                if (obj.ok) {
                    window.location = './index.php?r=timetable/list';
                } else {
                    alert(obj.error);
                }
            }
        });
    }
    
    /* ------------------------------------- */
    /* ------------------------------------- */
    /* функции редактирования ячейки таблицы */
   
    $scope.cellEdited = function(rowNumber, day) {
        if ($scope.editableModel.nowEdited === true 
                && $scope.editableModel.rowNumber === rowNumber
                && $scope.editableModel.day === day) {
            return true;
        } else {
            return false;
        }
    }
    
    $scope.showEmploymentTypesList = function(rowNumber, day) {
        return ($scope.cellEdited(rowNumber, day) && $scope.editableModel.showEmploymentTypesList);
    }
    
    $scope.editCell = function(rowNumber, day, event) {
        $scope.editableModel.nowEdited = true;
        $scope.editableModel.rowNumber = rowNumber;
        $scope.editableModel.day = day;
        for (var i = 0; i < $scope.employmentTypes.length; i++) {
            $scope.employmentTypes[i].show = true;
        }
        $scope.editableModel.showEmploymentTypesList = true;
        $scope.editableModel.text = getDaysInfoElementText(rowNumber, day);
        filterEmploymentTypeList($scope.editableModel.text);
        var td = $(event.currentTarget);
        var input = td.children('input');
        td.addClass('edited');
        $timeout(function() {input.focus(); });
        var onEndEdit = function(event) {
            var target = $(event.target);
            var parent = target.closest('.edited');
            if (!target.hasClass('edited') && parent.length == 0) {
                endEditCell();
                $(document).off('click', onEndEdit);
            }
        }
        $(document).click(onEndEdit);
    }
    
    // обработчик ввода в поле
    $('body').on('input', '.dayCellInput', function(event) {
        onChangeCellValue(event);
    });
    
    $scope.selectEmploymentType = function(shortName, event) {
        var text = $scope.editableModel.text;
        var obj = parseInputValue(text);
        var newText = shortName + ' ';
        if (obj.time !== undefined) {
            newText += obj.time;
        }
        $scope.editableModel.text = newText;
        filterEmploymentTypeList($scope.editableModel.text);
        $scope.editableModel.showEmploymentTypesList = false;
        var target = $(event.target);
        var input = target.parent('ul').siblings('input');
        $timeout(function() {input.focus(); });
    }
    
    $scope.getTimeTableElementText = function(rowNumber, day) {
        return getDaysInfoElementText(rowNumber, day);
    }
    
    function onChangeCellValue(event) {
        var value = $(event.target).val();
        $scope.editableModel.text = value;
        filterEmploymentTypeList($scope.editableModel.text);
        $scope.editableModel.showEmploymentTypesList = true;
        $scope.$apply();
    }
    
    function filterEmploymentTypeList(value) {
        var obj = parseInputValue(value);
        var shortName = obj.shortName;  
        var find = false;
        if (shortName === '') {
            for (var i = 0; i < $scope.employmentTypes.length; i++) {
                $scope.employmentTypes[i].show = true;
                find = true;
            }
        } else {
            for (var i = 0; i < $scope.employmentTypes.length; i++) {
                if ($scope.employmentTypes[i].short_name.toLowerCase().indexOf(shortName.toLowerCase()) !== -1) {
                    $scope.employmentTypes[i].show = true;
                    find = true;
                } else {
                    $scope.employmentTypes[i].show = false;
                }
            }
        }
        if (find) {
            $scope.editableModel.notFoundEmploymentType = false;
        } else {
            $scope.editableModel.notFoundEmploymentType = true;
            $scope.editableModel.notFoundText = shortName;
        }
    }
    
    function endEditCell() {
        var m = $scope.editableModel;
        m.nowEdited = false;
        $scope.$apply();
        $('.edited').removeClass('edited');
        
        var rowNumber = $scope.editableModel.rowNumber;
        var day = $scope.editableModel.day;
        var obj = parseInputValue($scope.editableModel.text);
        var time = obj.time;
        var employmentTypeShortName = obj.shortName;
        var employmentTypeId = undefined;
        if (employmentTypeShortName !== '') {
            employmentTypeId = getEmploymentTypeId(employmentTypeShortName);
            if (employmentTypeId === undefined) {
                alert('введены неправильные данные!');
                return;
            }
        }
        saveDaysInfoElement(rowNumber, day, time, employmentTypeId, employmentTypeShortName);
    }
    
    function getEmploymentTypeId(employmentTypeShortName) {
        var employmentTypeId = undefined;
        for (var i = 0; i < $scope.employmentTypes.length; i++) {
            var elem = $scope.employmentTypes[i];
            if (elem.short_name.toLowerCase() === employmentTypeShortName.toLowerCase()) {
                employmentTypeId = elem.employment_type_id;
            }
        }
        return employmentTypeId;
    }
    
    /*
     * 
     * @param {type} value
     * @returns {time:  shortName:  }
     */
    function parseInputValue(value) {
        var shortName = '';
        var time = undefined;
        var a = value.split(' ');
        var arr = [];
        for (var i = 0; i < a.length; i++) {
            if (a[i] !== '') {
                arr.push(a[i]);
            }
        }
        if (arr.length === 0) {
            
        } else if (arr.length === 1) {
            var elem = arr[0];
            if (isNumeric(elem)) {
                time = Number(elem);
            } else {
                shortName = elem;
            }
        } else if (arr.length === 2) {
            if (isNumeric(arr[1])) {
                shortName = arr[0];
                time = arr[1];
            } else {
                shortName = value;
            }
        } else {
            shortName = value;
        }
        return {
            shortName: shortName,
            time: time
        };
    }
    
    function saveDaysInfoElement(rowNumber, day, time, employmentTypeId, employmentTypeShortName) {
        $scope.daysInfoArray[rowNumber].days[day] = {
            time: time,
            employment_type_id: employmentTypeId,
            employment_type_short_name: employmentTypeShortName
        };
        $scope.$apply();
    }
    
    function getDaysInfoElementText(rowNumber, day) {
        var arr = $scope.daysInfoArray;
        if (arr[rowNumber] && arr[rowNumber].days && arr[rowNumber].days[day]) {
            var obj = arr[rowNumber].days[day];
            var time = obj.time;
            var text = obj.employment_type_short_name;
            if (time !== undefined && time !== null) {
                text += ' ' + time;
            }
            return text;
        } else {
            return '';
        }
    }
    
    function getDaysInfoRow() {
        $scope.daysInfoArray.push({
            timetable_worker_id: '',
            days: {}
        });
    }
    
    function deleteDaysInfoRow(rowNumber) {
        $scope.daysInfoArray.splice(rowNumber, 1);
    }
    
    /* ------------------------------------------------------- */
    /* ------------------------------------------------------- */
    /* ------------------------------------------------------- */
    
    function setDataForAdd() {
        var url = '?r=timetable/add_info';
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var result = $.parseJSON(response.data);
            $scope.years = result.years;
            $scope.months = result.months;
            $scope.units = result.units;
            $scope.timetableModel.year = String(result.currentYear);
            $scope.timetableModel.month = String(result.currentMonth);
            $scope.timetableModel.create_date = result.currentDate;
            $scope.employmentTypes = result.employmentTypes;
            $scope.days = getDays(getMonth(), getYear());
        }, function error() {});
    }
    
    function setDataForChange() {
        var params = getUrlParams();
        var timetable_id = params.timetable_id;
        var url = '?r=timetable/change&type=getDataForChange&timetable_id=' + timetable_id;
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var result = $.parseJSON(response.data);
            if (!result.ok) {
                alert(result.error);
            }
            $scope.years = result.years;
            $scope.months = result.months;
            $scope.units = result.units;
            $scope.timetableModel = result.model;
            $scope.timetableWorkerArray = result.timetableWorkerArray;
            $scope.employmentTypes = result.employmentTypes;
            $scope.daysInfoArray = result.daysInfoArray;
            $scope.days = getDays(getMonth(), getYear());
        });
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
        return Number($scope.timetableModel.month);
    }
    
    function getYear() {
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
    
    function setMode() {
        var params = getUrlParams();
        if (params['r'] === 'timetable/add') {
            return 'add';
        } else if (params['r'] === 'timetable/change') {
            return 'change';
        } else {
            alert('Ошибка');
        }
    }
    
    function getUrlParams() {
       return window.location.search.replace('?','').split('&').reduce(
        function(p,e){
                var a = e.split('=');
                p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                return p;
            },
            {}
        );
    }
    
    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    
});


