create table purchase (
    purchase_id INT AUTO_INCREMENT,
    user_id int(11),
    item_id int(11),
    amount int(11),
    price int(11),
    purchase_date date,
    primary key(purchase_id)
    );
a    