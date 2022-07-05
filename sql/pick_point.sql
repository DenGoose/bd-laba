create table pick_point
(
    ID      int          auto_increment
        primary key,
    ADDRESS varchar(255) null
);

INSERT INTO bitrix.pick_point (ID, ADDRESS) VALUES (1, 'просп. Университетский, 92, Волгоград, Волгоградская обл., 400062');
INSERT INTO bitrix.pick_point (ID, ADDRESS) VALUES (2, 'улица Ермакова Роща, 7а ст1, 3 этаж, Москва, 123290');
INSERT INTO bitrix.pick_point (ID, ADDRESS) VALUES (3, 'Измайловский пр., 4, Санкт-Петербург, 190005');
