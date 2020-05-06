let questionsContainer = $('#questionsCardsHolder');
let questionLinksContainer = $('#questionLinksHolder');
let questionCount = 1;

const defaultSingleOptions = 2;
const defaultMultiOptions = 3;

const questionValuable = '1';
const questionInvaluable = '2';

const answerSingle = '1';
const answerMulti = '2';
const answerText = '3';

let optionIsCorrectItem = function(answerType) {
    return `<div class="input-group-prepend"><div class="input-group-text">
        <input type="${(answerType === questionValuable) ? 'radio': 'checkbox'}" name="options" checked>
    </div></div>`;
}

let optionItem = function (answerType, questionType) {
    return `<div class="input-group">
            ${(questionType === questionValuable) ?
                `<div class="input-group-prepend"><div class="input-group-text">
                    <input type="${(answerType === questionValuable) ? 'radio': 'checkbox'}" name="options" checked>
                </div></div>` : ""}
                <input type="text" name="optionText" class="form-control" maxlength="1000" placeholder="Текст варианта ответа">
                <div class="col-1 px-0 mx-0 my-auto">
                    <a type="button" class="pl-3 close float-left deleteOption"><span aria-hidden="true">&times;</span></a>
                </div>
            </div>`;
}

let textOption = `<input type="text" name="textOption" class="form-control" maxlength="1000" placeholder="Ответ на вопрос">`;

let questionLink = function(index) {
    return `<a href="#question${index}" class="col-12 p-3 my-2 rounded-15 border border-info text-center">
                <span>Вопрос ${index}</span>
            </a>`;
}

let questionBody = function(index) {
    return `<div id="question${index}" class="questionCard p-4">
                <h4 class="mb-3">Вопрос ${index}</h4>
                <div class="form-row">
                    <div class="form-group col-md-10 order-2 order-md-1">
                        <input name="questionText" type="text" class="form-control" maxlength="1000" placeholder="Текст вопроса">
                    </div>
                    <div class="form-group col-md-2 order-1 order-md-2">
                        <button class="btn btn-outline-danger float-right" name="deleteQuestion">Удалить вопрос</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12 col-md-4">
                        <div class="form-group col-12 p-0 mb-2">Тип вопроса:</div>
                        <select class="form-control" name="questionType">
                            <option value="1">Оцениваемый</option>
                            <option value="2">Неоцениваемый</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <div class="form-group col-12 p-0 mb-2">Тип ответа:</div>
                        <select class="form-control" name="answerType">
                            <option value="1">Одиночный ответ</option>
                            <option value="2">Множественный ответ</option>
                            <option value="3">Письменный ответ</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <div class="form-group col-12 p-0 mb-2">Категория вопроса:</div>
                        <select class="form-control" name="questionCategory"></select>
                    </div>
                </div>
                <div class="form-row optionsHolder">
                    <div class="form-group col-12 mx-0 mb-0 grid-box-fill-parent"></div>
                    <div class="col-12 p-3 mt-3 rounded-15 border border-info addOptionBtn foggy">
                        <span>Добавить вариант</span>
                    </div>
                </div>
            </div>`;
}

function getOptionByAnswerType(answerType, questionType) {
    return (answerType === answerText) ? textOption : optionItem(answerType, questionType);
}

function createOptions(answerType, questionType) {
    let i;
    let count = (answerType === questionValuable) ? defaultSingleOptions : defaultMultiOptions;
    let options = "";
    for(i = 0; i < count; i++) {
        options += getOptionByAnswerType(
            answerType,
            questionType
        );
    }
    return options;
}

// Кнопка "Добавить вопрос"
$('#addQuestionBtn').on('click', function () {
    questionLinksContainer.find('#addQuestionBtn').before(questionLink(questionCount))
    questionsContainer.append(questionBody(questionCount))

    let lastQuestionCard = questionsContainer.find('.questionCard:last')
    let curQuestionOptionsHolder = lastQuestionCard.find('.optionsHolder .form-group')
    let answerTypeSelect = lastQuestionCard.find('select[name="answerType"]')
    let questionTypeSelect = lastQuestionCard.find('select[name="questionType"]')

    ajax(
        "./control/questionCategories.php",
        "GET",
        null,
        function(data) {
            data.forEach(function (item) {
                lastQuestionCard.find('select[name="questionCategory"]').append(`<option value="${item.id}">${item.title}</option>`)
            })
        }
    )

    curQuestionOptionsHolder.append(createOptions(answerTypeSelect.val(), questionTypeSelect.val()))

    //-----------------------------------------------------------------//

    // Сохранение предыдущего типа вопроса
    questionTypeSelect.on('focusin', function(){
        $(this).data('prev-val', $(this).val())
    });

    // Когда тип ответа изменяется
    questionTypeSelect.on('change', function () {
        let card = $(this).parent().parent().parent() // Ссылка на вопрос целиком из контекста нажатия
        let optionsHolder = card.find('.optionsHolder .form-group')
        let answerType = card.find('select[name="answerType"]').val()
        let prevType = $(this).data('prev-val')
        let currentType = $(this).val()

        if(prevType !== currentType) { // Когда тип вопроса изменился
            if(currentType === questionValuable) { // Когда вопрос оцениваемый
                if(answerType === answerText) {
                    optionsHolder.prepend(textOption)
                } else {
                    card.find('.input-group').each(function() {
                        $(this).prepend(optionIsCorrectItem(card.find('select[name="answerType"]').val()))
                    });
                }
            } else { // Когда вопрос неоцениваемый
                if(answerType === answerText) {
                    optionsHolder.html("")
                } else {
                    optionsHolder.find('.input-group-prepend').remove()
                }
            }
        }

        $(this).data('prev-val', currentType)
    });

    //-----------------------------------------------------------------//

    // Сохранение предыдущего типа ответа
    answerTypeSelect.on('focusin', function(){
        $(this).data('prev-val', $(this).val())
    });

    // Когда тип ответа изменяется
    answerTypeSelect.on('change', function () {
        let card = $(this).parent().parent().parent() // Ссылка на вопрос целиком из контекста нажатия
        let optionsHolder = card.find('.optionsHolder .form-group')
        let prevType = $(this).data('prev-val')
        let currentType = $(this).val()

        if(prevType === answerText && currentType < answerText) { // Когда был письменный, а стал одиночный и множетсвенный
            card.find('.addOptionBtn').show()

            card.find('.optionsHolder .form-group').html("")
            optionsHolder.append(createOptions(currentType, card.find('select[name="questionType"]').val()));
        } else if(prevType < answerText && currentType < answerText) { // Когда был одиночный или множественный и стал опять одним из них
            card.find('.addOptionBtn').show()

            card.find('.input-group').each(function() {
                optionsHolder.find('.input-group-prepend input[name="options"]').attr("type", (currentType === questionValuable) ? 'radio': 'checkbox');
            });
        } else if(currentType === answerText && prevType < answerText) { // Когда стал письменным
            // TODO: Подтверждение удаления всех вариантов ответа в вопросе
            card.find('.addOptionBtn').hide()

            optionsHolder.html("")
            if(card.find('select[name="questionType"]').val() === questionValuable) {
                optionsHolder.append(textOption)
            }
        }

        $(this).data('prev-val', currentType)
    });

    // Кнопка "Добавить вариант"
    lastQuestionCard.find('.addOptionBtn').on('click', function() {
        $(this).parent().find('.form-group .input-group:last').after(getOptionByAnswerType(
            $(this).parent().parent().find('select[name="answerType"]').val(),
            $(this).parent().parent().find('select[name="questionType"]').val()
        ))
    })

    // Кнопка "Удалить вопрос"
    lastQuestionCard.find('button[name="deleteQuestion"]').on('click', function() {
        // TODO: Подтверждение удаления вопроса
        let idOfQuestion = $(this).parent().parent().parent().attr('id')
        $(`#${idOfQuestion}`).slideUp(function () {
            $(this).remove()
        });
        $(`a[href="#${idOfQuestion}"]`).remove()
        // TODO: ПЕРЕСЧЁТ НОМЕРОВ КАРТОЧЕК
    });

    // Кнопка "Удалить вариант"
    lastQuestionCard.find('a[type="button"]').on('click', function () {
        let optionsCount = $(this).parent().parent().parent().find('.input-group').length
        let answerType = $(this).parent().parent().parent().parent().parent().find('select[name="answerType"]').val()
        if((answerType === answerSingle && optionsCount > 2) || (answerType === answerMulti && optionsCount > 1)) {
            $(this).parent().parent().remove()
        }

    });

    questionCount++;
});

$(window).on('load', function () {
    $('#addQuestionBtn').click();
});

//-----------------------------------------------------------------//

function parseQuestion(questionContent) {
    let question = new Question();
    question.title = questionContent.find('input[name="questionText"]').val();
    question.questionType = questionContent.find('select[name="questionType"]').val();
    question.answerType = questionContent.find('select[name="answerType"]').val();
    question.questionCategory = questionContent.find('select[name="questionCategory"]').val();

    let optionsList  = []
    questionContent.find('.input-group').each(function () {
        let option = new Option();
        option.content = $(this).find('input[name="optionText"]').val();
        option.isCorrect = $(this).find('input[name="options"]').prop("checked");

        optionsList.push(option)
    })

    question.options = optionsList

    return question
}

$('#saveFormBtn').on('click', function () {
    let loadingInfoHolder = showPreloader("Ожидайте, выполняется загрузка формы...")

    let form = new Form()
    let questionsList = []

    questionsContainer.find('.questionCard').each(function () {
        questionsList.push(parseQuestion($(this)))
    })
    form.questions = questionsList
    form.title = $('#formNameInput').val()
    form.specialtyId = $('#specialtySelect').val()
    form.typeId = $('#formTypeSelect').val()

    ajax(
        "./control/forms.php",
        "POST",
        JSON.stringify(form),
        function (data) {
            showAlert(data.message, "success", 4000, loadingInfoHolder, true)
            setTimeout(function() {
                window.location.href = "./controlPanel.php"
            }, 4000);
        },
        function (data) {
            showAlert(data.message, "danger", 3000, loadingInfoHolder, true)
            setTimeout(function() {
                hidePreloader();
            }, 3000);
        }
    )
})

class Form {
    constructor() {
        this.id
        this.title
        this.specialtyId
        this.typeId
        this.isActive
        this.questions
    }
}

class Question {
    constructor() {
        this.id
        this.title
        this.questionType
        this.answerType
        this.questionCategory
        this.options
    }
}

class Option {
    constructor() {
        this.id
        this.questionId
        this.content
        this.isCorrect
    }
}