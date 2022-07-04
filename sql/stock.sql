create table stock
(
    ID      int          not null
        primary key,
    CITY    varchar(255) null,
    ADDRESS varchar(255) null
);

INSERT INTO bitrix.stock (ID, CITY, ADDRESS) VALUES (1, 'Волгоград', 'просп. Университетский, 92, Волгоград, Волгоградская обл., 400062');
INSERT INTO bitrix.stock (ID, CITY, ADDRESS) VALUES (2, 'Москва', 'улица Ермакова Роща, 7а ст1, 3 этаж, Москва, 123290');
INSERT INTO bitrix.stock (ID, CITY, ADDRESS) VALUES (3, 'СПБ', 'Измайловский пр., 4, Санкт-Петербург, 190005');
INSERT INTO bitrix.stock (ID, CITY, ADDRESS) VALUES (4, 'Краснодар', 'ТК "Галерея", ул. Головатого, 313, Краснодар, Краснодарский край, 350000');
INSERT INTO bitrix.stock (ID, CITY, ADDRESS) VALUES (5, 'Ростов-на-дону', 'пр. Михаила Нагибина, 32Ж, Ростов-на-Дону, Ростовская обл., 344068');
