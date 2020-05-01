
var user = new User(
    userLogin,
    function () {
        // ИНИЦИАЛИЗАЦИЯ ВКЛАДОК
        initCartridgesTab();
        initEmployeesTab();
        initNews();
    },
    function (data) {
        showAlert("Что-то пошло не так! Cайт не может продолжить работу, попробуйте обновить страницу");
    });

function addActionForEmployeeTableRows() {
    if(!user.isAdmin() && !user.isHR()) return;

    $("div#emplListContainer > table > tbody > tr").off("click");

    // ROW ON CLICK ACTION
    $("div#emplListContainer > table > tbody > tr").on('click', function(){
        $.ajax({
            url: '/ajax.php',
            method: 'post',
            dataType: 'html',
            data: {
                act: "getempl",
                emplLogin: $(this).attr('data-login')
            },
            success: function(json) {
                try {
                    var data = JSON.parse(json);

                    curEditEmplLogin = data.login;

                    $('#emplLogin').val(data.login);
                    $('#emplPassword').val(data.pass);
                    $('#emplName').val(data.name);
                    $('#roleSelect').val(data.roleId);
                    $('#positionSelect').val(data.positionId);
                    $('#statusSelect').val(data.statusId);
                    $('#emplPhone').val(data.phone);

                    if(data.login === user.getLogin) {
                        $('#editionDanger').show();
                        isSelfEdition = true;
                    } else {
                        $('#editionDanger').hide();
                        isSelfEdition = false;
                    }

                    $('#emplForm').removeClass('was-validated');
                    $('#emplModal').modal('show');
                } catch(err) {
                    console.log(err);
                }
            }
        });
    });
}

$('#addEmployee').on('click', function() {
    curEditEmplLogin = -1;
    isEmplActAddition = true;

    $('#editionDanger').hide();

    $('#emplLogin').val("");
    $('#emplPassword').val("");
    $('#emplName').val("");
    $('#emplPhone').val("");

    $("#emplDelete").hide();
    $('#emplModalTitle').html("Добавление сотрудника");
    $('#emplSave').html("Подтвердить");
    $('#emplModal').modal('show');
});

function saveCartChanges() {
    if(isCartActAddition) {
        if($('#cartModelName').val() === "") return;
        addCartridge();
        return;
    }
    $('#cartridgeModal').modal('hide');
    $('#cartrContainer tr[data-id="' + curCartridgeName + '"]').animate({'opacity': 0}, 200, function() { // HIDE ROW UNTIL UPDATE
        // UPDATE VALUE
        $.ajax({
            url: '/cartridges.php',
            method: 'put',
            contentType: "application/json",
            data: JSON.stringify({
                "name": $('#cartModelName').val(),
                "oldName": curCartridgeName,
                "analogue": ($('#cartAnalogues').val() === "") ? "" : $('#cartAnalogues').val(),
                "manufacId": $('#manufSelect').val(),
                "number": $('#cartNumber').val()
            }),
            success: function(data) {
                try {
                    if(data.rowsAffected === 1 || data.rowsAffected === 0) {
                        $('#cartridgeModal').modal('hide');
                        // LOAD AND SHOW VALUE
                        $('#cartrContainer tr[data-id="' + curCartridgeName + '"]').html('<td>'+ $('#manufSelect option[value="' + $('#manufSelect').val() + '"]').html() +'</td><th scope="row">' + $('#cartModelName').val() + '</th><td>' + (($('#cartAnalogues').val() === "") ? '-' : $('#cartAnalogues').val()) + '</td><td>'+ $('#cartNumber').val() +'</td>').animate({'opacity': 1}, 200, function() {
                            $(this).attr('data-id', $('#cartModelName').val());
                            curCartridgeName = -1;
                            //addActionForRows();
                        });

                    } else {
                        alert(data);
                    }
                } catch(err) {
                    if(err instanceof SyntaxError) {
                        alert(json);
                    }
                }
            },
            error: function(data) {
                console.log(data.responseJSON);
            }
        });
    });
}

function saveEmplChanges() {
    if(isEmplActAddition) {
        addEmployee();
        return;
    }
    $('#emplList tbody tr[data-login="' + curEditEmplLogin + '"]').animate({'opacity': 0}, 200, function() { // HIDE ROW UNTILL UPDATE
        // UPDATE EMPLOYEE VALUE
        $.ajax({
            url: '/ajax.php',
            method: 'post',
            contentType: "application/json",
            dataType: 'json',
            headers: { 'X-Act': 'updateempl' },
            data: JSON.stringify({
                "phone": ($('#emplPhone').val() === "") ? null : $('#emplPhone').val(),
                "statusId": $('#statusSelect').val(),
                "positionId": $('#positionSelect').val(),
                "roleId": $('#roleSelect').val(),
                "name": $('#emplName').val(),
                "pass": $('#emplPassword').val(),
                "login": $('#emplLogin').val(),
                "oldLogin": curEditEmplLogin
            }),
            success: function(data) {
                try {
                    if(data.rowsAffected === 1 || data.rowsAffected === 0) {
                        if(isSelfEdition && data.rowsAffected === 1) location.href = "./login.php?act=logout";
                        // LOAD AND SHOW EMPLOYEE ROW
                        $('#emplList tbody tr[data-login="' + curEditEmplLogin + '"]').attr('data-unit', data.newUnitId);
                        $('#emplList tbody tr[data-login="' + curEditEmplLogin + '"]').html('<th scope="row">' + $('#emplName').val() + '</th><td>' + $('#positionSelect option[value="' + $('#positionSelect').val() + '"]').html() + '</td><td>' + (($('#emplPhone').val() === '') ? '-' : $('#emplPhone').val()) + '</td>').animate({'opacity': 1}, 200, function() {
                            $(this).attr('data-login', $('#emplLogin').val());
                            curEditEmplLogin = -1;
                        });
                    } else {
                        alert(data);
                    }
                } catch(err) {
                    console.log(err);
                }
            },
            error: function(data) {
                console.log(data.responseJSON);
            }
        });
    });
}

// EMPLOYEES TABLE FILTER ON CHANGE
$("#unitSelect").change(function() {
    let selectedUnit = parseInt($("#unitSelect").val());
    $("#emplList tbody tr").each(function() {
        let tr = $(this);
        if(selectedUnit === 0) {
            if(tr.css('opacity') === "0") {
                tr.animate({opacity: 1}, 200)
                    .children()
                    .show();
            }
            return true;
        }

        if(parseInt(tr.attr('data-unit')) !== selectedUnit) {
            tr.animate({opacity: 0}, 200)
                .children()
                .slideUp();
        } else {
            if(tr.css('opacity') === "0") {
                tr.animate({opacity: 1}, 200)
                    .children()
                    .show();
            }
        }
    });
});

$("table #minus").on('click', function() {
    if(parseInt($('#cartNumber').val()) < 1) return;
    $('#cartNumber').val(parseInt($('#cartNumber').val()) - 1);
});

$("table #plus").on('click', function() {
    $('#cartNumber').val(parseInt($('#cartNumber').val()) + 1);
});

// CARTRIDGE DELETION ON CLICK
$("#cartDelete").on('click', function() {
    deleteCartridge(curCartridgeName);
});

$('#addCartridge').on('click', function() {
    curCartridgeName = -1;
    isCartActAddition = true;

    $('#cartModelName').val("");
    $('#cartAnalogues').val("");
    $('#cartNumber').val("0");

    $("#cartDelete").hide();
    $('#cartridgeModalTitle').html("Добавление картриджа");
    $('#cartSave').html("Подтвердить");
    $('#cartridgeModal').modal('show');
});

$('#cartridgeModal').on('hidden.bs.modal', function () {
    if(isCartActAddition) {
        $('#cartDelete').show();
        $('#cartridgeModalTitle').html("Редактирование картриджа");
        $('#cartSave').html("Сохранить");
        isCartActAddition = false;
    }
})

$('#emplModal').on('hidden.bs.modal', function () {
    if(isEmplActAddition) {
        $('#emplDelete').show();
        $('#emplModalTitle').html("Редактирование cотрудника");
        $('#emplSave').html("Сохранить");
        isEmplActAddition = false;
    }
})

function addCartridge() {
    $.ajax({
        url: '/cartridges.php',
        method: 'post',
        contentType: "application/json",
        data: JSON.stringify({ "name": $('#cartModelName').val(), "analogue": ($('#cartAnalogues').val() === "") ? "" : $('#cartAnalogues').val(), "manufacId": $('#manufSelect').val(), "number": $('#cartNumber').val()}),
        success: function(data) {
            try {
                if(data.rowsAffected === 1) {
                    $('#cartridgeModal').modal('hide');
                    $('#cartrContainer table tr:last').after('<tr class="clickableCart" data-id="' + $('#cartModelName').val() + '"><td>'+ $('#manufSelect option[value="' + $('#manufSelect').val() + '"]').html() +'</td><th scope="row">' + $('#cartModelName').val() + '</th><td>' + (($('#cartAnalogues').val() === "") ? '-' : $('#cartAnalogues').val()) + '</td><td>'+ $('#cartNumber').val() +'</td></tr>').animate({'opacity': 1}, 200);
                    addActionForRows();
                } else {
                    alert(data);
                }
            } catch(err) {
                console.log(err);
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

function deleteCartridge(model) {
    $.ajax({
        url: '/cartridges.php',
        method: 'delete',
        contentType: "application/json",
        data: JSON.stringify({'id': model}),
        success: function(data) {
            try {
                if(data.rowsAffected === 1) {
                    $('#cartridgeModal').modal('hide');
                    $('#cartrContainer tr[data-id="' + curCartridgeName + '"]').animate({ height: 0, opacity: 0 }, 200)
                        .children()
                        .slideUp(function() { $(this).remove(); });
                    curCartridgeName = -1;
                } else {
                    alert(data);
                }
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

function addEmployee() { // ADDITION OF AN EMPLOYEE
    $.ajax({
        url: '/ajax.php',
        method: 'post',
        contentType: "application/json",
        dataType: 'json',
        headers: { 'X-Act': 'createempl' },
        data: JSON.stringify({
            "phone": ($('#emplPhone').val() === "") ? null : $('#emplPhone').val(),
            "statusId": $('#statusSelect').val(),
            "positionId": $('#positionSelect').val(),
            "roleId": $('#roleSelect').val(),
            "name": $('#emplName').val(),
            "pass": $('#emplPassword').val(),
            "login": $('#emplLogin').val()
        }),
        success: function(data) {
            try {
                if(data.rowsAffected === 1 || data.rowsAffected === 0) {
                    $('#emplList tbody tr:last').after('<tr data-login="' + $('#emplLogin').val() + '" data-unit="' + data.newUnitId + '"><th scope="row">' + $('#emplName').val() + '</th><td>' + $('#positionSelect option[value="' + $('#positionSelect').val() + '"]').html() + '</td><td>' + (($('#emplPhone').val() === '') ? '-' : $('#emplPhone').val()) + '</td></tr>').animate({'opacity': 1}, 200);
                    addActionForEmployeeTableRows();
                } else {
                    alert(data);
                }
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

function deleteEmployee(login) {
    $.ajax({
        url: '/ajax.php',
        method: 'post',
        dataType: 'html',
        data: {
            act: "delempl",
            emplLogin: login
        },
        success: function(data) {
            try {
                if(data === "1") {
                    if(isSelfEdition) location.href = "./login.php?act=logout";
                    $('#emplModal').modal('hide');
                    $('#emplListContainer tbody tr[data-login="' + curEditEmplLogin + '"]').animate({opacity: 0 }, 200)
                        .children()
                        .slideUp(function() { $(this).remove(); });
                    curEditEmplLogin = -1;
                } else {
                    alert(data);
                }
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        }
    });
}

$('#updateCartTable').on('click', function() {
    $('#cartrList').html("").animate({'opacity': 0}, 200);
    initCartridgesTab();
});

$('#updateEmplTable').on('click', function() {
    $('#emplList').html("").animate({'opacity': 0}, 200);
    $('#unitSelect').val('0');
    initEmployeesTab();
    $('#emplList').animate({'opacity': 1}, 200);
});

// LOAD EMPLOYEES TAB WITH TABLE AND EVENT HANDLERS FOR TRs
function initEmployeesTab() {
    // LOAD EMPLOYEES
    $.ajax({
        url: '/ajax.php',
        method: 'post',
        dataType: 'html',
        data: {
            act: "getAllEmpls"
        },
        success: function(json) {
            try {
                let employees = JSON.parse(json);
                initSearch(employees);
                $('#emplList').hide();
                var table = document.getElementById('emplList');
                var tableContent = "";
                tableContent += '<thead class="thead-dark"><tr><th scope="col">Имя</th><th scope="col">Должность</th><th scope="col">Номер</th></tr></thead><tbody>';
                employees.forEach(function(employee) {
                    tableContent += '<tr data-unit="' + employee.unit + '" data-login="' + employee.login + '"><th scope="row">' + employee.emplname + '</th><td>' + employee.position + '</td><td>' + ((employee.phone === null) ? '-' : employee.phone) + '</td></tr>';
                });
                tableContent += "</tbody>";
                table.innerHTML = tableContent;
                $('#emplList').html(tableContent).show();

                // Load info when click on row in tables with employees
                addActionForEmployeeTableRows();
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        }
    });

    // EMPLOYEE DELETION ON CLICK
    $("#emplDelete").on('click', function() {
        deleteEmployee(curEditEmplLogin);
    });
}

// LOAD CARTRIDGES TAB WITH TABLE AND EVENT HANDLERS
function initCartridgesTab() {
    $.ajax({
        url: '/cartridges.php',
        method: 'get',
        dataType: 'html',
        success: function(json) {
            try {
                let cartriges = JSON.parse(json);
                let tableContent = "";
                tableContent += '<thead class="thead-dark"><tr><th scope="col">Производитель</th><th scope="col">Модель</th><th scope="col">Аналоги</th><th scope="col">Кол-во</th></tr></thead><tbody>';
                cartriges.forEach(function(catridge) {
                    tableContent += '<tr class="clickableCart" data-id="' + catridge.name + '"><td>'+ catridge.manufac +'</td><th scope="row">' + catridge.name + '</th><td>' + ((catridge.analogue === null || catridge.analogue === "") ? '-' : catridge.analogue) + '</td><td>'+ catridge.number +'</td></tr>';
                });
                tableContent += "</tbody>";
                $('#cartrList').html(tableContent).animate({'opacity': 1}, 200);

                // Load info when click on row in tables with cartriges
                addActionForRows();
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        }
    });
}

function addActionForRows() {
    $("div#cartrContainer > table > tbody > tr").off('click');
    $("div#cartrContainer > table > tbody > tr").on('click', function(){
        $.ajax({
            url: '/cartridges.php',
            method: 'get',
            dataType: 'html',
            data: {
                id: $(this).attr('data-id')
            },
            success: function(json) {
                try {
                    var data = JSON.parse(json);

                    curCartridgeName = data.name;

                    $('#cartModelName').val(data.name);
                    $('#cartAnalogues').val(data.analogue);
                    $('#cartNumber').val(data.number);
                    $('#manufSelect').val(data.manufac);
                    $('#cartridgeModal').modal('show');
                } catch(err) {
                    console.log(err);
                }
            }
        });
    });
}

//-------------FORM VALIDATOR-------------//

(function() {
    'use strict';
    window.addEventListener('load', function() {
        let forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if (form.checkValidity() === true) {
                    const letters = /^[a-zA-Z0-9 ]*$/gm;
                    const withRussianLetters = /^[a-zA-Z0-9ЁёА-я ]*$/gm;
                    const text = /[^A-Za-z0-9ЁёА-я .'?!,@$#-_\n\r]*$/gm;
                    switch (form.id) {
                        case 'emplForm':
                            if(!user.isAdmin() && !user.isHR()) break;
                            if($('#emplLogin').val().match(letters) && $('#emplPassword').val().match(letters) && $('#emplName').val().match(withRussianLetters) && $('#roleSelect').val().match(letters) && $('#positionSelect').val().match(letters) && $('#statusSelect').val().match(letters) && (($('#emplPhone').val() === '') ? true : $('#emplPhone').val().match(letters))) {
                                $('#emplModal').modal('hide');
                                form.classList.remove('was-validated');
                                saveEmplChanges();
                                return;
                            } else {
                                alert('Проверьте правильность введенных данных!');
                                return;
                            }
                        case 'cartForm':
                            if(!user.isITEmployee() && !user.isAdmin()) break;
                            if($('#cartModelName').val().match(letters) && (($('#cartAnalogues').val() === '') ? true : $('#cartAnalogues').val().match(letters)) && $('#manufSelect').val().match(letters)) {
                                $('#cartridgeModal').modal('hide');
                                form.classList.remove('was-validated');
                                saveCartChanges();
                                return;
                            } else {
                                alert('Проверьте правильность введенных данных!');
                                return;
                            }
                        case 'newsCreationForm':
                            if(!user.isEditor() && !user.isAdmin()) break;
                            if($('#newsTitle').val().match(text) && $('#newsText').val().match(text)) {
                                form.classList.remove('was-validated');
                                createNews($('#newsTitle').val(), $('#newsText').val());
                                $('.addNewsControls').slideUp('fast');
                                $('#newsTitle').val("");
                                $('#newsText').val("");
                                return;
                            } else {
                                alert('Проверьте правильность введенных данных!');
                                return;
                            }
                        case 'newsEditionForm':
                            if(!user.isEditor() && !user.isAdmin()) break;
                            if($('#newsEditTitle').val().match(text) && $('#newsEditText').val().match(text)) {
                                form.classList.remove('was-validated');
                                updateNews($('#newsModal').attr('data-id') , $('#newsEditTitle').val(), $('#newsEditText').val());
                                return;
                            } else {
                                alert('Проверьте правильность введенных данных!');
                                return;
                            }
                    }
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

//------------------NEWS------------------//

function initNews() {
    if(user.isEditor() || user.isAdmin()) {
        $('#newsTitle').on('input', function() {
            $('.addNewsControls').slideDown("fast");
        });

        $('#newsText').on('input', function() {
            if($('#newsTitle').val() === "" && $('#newsText').val() === "") {
                $('.addNewsControls').slideUp("fast");
            }
        });

        $('#newsTitle').on('focusout', function() {
            if($('#newsTitle').val() === "" && $('#newsText').val() === "") {
                $('.addNewsControls').slideUp("fast");
            }
        });
    }

    $.ajax({
        url: '/news.php',
        method: 'get',
        contentType: "application/json",
        success: function(news) {
            try {
                $('#newsList').hide();
                $('#newsList').append(createNewsCards(news)).show();
                addActionForNews();
            } catch(err) {
                console.log(err);
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

// CREATE HTML CARDS FROM JSON
function createNewsCards(newsList) {
    let html = "";
    let count = 1;
    newsList.forEach(function(news) {
        html +=                '<div data-id="' + news.id + '" class="' + ((user.isAdmin() || user.isEditor()) ? 'box': '') + '' + ((count === newsList.length) ? ' mb-5' : '') + ' card ' + ((count > 1) ? 'mt-3' : '') + '">\n' +
            '                       <div class="card-body">\n' +
            '                           <h5 class="card-title">' + news.title + '</h5>\n' +
            '                           <h6 class="card-subtitle mb-2 text-muted">' + news.date + ' / ' + news.author + '</h6>\n' +
            '                           <p class="card-text">' + news.text + '</p>\n' +
            '                       </div>\n' +
            '                   </div>';
        count++;
    });
    return html;
}

// REFRESHES NEWS TAB
function refreshNews() {
    $.ajax({
        url: '/news.php',
        method: 'get',
        contentType: "application/json",
        success: function(news) {
            try {
                if(user.isAdmin() || user.isEditor()) { // CARD EXISTS
                    $('#newsList div.card:not("#newNewsCard")').animate({'opacity': 0}, 200, function () {
                        $('#newNewsCard').nextAll().remove();
                        $(createNewsCards(news)).insertAfter('#newNewsCard');
                        $('#newsList div.card:not("#newNewsCard")').animate({'opacity': 1}, 200);
                        addActionForNews();
                    });
                } else { // CARDS DOESN'T EXIST
                    $('#newsList').html("");
                    $('#newsList').html(createNewsCards(news));
                }
                addActionForNews();
            } catch(err) {
                console.log(err);
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

// CREATE NEWS
function createNews(_title, _text) {
    $.ajax({
        url: '/news.php',
        method: 'post',
        contentType: "application/json",
        data: JSON.stringify({ "title": _title, "text": _text}),
        success: function(data) {
            try {
                if(data.rowsAffected === 1) {
                    addActionForNews();
                    showAlert("Новость успешно создана!",'success', 3000);
                    refreshNews();
                } else {
                    alert(data);
                }
            } catch(err) {
                console.log(err);
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

// UPDATE NEWS
function updateNews(_id, _title, _text) {
    $.ajax({
        url: '/news.php',
        method: 'put',
        contentType: "application/json",
        data: JSON.stringify({
            "id": _id,
            "title": _title,
            "text": _text
        }),
        success: function(data) {
            try {
                if(data.rowsAffected === 1 || data.rowsAffected === 0) {
                    refreshNews();
                    $('#newsModal').modal('hide');
                } else {
                    alert(data);
                }
            } catch(err) {
                console.log(err);
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

function deleteNews(_id) {
    $.ajax({
        url: '/news.php',
        method: 'delete',
        contentType: "application/json",
        data: JSON.stringify({'id': _id}),
        success: function(data) {
            try {
                if(data.rowsAffected === 1) {
                    $('#newsModal').modal('hide');
                    $('#newsList div.card[data-id=' + $('#newsModal').attr('data-id') + ']').children().slideUp("fast", function () {
                        $('#newsList div.card[data-id=' + $('#newsModal').attr('data-id') + ']').remove();
                    });
                } else {
                    alert(data);
                }
            } catch(err) {
                if(err instanceof SyntaxError) {
                    alert(json);
                }
            }
        },
        error: function(data) {
            console.log(data.responseJSON);
        }
    });
}

$('#newsDelete').on('click', function () {
    deleteNews($('#newsModal').attr('data-id'));
});

// MAKE NEWS CLICKABLE FOR EDITOR
function addActionForNews() {
    $('#newsList div.card:not("#newNewsCard")').on('click', function() {
        $.ajax({
            url: '/news.php',
            method: 'get',
            contentType: "application/json",
            data: {
                id: $(this).attr('data-id')
            },
            newsId: $(this).attr('data-id'),
            success: function(news) {
                try {
                    $('#newsEditTitle').val(news.title);
                    $('#newsEditText').val(news.text);
                    $('#newsModal').attr('data-id', this.newsId);
                    $('#newsModal').modal('show');
                } catch(err) {
                    console.log(err);
                }
            },
            error: function(data) {
                console.log(data.responseJSON);
            }
        });
    });
}

//--------------NAVIGATION----------------//

$('.nav-pills a[href="#metrics"]').on('click', function(){
    location.href = './metrics.php';
})