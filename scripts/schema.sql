create table overrides (
    id integer primary key not null,
    transaction_date date not null,
    description varchar not null,
    amount DOUBLE not null,
    category varchar not null,
    notes text
);