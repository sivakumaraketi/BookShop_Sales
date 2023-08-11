-- Insert sample customers
INSERT INTO customers (name) VALUES
    ('John Doe'),
    ('Jane Smith'),
    ('Michael Johnson');

-- Insert sample sales data
INSERT INTO sales (customer_id, product, price) VALUES
    (1, 'Book A', 15.99),
    (2, 'Book B', 12.50),
    (1, 'Book C', 19.75),
    (3, 'Book A', 15.99),
    (2, 'Book D', 9.99),
    (1, 'Book E', 22.50),
    (3, 'Book B', 12.50),
    (2, 'Book F', 8.75);
