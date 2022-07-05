create table `order`
(
    ID            int auto_increment
        primary key,
    TOTAL_PRICE   int null,
    USER_ID       int null,
    PICK_POINT_ID int null,
    constraint order_pick_point_ID_fk
        foreign key (PICK_POINT_ID) references pick_point (ID)
            on delete cascade,
    constraint order_user_ID_fk
        foreign key (USER_ID) references user (ID)
            on delete cascade
);

INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (1, 510770, 1, 1);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (2, 429780, 2, 2);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (3, 460980, 3, 1);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (4, 55580, 3, 2);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (5, 12590, 3, 1);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (6, 5290, 4, 1);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (7, 74990, 5, 3);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (8, 70110, 6, 1);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (9, 18990, 2, 2);
INSERT INTO bitrix.`order` (ID, TOTAL_PRICE, USER_ID, PICK_POINT_ID) VALUES (10, 7070, 7, 2);
