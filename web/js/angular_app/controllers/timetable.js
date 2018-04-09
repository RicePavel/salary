

myApp.controller('timetableController', function($scope, $http, $timeout) {
     
    $(window).on('beforeunload', function(event) {
        return '1';
    });
    
    const minCountRows = 2;    
        
    const DEFAULT_EMPLOYMENT_TYPE_ID = 2;
    const DEFAULT_EMPLOYMENT_TYPE_SHORT_NAME = 'Я';
        
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
        timetableWorkerIndex: undefined,
        rowIndex: undefined,
        day: undefined,
        
        text: '',
        
        nowEdited: false,
        showEmploymentTypesList: false,
        
        notFoundEmploymentType: false,
        notFoundText: ''
    };
    
    /* */
    
    var daysInfoKeeper = new DaysInfoKeeper([]);
    
    /* */
    
    var mode = setMode();
    
    setWorkers();
    if (mode === 'add') {
        setDataForAdd();
    } else {
        setDataForChange();
    }
    
    $scope.changeCountRows = function() {
        var currentCountRows = daysInfoKeeper.getMaxCountRows();
        if ($scope.timetableModel.count_rows_on_day < minCountRows) {
            $scope.timetableModel.count_rows_on_day = minCountRows;
            alert('Невозможно уменьшить количество строк. Количество должно быть не менее ' + minCountRows);
        } else if ($scope.timetableModel.count_rows_on_day < currentCountRows) {
            $scope.timetableModel.count_rows_on_day = currentCountRows;
            alert('Невозможно уменьшить количество строк. Есть полностью заполненные дни.');
        } else {
            daysInfoKeeper.setCountRowsOnDay($scope.timetableModel.count_rows_on_day);
        }
    };
    
    $scope.copyTimetableWorker = function(timetableWorkerIndex) {
        daysInfoKeeper.copyTimetableWorker(timetableWorkerIndex);
    };
    
    $scope.getPartTotalText = function (timetableWorkerIndex) {
        var str = $scope.getTotalText(timetableWorkerIndex);
        if (str.length > 7) {
            str = str.substring(0, 7) + '...';
        }
        return str;
    };
    
    $scope.getTotalText = function(timetableWorkerIndex) {
        var obj = daysInfoKeeper.getTotals(timetableWorkerIndex);
        var str = '';
        for (var shortName in obj) {
            str += shortName + ' ' + obj[shortName].days + ' д. ' + obj[shortName].hours + ' ч.  ';
        }
        return str;
    };
    
    $scope.getDaysInfoArray = function() {
        var daysInfoArray = daysInfoKeeper.getDaysInfoArray();
        return daysInfoArray;
    };
    
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
            daysInfoKeeper.addTimetableWorker(addWorkerId, fio, $scope.timetableModel.count_rows_on_day);
            $('#myModal').modal('hide');
        } else {
            alert('выберите пользователя!');
        }
    };
    
    $scope.deleteTimetableWorker = function(index) {
        if (confirm('подтвердите удаление')) {
            daysInfoKeeper.deleteTimetableWorker(index);
        }
    };
    
    $scope.updateDays = function() {
        $scope.days = getDays(getMonth(), getYear());
    };
    
    $scope.save = function() {
        $(window).off('beforeunload');
        
        if (mode === 'add') {
            var url = '?r=timetable/add&type=addByAjax';
        } else {
            var url = '?r=timetable/change&type=byAjax';
        }
        var formData = new FormData();
        for (var key in $scope.timetableModel) {
            formData.append('Model[' + key + ']', $scope.timetableModel[key]);
        }
        formData.append('daysInfoArray', $.toJSON(daysInfoKeeper.getDaysInfoArray()));
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
    };
    
    /* редактирование пользователя */
    
    $scope.changeWorker = function(timetableWorkerIndex, workerId) {
        var fio = getWorkerFio(workerId);
        daysInfoKeeper.setWorker(timetableWorkerIndex, workerId, fio);
    };
    
    $('body').on('dblclick', '.workerTd', function(event) {
        var target = $(event.target);
        if (target.prop("tagName") !== "BUTTON") {
            var td = $(event.currentTarget);
            var div = td.find('.workerFio');
            var select = td.find('.workerFioSelect');
            div.hide();
            select.show();
            var finishEditing = function(event) {
                var target = $(event.target);
                var tagName = target.prop("tagName");
                if (tagName !== 'SELECT' && tagName !== 'OPTION') {
                    div.show();
                    select.hide();
                    $(document).off('click', finishEditing);
                }
            };
            $(document).click(finishEditing);
        }
    });
    
    /** перемещение вверх и вниз */
    
    $scope.up = function(index) {
        daysInfoKeeper.up(index);
    };
    
    $scope.down = function(index) {
        daysInfoKeeper.down(index);
    }
    
    /* ------------------------------------- */
    /* ------------------------------------- */
    /* функции редактирования ячейки таблицы */
   
    $scope.cellEdited = function(timetableWorkerIndex, rowIndex, day) {
        if ($scope.editableModel.nowEdited === true 
                && $scope.editableModel.timetableWorkerIndex === timetableWorkerIndex
                && $scope.editableModel.rowIndex === rowIndex
                && $scope.editableModel.day === day) {
            return true;
        } else {
            return false;
        }
    }
    
    $scope.showEmploymentTypesList = function(timetableWorkerIndex, rowIndex, day) {
        return ($scope.cellEdited(timetableWorkerIndex, rowIndex, day) && $scope.editableModel.showEmploymentTypesList);
    }
    
    $scope.editCell = function(timetableWorkerIndex, rowIndex, day, event) {
        $scope.editableModel.nowEdited = true;
        $scope.editableModel.timetableWorkerIndex = timetableWorkerIndex;
        $scope.editableModel.rowIndex = rowIndex;
        $scope.editableModel.day = day;
        
        for (var i = 0; i < $scope.employmentTypes.length; i++) {
            $scope.employmentTypes[i].show = true;
        }
        $scope.editableModel.showEmploymentTypesList = true;
        
        var dayInfo = daysInfoKeeper.getDayInfo(timetableWorkerIndex, rowIndex, day);
        $scope.editableModel.text = getDayInfoText(dayInfo);
        
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
        };
        $(document).click(onEndEdit);
        
        var endEditByKeypress = function(event) {
            if (event.keyCode == 13) {
                endEditCell();
                $(document).off('keypress', endEditByKeypress);
                var nextTd = td.next("td");
                if (nextTd.length === 1) {
                    nextTd.dblclick();
                }
            }
        };
        $(document).keypress(endEditByKeypress);
    };
    
    function getDayInfoText(dayInfoObject) {
        var text = '';
        if (dayInfoObject !== undefined) {
            if (dayInfoObject.short_name !== undefined && dayInfoObject.short_name !== null) {
                text = dayInfoObject.short_name + ' ';
                if (dayInfoObject.time !== undefined && dayInfoObject.time !== null) {
                    text += dayInfoObject.time;
                }
            }
        }
        return text;
    };
    
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
    };
    
    $scope.getDayElementText = function(timetableWorkerIndex, rowIndex, day) {
        var dayInfo = daysInfoKeeper.getDayInfo(timetableWorkerIndex, rowIndex, day);
        return getDayInfoText(dayInfo);
    };
    
    function onChangeCellValue(event) {
        var value = $(event.target).val();
        $scope.editableModel.text = value;
        filterEmploymentTypeList($scope.editableModel.text);
        $scope.editableModel.showEmploymentTypesList = true;
        $scope.$apply();
    };
    
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
        
        var timetableWorkerIndex = $scope.editableModel.timetableWorkerIndex;
        var rowIndex = $scope.editableModel.rowIndex;
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
        if (employmentTypeId === undefined && time !== undefined) {
            employmentTypeId = DEFAULT_EMPLOYMENT_TYPE_ID;
            employmentTypeShortName = DEFAULT_EMPLOYMENT_TYPE_SHORT_NAME;
        }
        daysInfoKeeper.saveDayInfo(timetableWorkerIndex, rowIndex, day, time, employmentTypeId, employmentTypeShortName);
        $scope.$apply();
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
            $scope.timetableModel.count_rows_on_day = minCountRows;
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
            if (!$scope.timetableModel.count_rows_on_day || $scope.timetableModel.count_rows_on_day < minCountRows) {
                $scope.timetableModel.count_rows_on_day = minCountRows;
            }
            $scope.timetableWorkerArray = result.timetableWorkerArray;
            $scope.employmentTypes = result.employmentTypes;
            daysInfoKeeper = new DaysInfoKeeper(result.daysInfoArray);
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
    
    function getWorkerFio(workerId) {
        var fio = '';
        for (var i = 0; i < $scope.workers.length; i++) {
            var worker = $scope.workers[i];
            if (worker.worker_id == workerId) {
                fio = worker.fio;
            }
        }
        return fio;
    }
    
});


function DaysInfoKeeper(daysInfoArray) {
    
    /**
     * @type Array
     * 
     * [
     *   {
     *      timetable_worker_id : ,
     *      fio: ,
     *      worker_id,
     *      rows : [
     *         {
     *              timetable_row_id : ,
     *              days : {
     *                  -dayNumber- : {
     *                      time: ,
     *                      employment_type_id: ,
     *                      short_name:    
     *                  }
     *              }
     *          }
     *      ]
     *   }
     * ]
     * 
     */
    var timetableWorkersArray = daysInfoArray;
    
    this.getDaysInfoArray = function() {
        return timetableWorkersArray;
    }; 
    
    this.addTimetableWorker = function(worker_id, fio, countRows) {
        var obj = {
            timetable_worker_id: '',
            fio: fio,
            worker_id: worker_id,
            rows: []
        };
        while (obj.rows.length < countRows) {
            obj.rows.push(getNewRow());
        }
        
        timetableWorkersArray.push(obj);
    };
    
    this.setCountRowsOnDay = function(count) {
        for (var i = 0; i < timetableWorkersArray.length; i++) {
            var rows = timetableWorkersArray[i].rows;
            if (rows.length > count) {
                var newRows = rows.slice(0, count);
                timetableWorkersArray[i].rows = newRows;
            } else if (rows.length < count) {
                var newRows = rows;
                while (newRows.length < count) {
                    newRows.push(getNewRow());
                }
                timetableWorkersArray[i].rows = newRows;
            }
        }
    };
    
    this.getDayInfo = function(timetableWorkerIndex, rowIndex, day) {
        if (timetableWorkersArray[timetableWorkerIndex]) {
            var tw = timetableWorkersArray[timetableWorkerIndex];
            if (tw.rows[rowIndex]) {
                var r = tw.rows[rowIndex];
                if (r.days[day]) {
                    return r.days[day];
                }
            }
        }
        return undefined;
    };
    
    this.saveDayInfo = function(timetableWorkerIndex, rowIndex, day, time, employment_type_id, short_name) {
        if (timetableWorkersArray[timetableWorkerIndex]) {
            var tw = timetableWorkersArray[timetableWorkerIndex];
            if (tw.rows[rowIndex]) {
                var r = tw.rows[rowIndex];
                r.days[day] = getNewDay(time, employment_type_id, short_name);
            }
        }
    };
    
    this.deleteTimetableWorker = function(index) {
        timetableWorkersArray.splice(index, 1);
    };
    
    this.getMaxCountRows = function() {
        var maxCountRows = 0;
        for (var i = 0; i < timetableWorkersArray.length; i++) {
            var currentCountRows = 0;
            var timetableWorker = timetableWorkersArray[i];
            for (var k = 0; k < timetableWorker.rows.length; k++) {
                var row = timetableWorker.rows[k];
                if (existCompletedDays(row)) {
                    currentCountRows++;
                }
            }
            if (currentCountRows > maxCountRows) {
                maxCountRows = currentCountRows;
            }
        }
        return maxCountRows;
    };
    
    /**
     * 
     * {shortName: {days: , hours: } }
     * 
     * @param {type} timetableWorkerIndex
     * @returns {shortName: {days: , hours: } }
     */
    this.getTotals = function(timetableWorkerIndex) {
        var obj = {};
        var rows = timetableWorkersArray[timetableWorkerIndex].rows;
        for (var k = 0; k < rows.length; k++) {
            var days = rows[k].days;
            for (var d in days) {
                var day = days[d];
                var shortName = day.short_name;
                var time = day.time;
                if (shortName) {
                    if (!(shortName in obj)) {
                        obj[shortName] = {days: 0, hours: 0};
                    }
                    obj[shortName].days++;
                    if (time !== undefined) {
                        obj[shortName].hours += Number(time);
                    }
                }
            }
        }
        return obj;
    };
    
    this.setWorker = function(timetableWorkerIndex, workerId, fio) {
        var elem = timetableWorkersArray[timetableWorkerIndex];
        elem.worker_id = Number(workerId);
        elem.fio = fio;
    };
    
    this.copyTimetableWorker = function(timetableWorkerIndex) {
        var elem = timetableWorkersArray[timetableWorkerIndex];
        var clone = angular.merge({}, elem);
        timetableWorkersArray.push(clone);
    };
    
    this.up = function(index) {
        if (index > 0) {
            var upElement = timetableWorkersArray[index - 1];
            timetableWorkersArray[index - 1] = timetableWorkersArray[index];
            timetableWorkersArray[index] = upElement;
        } 
    };
    
    this.down = function(index) {
        if (index < timetableWorkersArray.length - 1) {
            var downElement = timetableWorkersArray[index + 1];
            timetableWorkersArray[index + 1] = timetableWorkersArray[index];
            timetableWorkersArray[index] = downElement;
        }
    };
    
    function existCompletedDays(row) {
        for (var d in row.days) {
            var day = row.days[d];
            if (day.time || day.employment_type_id) {
                return true;
            }
        }
        return false;
    }
    
    function getNewDay(time, employment_type_id, short_name) {
        return {
            time: time,
            employment_type_id: employment_type_id,
            short_name: short_name
        };
    }
    
    function getNewRow() {
        return {
            timetable_row_id : '',
            days: {}
        };
    }
    
}


