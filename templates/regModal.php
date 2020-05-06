<div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-15">
            <form id="regForm" class="needs-validation" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalTitle">Регистрация</h5>
                </div>
                <div class="modal-body table-responsive w-100">
                    <div id="errHolderReg"></div>
                    <div class="form-group">
                        <label for="regLogin">Логин</label>
                        <input name="email" type="text" maxlength="45" class="form-control" id="regLogin" placeholder="Введите ваш e-mail" autocomplete="home username email" required>
                        <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                    </div>
                    <div class="form-group">
                        <label for="regPassword">Пароль</label>
                        <input name="password" type="password" maxlength="45" class="form-control" id="regPassword" placeholder="Введите пароль" autocomplete="new-password" required>
                        <div class="invalid-feedback feedbackPwdText">Пожалуйста, заполните это поле</div>
                    </div>
                    <div class="form-group">
                        <input type="password" maxlength="45" class="form-control" id="regPasswordRepeat" placeholder="Повторите пароль" required>
                        <div class="invalid-feedback feedbackPwdText">Пожалуйста, заполните это поле</div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label for="regLast">Фамилия</label>
                            <input type="text" maxlength="100" class="form-control" id="regLast" placeholder="Иванов" required>
                            <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                        </div>
                        <div class="col">
                            <label for="regFirst">Имя</label>
                            <input type="text" maxlength="100" class="form-control" id="regFirst" placeholder="Иван" autocomplete="name" required>
                            <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="regMiddle">Отчество (при наличии)</label>
                        <input type="text" maxlength="100" class="form-control" id="regMiddle" placeholder="Иванов">
                        <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Назад</button>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    </div>
</div>