-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema hrwebapp
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `hrwebapp` ;

-- -----------------------------------------------------
-- Schema hrwebapp
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `hrwebapp` DEFAULT CHARACTER SET utf8 ;
USE `hrwebapp` ;

-- -----------------------------------------------------
-- Table `hrwebapp`.`specialties`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`specialties` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`specialties` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(144) NOT NULL,
  `description` TEXT NOT NULL,
  `isOpen` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`userRoles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`userRoles` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`userRoles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`users` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`users` (
  `login` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `lastName` VARCHAR(100) NOT NULL,
  `firstName` VARCHAR(100) NOT NULL,
  `middleName` VARCHAR(100) NOT NULL DEFAULT '-',
  `specialtyId` INT NULL,
  `roleId` INT NOT NULL DEFAULT 1,
  `isActive` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`login`),
  CONSTRAINT `specKey`
    FOREIGN KEY (`specialtyId`)
    REFERENCES `hrwebapp`.`specialties` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `roleKey`
    FOREIGN KEY (`roleId`)
    REFERENCES `hrwebapp`.`userRoles` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`formTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`formTypes` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`formTypes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`forms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`forms` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`forms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(144) NOT NULL,
  `specialtyId` INT NOT NULL,
  `typeId` INT NOT NULL,
  `isActive` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `specFormKey`
    FOREIGN KEY (`specialtyId`)
    REFERENCES `hrwebapp`.`specialties` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `FormTypeKey`
    FOREIGN KEY (`typeId`)
    REFERENCES `hrwebapp`.`formTypes` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`questionTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`questionTypes` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`questionTypes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`questionCategories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`questionCategories` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`questionCategories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`answerTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`answerTypes` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`answerTypes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`questions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`questions` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`questions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `formId` INT NOT NULL,
  `title` VARCHAR(1000) NOT NULL,
  `image` VARCHAR(200) NULL COMMENT 'Path to an image on the server',
  `questionType` INT NOT NULL,
  `answerType` INT NOT NULL,
  `questionCategory` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `formKey`
    FOREIGN KEY (`formId`)
    REFERENCES `hrwebapp`.`forms` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `typeKey`
    FOREIGN KEY (`questionType`)
    REFERENCES `hrwebapp`.`questionTypes` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `categoryKey`
    FOREIGN KEY (`questionCategory`)
    REFERENCES `hrwebapp`.`questionCategories` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `answerTypeKey`
    FOREIGN KEY (`answerType`)
    REFERENCES `hrwebapp`.`answerTypes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`applicationStatuses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`applicationStatuses` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`applicationStatuses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`applications` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`applications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `applicantLogin` VARCHAR(36) NOT NULL,
  `formId` INT NOT NULL,
  `score` INT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statusId` INT NOT NULL DEFAULT 1,
  `lastModified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `userKey`
    FOREIGN KEY (`applicantLogin`)
    REFERENCES `hrwebapp`.`users` (`login`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `formApplicationKey`
    FOREIGN KEY (`formId`)
    REFERENCES `hrwebapp`.`forms` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `statusKey`
    FOREIGN KEY (`statusId`)
    REFERENCES `hrwebapp`.`applicationStatuses` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`options`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`options` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`options` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `questionId` INT NOT NULL,
  `content` VARCHAR(1000) NOT NULL,
  `isCorrect` TINYINT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `questionKey`
    FOREIGN KEY (`questionId`)
    REFERENCES `hrwebapp`.`questions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hrwebapp`.`userAnswers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hrwebapp`.`userAnswers` ;

CREATE TABLE IF NOT EXISTS `hrwebapp`.`userAnswers` (
  `id` INT ZEROFILL NOT NULL AUTO_INCREMENT,
  `applicationId` INT NOT NULL,
  `questionId` INT NOT NULL,
  `optionId` INT NULL,
  `textAnswer` VARCHAR(1000) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `questionAnswerKey`
    FOREIGN KEY (`questionId`)
    REFERENCES `hrwebapp`.`questions` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `applicationKey`
    FOREIGN KEY (`applicationId`)
    REFERENCES `hrwebapp`.`applications` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `optionKey`
    FOREIGN KEY (`optionId`)
    REFERENCES `hrwebapp`.`options` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;

USE `hrwebapp`;

DELIMITER $$

USE `hrwebapp`$$
DROP TRIGGER IF EXISTS `hrwebapp`.`forms_BEFORE_INSERT` $$
USE `hrwebapp`$$
CREATE DEFINER = CURRENT_USER TRIGGER `forms_BEFORE_INSERT` BEFORE INSERT ON `forms` FOR EACH ROW
BEGIN
	DECLARE specialtyStatus TINYINT;
    SELECT isOpen INTO specialtyStatus FROM specialties WHERE id = NEW.specialtyId;
    
    IF specialtyStatus = 0 THEN -- Когда должность закрыта
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Невозможность создать форму для закрытой должности!';
    END IF;
END$$


USE `hrwebapp`$$
DROP TRIGGER IF EXISTS `hrwebapp`.`applications_BEFORE_INSERT` $$
USE `hrwebapp`$$
CREATE DEFINER = CURRENT_USER TRIGGER `applications_BEFORE_INSERT` BEFORE INSERT ON `applications` FOR EACH ROW
BEGIN
	DECLARE isFormActive TINYINT;
    SELECT isActive INTO isFormActive FROM forms WHERE id = NEW.formId;
    
    IF isFormActive != 1 THEN -- Когда опрос закрыт для прохождения
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Невозможно пройти опрос, так как он завершён';
    END IF;
END$$


USE `hrwebapp`$$
DROP TRIGGER IF EXISTS `hrwebapp`.`applications_BEFORE_UPDATE` $$
USE `hrwebapp`$$
CREATE DEFINER = CURRENT_USER TRIGGER `applications_BEFORE_UPDATE` BEFORE UPDATE ON `applications` FOR EACH ROW
BEGIN
	IF NEW.statusId != OLD.statusId and OLD.statusId > 1 THEN -- Когда статус изменился и статут не "Обрабатывается"
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Невозможно изменять статус заявки после её завершения или отклонения!';
    END IF;
END$$


USE `hrwebapp`$$
DROP TRIGGER IF EXISTS `hrwebapp`.`options_BEFORE_INSERT` $$
USE `hrwebapp`$$
CREATE DEFINER = CURRENT_USER TRIGGER `hrwebapp`.`options_BEFORE_INSERT` BEFORE INSERT ON `options` FOR EACH ROW
BEGIN
	DECLARE correctOptions INT;
    DECLARE currentQuestionType INT;
    DECLARE currentAnswerType INT;
    DECLARE numberOfOptions INT;
    
    SELECT COUNT(*) INTO correctOptions FROM options WHERE questionId = NEW.questionId and isCorrect = 1;
    SELECT questionType INTO currentQuestionType FROM questions WHERE id = NEW.questionId; -- Оцениваемый или нет
    SELECT COUNT(*) INTO numberOfOptions  FROM options WHERE questionId = NEW.questionId;
    SELECT answerType INTO currentAnswerType FROM questions WHERE id = NEW.questionId; -- Одинарный, множественный, письменный
    
    IF currentQuestionType = 1 and NEW.isCorrect IS NULL and currentAnswerType < 3 THEN -- Если тип ответа "Оцениваемый" и правильность не задана
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Оцениваемые вопросы должны содержать варианты ответы дифференцированные на неправильные и верные!';
    END IF;
    
    IF currentAnswerType = 1 THEN -- Тип вопроса "Одинарный ответ"
		IF correctOptions > 0 THEN -- Когда правильных ответов больше нуля
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'В вопросе с одинарным ответом не может быть более одно правильного варианта ответа!';
        END IF;
    END IF;
    
	IF currentAnswerType = 3 THEN -- Тип вопроса "Письменный ответ"
		IF numberOfOptions > 0 THEN -- Когда вариантов ответа больше нуля
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'В вопросе с письменным ответом не может быть более одно варианта ответа!';
        ELSE
			SET NEW.isCorrect = 1; -- Единственный вариант ответа в письменном вопросе считается правильным
        END IF;
    END IF;
END$$


USE `hrwebapp`$$
DROP TRIGGER IF EXISTS `hrwebapp`.`userAnswers_BEFORE_INSERT` $$
USE `hrwebapp`$$
CREATE DEFINER = CURRENT_USER TRIGGER `hrwebapp`.`userAnswers_BEFORE_INSERT` BEFORE INSERT ON `userAnswers` FOR EACH ROW
BEGIN
	DECLARE currentAnswerType INT;
    
    SELECT answerType INTO currentAnswerType FROM questions WHERE id = NEW.questionId;
    
    IF currentAnswerType > 3 and NEW.textAnswer IS NULL THEN -- Когда "Письменный ответ" и когда он пустой
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Письменный ответ не может быть пустым!';
	ELSE 
		IF NEW.optionId IS NULL THEN -- Когда "Одинарный" или "Множественный" ответ и когда он пустой
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Одинарный и/или Множественный ответ не может быть пустым!';
		END IF;
    END IF;
END$$


DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `hrwebapp`.`specialties`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`specialties` (`id`, `name`, `description`, `isOpen`) VALUES (1, 'Аналитик (Управляющий проектами)', '-', DEFAULT);
INSERT INTO `hrwebapp`.`specialties` (`id`, `name`, `description`, `isOpen`) VALUES (2, 'Тестировщик', '-', DEFAULT);
INSERT INTO `hrwebapp`.`specialties` (`id`, `name`, `description`, `isOpen`) VALUES (3, 'Офис-менеджер', '-', DEFAULT);
INSERT INTO `hrwebapp`.`specialties` (`id`, `name`, `description`, `isOpen`) VALUES (4, 'Старший программист', '-', DEFAULT);
INSERT INTO `hrwebapp`.`specialties` (`id`, `name`, `description`, `isOpen`) VALUES (5, 'Младший программист', '-', DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`userRoles`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`userRoles` (`id`, `title`) VALUES (1, 'Соискатель');
INSERT INTO `hrwebapp`.`userRoles` (`id`, `title`) VALUES (2, 'Сотрудник');
INSERT INTO `hrwebapp`.`userRoles` (`id`, `title`) VALUES (3, 'Администратор');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`users`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`users` (`login`, `password`, `lastName`, `firstName`, `middleName`, `specialtyId`, `roleId`, `isActive`) VALUES ('olgol', '$2y$10$KAxR8ZrK/BC3IOhWIW1m9OcMMOmTXJb6V2hJkzEt/ubsSVdOJubki', 'Свиридов', 'Михаил', 'Павлович', 4, 1, DEFAULT);
INSERT INTO `hrwebapp`.`users` (`login`, `password`, `lastName`, `firstName`, `middleName`, `specialtyId`, `roleId`, `isActive`) VALUES ('mulgul', '$2y$10$KAxR8ZrK/BC3IOhWIW1m9OcMMOmTXJb6V2hJkzEt/ubsSVdOJubki', 'Кочанова', 'Ольга', 'Викторовна', 1, 1, DEFAULT);
INSERT INTO `hrwebapp`.`users` (`login`, `password`, `lastName`, `firstName`, `middleName`, `specialtyId`, `roleId`, `isActive`) VALUES ('test@test.com', '$2y$10$KAxR8ZrK/BC3IOhWIW1m9OcMMOmTXJb6V2hJkzEt/ubsSVdOJubki', 'Идиотов', ' Кирилл', ' Николаевич', 5, 1, DEFAULT);
INSERT INTO `hrwebapp`.`users` (`login`, `password`, `lastName`, `firstName`, `middleName`, `specialtyId`, `roleId`, `isActive`) VALUES ('admin@admin.com', '$2y$2y$10$G2BG/Cmw17qjoTafssT28u1f3Qe.RI4MCgd1xoy.kcIcnh4qQVB9.', 'Администратор', '-', '-', NULL, 3, DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`formTypes`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`formTypes` (`id`, `title`) VALUES (1, 'Резюмирование');
INSERT INTO `hrwebapp`.`formTypes` (`id`, `title`) VALUES (2, 'Тестирование');
INSERT INTO `hrwebapp`.`formTypes` (`id`, `title`) VALUES (3, 'Комбинированный');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`forms`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`forms` (`id`, `title`, `specialtyId`, `typeId`, `isActive`) VALUES (1, 'Вакансия на должность младшего Java программиста в отделе разработки решений', 4, 1, DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`questionTypes`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`questionTypes` (`id`, `title`) VALUES (1, 'Оцениваемый');
INSERT INTO `hrwebapp`.`questionTypes` (`id`, `title`) VALUES (2, 'Неоцениваемый');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`questionCategories`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`questionCategories` (`id`, `title`) VALUES (1, 'Общие вопросы');
INSERT INTO `hrwebapp`.`questionCategories` (`id`, `title`) VALUES (2, 'Опыт работы');
INSERT INTO `hrwebapp`.`questionCategories` (`id`, `title`) VALUES (3, 'Знания');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`answerTypes`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`answerTypes` (`id`, `title`) VALUES (1, 'Одинарный ответ');
INSERT INTO `hrwebapp`.`answerTypes` (`id`, `title`) VALUES (2, 'Множественный ответ');
INSERT INTO `hrwebapp`.`answerTypes` (`id`, `title`) VALUES (3, 'Письменный ответ');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`questions`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (1, 1, 'Каковы ваши сильные стороны?', NULL, 2, 3, 1);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (2, 1, 'Каковы ваши слабые стороны?', NULL, 2, 3, 1);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (3, 1, 'Почему вы хотите получить именну эту должность в нашей компании?', NULL, 2, 3, 1);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (4, 1, 'Готовы ли вы переехать?', NULL, 2, 3, 1);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (5, 1, 'Какие зарплаты вы получали раньше?', NULL, 2, 3, 2);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (6, 1, 'MVC - это', NULL, 1, 2, 3);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (7, 1, 'Какой  из перечисленных принципов не относится к ООП?', NULL, 1, 1, 3);
INSERT INTO `hrwebapp`.`questions` (`id`, `formId`, `title`, `image`, `questionType`, `answerType`, `questionCategory`) VALUES (8, 1, 'Как называется функция, являющаяся точкой входа в программу. В приложении может быть несколько таких методов. Если метод отсутствует, то компиляция возможна, но при запуске будет получена ошибка. Ответ напишите без скобок.', NULL, 1, 3, 3);

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`applicationStatuses`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`applicationStatuses` (`id`, `title`) VALUES (1, 'Обрабатывается');
INSERT INTO `hrwebapp`.`applicationStatuses` (`id`, `title`) VALUES (2, 'Подтверждена');
INSERT INTO `hrwebapp`.`applicationStatuses` (`id`, `title`) VALUES (3, 'Отклонена');

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`applications`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`applications` (`id`, `applicantLogin`, `formId`, `score`, `date`, `statusId`, `lastModified`) VALUES (1, 'admin@admin.com', 1, NULL, DEFAULT, DEFAULT, DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `hrwebapp`.`options`
-- -----------------------------------------------------
START TRANSACTION;
USE `hrwebapp`;
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (1, 6, '- это схема разделения данных приложения, пользовательского интерфейса и управляющей логики на три отдельных компонента', 1);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (2, 6, '- это шаблон проектирования пользовательского интерфейса, который был разработан для облегчения автоматического модульного тестирования и улучшения разделения ответственности', 0);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (3, 6, '- это шаблон для разделения модели и её представления, что необходимо для их изменения отдельно друг от друга', 0);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (4, 7, 'Полиморвизм', 0);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (5, 7, 'Абстракция', 0);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (6, 7, 'Анонимность', 1);
INSERT INTO `hrwebapp`.`options` (`id`, `questionId`, `content`, `isCorrect`) VALUES (7, 8, 'main', NULL);

COMMIT;

