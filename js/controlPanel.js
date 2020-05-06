let user = new User(
    currentUserLogin,
    function () {
        // ИНИЦИАЛИЗАЦИЯ АВТОРИЗИРОВАННОЙ СТРАНИЦЫ
        $('#tabHeader').html(`Добро пожаловать, ${user._firstName} ${user._middleName}`);
        $('#loginBtn').hide();
        $('#regBtn').hide();

        $('#logoutBtn').show();
        $('#logoutBtn').on('click', function () {
            showPreloader(function () {
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

function createApplyCard(fullName, vacancyName, progress, applyDate, imgPath = null) {
    return  `<div class="applyCard mt-3 shadow-lg">
                        <div class="row">
                            <div class="col-md-1 my-auto text-center">
                                <img width="40px" src="${(imgPath === null) ? './img/blank-avatar.jpg' : imgPath}" class="rounded-circle" alt="Фото соискателя">
                            </div>
                            <div class="col-md-2 my-auto pt-2 pt-md-9">
                                <span>${fullName}</span>
                            </div>
                            <div class="col-md-4 my-auto pt-2 pt-md-9">
                                <span>${vacancyName}</span>
                            </div>
                            <div class="col-md-3 my-auto pt-2 pt-md-9">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-md-2 my-auto pt-2 pt-md-9">
                                <span>${applyDate}</span>
                            </div>
                        </div>
                    </div>`;
}

(function() {
    'use strict';
    // ОБРАБОТЧИК НАЖАТИЯ КНОПКИ ОТПРАВКИ ФОРМ
    window.addEventListener('load', function() {

        // ПЕРВОНАЧАЛЬНАЯ ЗАГРУЗКА
        loadApplies();
    }, false);
})();