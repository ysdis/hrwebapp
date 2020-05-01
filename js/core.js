const numbersR = /^[0-9]*$/gm;
const lettersR = /^[a-zA-Z0-9 ]*$/gm;
const lettersWithRussianR = /^[a-zA-Z0-9ЁёА-я ]*$/gm;
const textareaLettersR = /[^A-Za-z0-9ЁёА-я .'?!,@$#-_\n\r]*$/gm;

//-------------TABLE GENERATOR------------//

function createTable(headers = [],data, container = null, placeholder = '-') {
    let html = '<thead class="thead-light"><tr>';
    headers.forEach(function (name) {
    html += `<th scope="col">${name}</th>`;
    })
    html += '</tr></thead><tbody>';
    data.forEach(function (row) {
        html += '<tr>'
        Object.keys(row).forEach(function(key) {
            html += `<td>${(this[key] == null) ? placeholder : this[key]}</td>`;
        }, row);
        html += '</tr>';
    })
    html += '</tbody>';
    container.html(html);
    return html;
}

//----------------PRELOADER---------------//

(function() {
    'use strict';
    window.addEventListener('load', function() {
        $('#preloader').animate({'opacity': 0}, 200, function () {
            $('#preloader').hide();
            $('body').removeClass("preloader-site");
        });
    }, false);
})();

//------------------ALERT-----------------//

function showAlert(text, type = "danger", durationMills = 0) {
    if(!$('#alertContainer').length) {
        $('body').append('<div id="alertContainer" class="footer"></div>');
    }
    $('#alertContainer').append('<div class="alert alert-' + type + ' alert-dismissible fade show mx-3 mb-3" role="alert">' +
                                    text +
                                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                        '<span aria-hidden="true">&times;</span>' +
                                    '</button>' +
                                '</div>');
    let alertLink = $('#alertContainer .alert:last').alert();
    if(durationMills === 0) {
        return alertLink;
    } else {
        setTimeout(function() {
            alertLink.alert('close');
        }, durationMills);
    }
}

//------------------AJAX------------------//

function ajax(url,
              method,
              data = {},
              callbackSuccess = null,
              callbackError = function (data) {showAlert(data.responseJSON, 'danger', 3000);}) {
    $.ajax({
        url: url,
        method: method,
        data: data,
        contentType: 'application/json',
        success: function(data) {
            callbackSuccess(data);
        },
        error: function(data) {
            callbackError(data);
        }
    });
}

//-----------------SEARCH-----------------//

var searchList;
function initSearch(list) {
    searchList = list;

    $('#searchInput').on('input', function () {
        if($('#searchInput').val() === "") {
            $('.dropdown-menu').dropdown('hide');
        } else {
            $('.dropdown-menu').dropdown('show');
            makeSuggestion($(this).val());
        }
    });

    $('#searchInput').on('focusout', function () {
        $('.dropdown-menu').dropdown('hide');
    });

    $('#searchInput').on('focus', function () {
        if($('#searchInput').val() !== "") {
            $('.dropdown-menu').dropdown('show');
        }
    });
}

function createSuggestionItemHTML(title, extra) {
    return  '<h6 class="dropdown-header">' + title + '</h6>' +
        '    <p class="mb-0 px-4 py-1">' + ((extra === null) ? "-" : extra) + '</p> ';
}

function createSuggestionListHTML(list) {
    let htmlList = "";
    let counter = 1;

    if(list.length === 0) {
        return '<p class="mb-0 px-4 py-1">Ничего не найдено</p>';
    }

    list.forEach(function (item) {
        htmlList += createSuggestionItemHTML(item.title, item.extra);
        if(counter < list.length) {
            htmlList += '<div class="dropdown-divider"></div>';
        }
        counter++;
    })

    return htmlList;
}

function makeSuggestion(query) {
    let selectedItems = [];
    searchList.forEach(function (item) {
        if(item.emplname.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
            selectedItems.push({"title": item.emplname, "extra": item.phone});
        }
    })
    $('#searchResult').html(createSuggestionListHTML(selectedItems));
}

//------------------USER------------------//

// ROLES IDs
const USER = 1;
const EDITOR = 2;
const ITEMPL = 3;
const ADMIN = 4;
const HR = 5;

class User {
    constructor(login, callbackSuccess, callbackFail) {
        this._login = login;
        this._roleId = 0;
        this._fullName = "";
        this._callbackSuccess = callbackSuccess;
        this._callbackFail = callbackFail;
        this.setRole();
    }

    setFullName() {
        var self = this;
        $.ajax({
            url: './user.php',
            method: 'get',
            contentType: 'application/json',
            data: {
                login: this._login
            },
            success: function(data) {
                self._fullName = data.name;
                self._callbackSuccess();
            },
            error: function(data) {
                self._callbackFail(data);
            }
        });
    }

    setRole() {
        var self = this;
        $.ajax({
            url: './ajax.php',
            method: 'post',
            dataType: 'html',
            data: {
                act: "getRoleId"
            },
            success: function(data) {
                self._roleId = parseInt(data);
                self.setFullName();
            },
            error: function(data) {
                self._callbackFail(data);
            }
        });
    }

    get roleId() {
        return this._roleId;
    }

    get getLogin() {
        return this._login;
    }

    isAdmin() {
        return (this._roleId === ADMIN);
    }

    isITEmployee() {
        return (this._roleId === ITEMPL);
    }

    isEditor() {
        return (this._roleId === EDITOR);
    }

    isUser() {
        return (this._roleId === USER);
    }

    isHR() {
        return (this._roleId === HR);
    }
}