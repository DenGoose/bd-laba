create table product_orders
(
    PRODUCT_ID int null,
    ORDER_ID   int null
# TODO пока не использую ключи, подумать как заменить
#     constraint product_orders_order_ID_fk
#         foreign key (ORDER_ID) references `order` (ID)
#             on delete cascade,
#     constraint product_orders_product_ID_fk
#         foreign key (PRODUCT_ID) references product (ID)
#             on delete cascade
);

INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (1, 1);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (1, 2);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (1, 4);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (2, 1);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (2, 3);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (3, 1);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (3, 2);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (3, 3);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (4, 4);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (5, 4);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (5, 5);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (6, 6);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (7, 7);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (8, 8);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (9, 9);
INSERT INTO bitrix.product_orders (PRODUCT_ID, ORDER_ID) VALUES (10, 10);
