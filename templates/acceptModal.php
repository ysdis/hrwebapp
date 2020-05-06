<div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="loginModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-15">
            <form id="loginForm" class="needs-validation" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalTitle">Вход</h5>
                </div>
                <div class="modal-body table-responsive w-100">
                    <div id="errHolder"></div>
                    <div class="form-group">
                        <label for="userLogin">Логин</label>
                        <input name="email" type="text" maxlength="45" class="form-control" id="userLogin" placeholder="Введите e-mail" autocomplete="home username email" required>
                        <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Пароль</label>
                        <input name="password" type="password" maxlength="45" class="form-control" id="userPassword" placeholder="Введите пароль" autocomplete="password" required>
                        <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Назад</button>
                    <button type="submit" class="btn btn-primary">Войти</button>
                </div>
            </form>
        </div>
    </div>
</div>