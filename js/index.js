let user = new User(
    currentUserLogin,
    function () {
        // ИНИЦИАЛИЗАЦИЯ АВТОРИЗИРОВАННОЙ СТРАНИЦЫ
        $('#tabHeader').html(`Добро пожаловать, ${user._firstName} ${user._middleName}`);
        $('#loginBtn').hide();
        $('#regBtn').hide();

        $('#logoutBtn').show();
        $('#logoutBtn').on('click', function () {
            showPreloader(null, function () {
                window.location.href = "./logout.php";
            });
        });
    },
    function (data) {
        if(user.getLogin !== undefined) {
            showAlert("Что-то пошло не так! Cайт не может продолжить работу, попробуйте обновить страницу");
            console.log(data);
        }
    });

//------------------ACCOUNT CONTROL------------------//

function login() {
    showPreloader();
    ajax(
        "./login.php",
        "POST",
        JSON.stringify({'login': $('#userLogin').val(), 'password': $('#userPassword').val()}),
        function () {
            window.location.href = "./controlPanel.php";
        },
        function (data) {
            $('#userPassword').val('');
            showAlert(data.responseJSON.message, 'danger', 0, $('#errHolder'), true);
            hidePreloader();
        }
    );
}

function register() {
    showPreloader();
    ajax(
        "./control/users.php",
        "POST",
        JSON.stringify({
            'login': $('#regLogin').val(),
            'password': $('#regPassword').val(),
            'lastName': $('#regLast').val(),
            'firstName': $('#regFirst').val(),
            'middleName': $('#regMiddle').val()
        }),
        function () {
            window.location.href = "./controlPanel.php";
        },
        function (data) {
            $('#regPassword').val('');
            $('#regPasswordRepeat').val('');
            showAlert(data.responseJSON.message, 'danger', 0, $('#errHolderReg'), true);
            hidePreloader();
        }
    );
}

//---------------------FORM VALIDATOR---------------------//

(function() {
    'use strict';
    // ОБРАБОТЧИК НАЖАТИЯ КНОПКИ ОТПРАВКИ ФОРМ
    window.addEventListener('load', function() {
        let forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if (form.checkValidity() === true) {
                    switch (form.id) {
                        case 'loginForm':
                            if($('#userLogin').val().match(emailRegExp) && $('#userPassword').val().match(lettersR)) {
                                form.classList.remove('was-validated');
                                login();
                                return;
                            } else {
                                showAlert('Проверьте правильность введённых данных!', 'danger', 0, $('#errHolder'), true);
                                return;
                            }
                        case 'regForm':
                            if( $('#regLogin').val().match(emailRegExp) &&
                                $('#regPassword').val().match(lettersR) &&
                                $('#regPasswordRepeat').val().match(lettersR) &&
                                $('#regLast').val().match(lettersWithRussianR) &&
                                $('#regFirst').val().match(lettersWithRussianR) &&
                                $('#regMiddle').val().match(lettersWithRussianR)
                            ) {
                                if($('#regPassword').val() !== $('#regPasswordRepeat').val()) {
                                    let pwdInputs = $('#regForm :input[type="password"]');
                                    pwdInputs.val('');

                                    form.classList.add('was-validated');
                                    showAlert('Пароли не сопадают!', 'danger', 0, $('#errHolderReg'), true);
                                    return;
                                }
                                register();

                                form.classList.remove('was-validated');
                                return;
                            } else {
                                showAlert('Проверьте правильность введённых данных!', 'danger', 0, $('#errHolderReg'), true);
                                return;
                            }
                    }
                }
                form.classList.add('was-validated');
            }, false);
        });

        // ОБРАБОТЧИК ДЛЯ КНОПОК ОТМЕНЫ В МОДАЛЬНЫХ ОКНАХ ФОРМ
        $('.modal').on('hidden.bs.modal', function (e) {
            let inputs = $(this).find(':input.form-control');
            inputs.each(function () {
                $(this).val('');
            })
        })

        // ПЕРВОНАЧАЛЬНАЯ ЗАГРУЗКА
        loadVacancies();
    }, false);
})();

function loadVacancies() {

}