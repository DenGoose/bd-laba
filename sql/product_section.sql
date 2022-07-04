create table product_section
(
    ID   int auto_increment
        primary key,
    NAME varchar(255) null
)
    auto_increment = 6;

INSERT INTO bitrix.product_section (ID, NAME) VALUES (1, 'Компьютеры и ноутбуки');
INSERT INTO bitrix.product_section (ID, NAME) VALUES (2, 'Компьютерные комплектующие');
INSERT INTO bitrix.product_section (ID, NAME) VALUES (3, 'Телевизоры');
INSERT INTO bitrix.product_section (ID, NAME) VALUES (4, 'Бытовая техника');
INSERT INTO bitrix.product_section (ID, NAME) VALUES (5, 'Автотовары');
