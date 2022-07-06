create table product_stock
(
    PRODUCT_ID int null,
    STOCK_ID   int null
# TODO пока не использую ключи, подумать как заменить
#     constraint product_stock_product_ID_fk
#         foreign key (PRODUCT_ID) references product (ID)
#             on delete cascade,
#     constraint product_stock_stock_ID_fk
#         foreign key (PRODUCT_ID) references stock (ID)
#             on delete cascade
);

INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (1, 1);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (1, 2);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (1, 3);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (2, 1);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (2, 2);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (2, 3);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (3, 1);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (3, 2);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (3, 3);
INSERT INTO bitrix.product_stock (PRODUCT_ID, STOCK_ID) VALUES (4, 1);
