create table user
(
    ID          int          auto_increment
        primary key,
    NAME        varchar(255) null,
    SECOND_NAME varchar(255) null,
    LAST_NAME   varchar(255) null
);

INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (1, 'Захар', 'Виноградов', 'Улебович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (2, 'Валентин', 'Морозов', 'Пётрович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (3, 'Инесса', 'Бернацкая', 'Глебовна');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (4, 'Инга', 'Цветаева', null);
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (5, 'Феликс', 'Орлов', 'Федорович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (6, 'Никита', 'Белов', 'Антонинович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (7, 'Сергей', 'Королев', 'Максович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (8, 'Ярослав', 'Иванов', 'Олегович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (9, 'Адам', 'Орлов', 'Христофорович');
INSERT INTO bitrix.user (ID, NAME, SECOND_NAME, LAST_NAME) VALUES (10, 'Клара', 'Фёдорова', 'Никитевна');
