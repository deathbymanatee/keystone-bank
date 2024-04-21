CREATE DATABASE IF NOT EXISTS keystone;
USE keystone;

CREATE TABLE UserInformation (
    unique_user_ID CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    SSN CHAR(9) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE CheckingSavingsAccount (
    user_ID CHAR(36),
    acct_ID CHAR(36) PRIMARY KEY,
    checking_balance DECIMAL(10, 2) DEFAULT 0.00,
    savings_balance DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (user_ID) REFERENCES UserInformation(unique_user_ID) 
        ON DELETE CASCADE
);

CREATE TABLE UserTransactionTable (
    transaction_ID CHAR(36) PRIMARY KEY, 
    send_acctID CHAR(36),
    receive_acctID CHAR(36),
    amount_transferred DECIMAL(10, 2),
    transaction_date DATE DEFAULT CURDATE(),
);
