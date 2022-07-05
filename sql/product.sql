create table product
(
    ID         int          auto_increment
        primary key,
    NAME       varchar(255) null,
    SECTION_ID int          null,
    PRICE      int          null,
    constraint product_product_section_ID_fk
        foreign key (SECTION_ID) references product_section (ID)
            on delete cascade
);

INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (1, 'Huawei MateBook D 15 15.6', 1, 49790);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (2, 'Lenovo IP Gaming 3 15ACH6', 1, 80990);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (3, 'Apple MacBook Pro 14.2', 1, 379990);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (4, 'Intel Core i7 12700K', 2, 42990);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (5, 'Intel Core i5 10400F', 2, 12590);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (6, 'AMD R9 R9S416G3206U2S DDR4', 2, 5290);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (7, 'Samsung QE43LS01TBUXRU', 3, 74990);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (8, 'Xiaomi MI TV 55 QLED', 3, 70110);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (9, 'Xiaomi Robot Vacuum-Mop 2S', 4, 18990);
INSERT INTO bitrix.product (ID, NAME, SECTION_ID, PRICE) VALUES (10, 'VARTA Blue Dynamic 60Ач 540A', 5, 7070);
