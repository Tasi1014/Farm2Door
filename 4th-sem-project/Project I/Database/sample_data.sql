-- Farm2Door Sample Data
-- This file contains sample data for testing and development

USE farm2door;

-- Insert sample users (password is 'password123' hashed with bcrypt)
INSERT INTO users (email, password, first_name, last_name, phone, address, user_type, is_active, email_verified) VALUES
-- Farmers
('john.farmer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', '+1234567890', '123 Farm Road, Green Valley', 'farmer', TRUE, TRUE),
('mary.farmer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary', 'Johnson', '+1234567891', '456 Organic Lane, Fresh Fields', 'farmer', TRUE, TRUE),
('david.farmer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Brown', '+1234567892', '789 Harvest Street, Garden City', 'farmer', TRUE, TRUE),

-- Consumers
('sarah.consumer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', '+1234567893', '321 City Avenue, Urban Center', 'consumer', TRUE, TRUE),
('mike.consumer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Davis', '+1234567894', '654 Suburb Street, Residential Area', 'consumer', TRUE, TRUE),
('lisa.consumer@farm2door.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa', 'Anderson', '+1234567895', '987 Downtown Plaza, City Center', 'consumer', TRUE, TRUE);

-- Insert farmer profiles
INSERT INTO farmers (user_id, farm_name, farm_location, farm_size, certification, bio, rating, total_orders) VALUES
(1, 'Green Valley Organic Farm', 'Green Valley, CA', 50.5, 'USDA Organic', 'Family-owned organic farm specializing in fresh vegetables and herbs. We believe in sustainable farming practices.', 4.8, 156),
(2, 'Fresh Fields Farm', 'Fresh Fields, TX', 75.2, 'Certified Organic', 'Multi-generational farm producing the finest organic produce. Committed to environmental sustainability.', 4.6, 203),
(3, 'Garden City Produce', 'Garden City, FL', 30.8, 'Local Certified', 'Small family farm focused on seasonal vegetables and fruits. Pesticide-free and locally grown.', 4.7, 89);

-- Insert sample products
INSERT INTO products (farmer_id, category_id, name, description, price, stock_quantity, unit, is_organic, is_available, harvest_date) VALUES
-- John's products
(1, 1, 'Fresh Spinach', 'Crisp, dark green spinach leaves perfect for salads and cooking', 3.50, 50, 'bunch', TRUE, TRUE, '2024-01-15'),
(1, 1, 'Organic Carrots', 'Sweet, crunchy carrots grown without pesticides', 2.80, 75, 'kg', TRUE, TRUE, '2024-01-12'),
(1, 1, 'Ripe Tomatoes', 'Juicy, vine-ripened tomatoes perfect for salads and cooking', 4.20, 40, 'kg', TRUE, TRUE, '2024-01-14'),
(1, 1, 'Fresh Potatoes', 'Russet potatoes ideal for baking, frying, or mashing', 2.50, 100, 'kg', FALSE, TRUE, '2024-01-10'),

-- Mary's products
(2, 1, 'Green Bell Peppers', 'Crisp, sweet bell peppers in various colors', 3.80, 30, 'kg', TRUE, TRUE, '2024-01-13'),
(2, 1, 'Fresh Broccoli', 'Nutritious broccoli heads with tender stems', 4.50, 25, 'head', TRUE, TRUE, '2024-01-11'),
(2, 2, 'Strawberries', 'Sweet, red strawberries perfect for desserts', 6.00, 20, 'pint', TRUE, TRUE, '2024-01-16'),
(2, 1, 'Cucumbers', 'Crisp, refreshing cucumbers great for salads', 2.20, 60, 'kg', FALSE, TRUE, '2024-01-09'),

-- David's products
(3, 1, 'Lettuce Mix', 'Mixed lettuce varieties for fresh salads', 3.00, 35, 'bag', TRUE, TRUE, '2024-01-14'),
(3, 1, 'Onions', 'Yellow cooking onions with strong flavor', 1.80, 80, 'kg', FALSE, TRUE, '2024-01-08'),
(3, 3, 'Fresh Basil', 'Aromatic basil leaves perfect for Italian dishes', 4.00, 15, 'bunch', TRUE, TRUE, '2024-01-15'),
(3, 1, 'Zucchini', 'Tender zucchini squash for grilling or sautéing', 3.20, 45, 'kg', TRUE, TRUE, '2024-01-12');

-- Insert sample orders
INSERT INTO orders (consumer_id, farmer_id, order_number, total_amount, status, delivery_address, delivery_date, payment_status, payment_method) VALUES
(4, 1, 'ORD-2024-001', 15.30, 'delivered', '321 City Avenue, Urban Center', '2024-01-20', 'paid', 'credit_card'),
(5, 2, 'ORD-2024-002', 22.50, 'shipped', '654 Suburb Street, Residential Area', '2024-01-21', 'paid', 'paypal'),
(6, 3, 'ORD-2024-003', 18.40, 'confirmed', '987 Downtown Plaza, City Center', '2024-01-22', 'paid', 'credit_card'),
(4, 2, 'ORD-2024-004', 12.80, 'pending', '321 City Avenue, Urban Center', '2024-01-23', 'pending', 'credit_card');

-- Insert order items
INSERT INTO order_items (order_id, product_id, quantity, price, total_price) VALUES
-- Order 1 items
(1, 1, 2, 3.50, 7.00),
(1, 2, 3, 2.80, 8.40),

-- Order 2 items
(2, 5, 2, 3.80, 7.60),
(2, 6, 1, 4.50, 4.50),
(2, 7, 2, 6.00, 12.00),

-- Order 3 items
(3, 9, 1, 3.00, 3.00),
(3, 10, 2, 1.80, 3.60),
(3, 11, 1, 4.00, 4.00),
(3, 12, 2, 3.20, 6.40),

-- Order 4 items
(4, 5, 1, 3.80, 3.80),
(4, 6, 1, 4.50, 4.50),
(4, 8, 2, 2.20, 4.40);

-- Insert sample cart items
INSERT INTO cart (consumer_id, product_id, quantity) VALUES
(4, 3, 2),
(4, 4, 1),
(5, 1, 1),
(5, 2, 2),
(6, 7, 1),
(6, 11, 2);

-- Insert sample reviews
INSERT INTO reviews (order_id, consumer_id, farmer_id, product_id, rating, review_text, is_verified) VALUES
(1, 4, 1, 1, 5, 'Excellent quality spinach! Very fresh and crisp.', TRUE),
(1, 4, 1, 2, 4, 'Great carrots, very sweet and fresh.', TRUE),
(2, 5, 2, 5, 5, 'Perfect bell peppers, exactly as described.', TRUE),
(2, 5, 2, 6, 4, 'Good broccoli, very fresh.', TRUE),
(3, 6, 3, 9, 5, 'Amazing lettuce mix, perfect for salads!', TRUE),
(3, 6, 3, 11, 5, 'Fresh basil with great aroma.', TRUE);

-- Insert sample messages
INSERT INTO messages (sender_id, receiver_id, order_id, subject, message, is_read) VALUES
(4, 1, 1, 'Delivery Question', 'Hi, can you deliver my order in the morning?', TRUE),
(1, 4, 1, 'Delivery Confirmation', 'Sure! I can deliver between 9-11 AM. Is that okay?', TRUE),
(5, 2, 2, 'Order Status', 'When will my order be shipped?', FALSE),
(6, 3, 3, 'Special Request', 'Can you include some extra herbs with my order?', FALSE);

-- Insert sample notifications
INSERT INTO notifications (user_id, title, message, type, is_read) VALUES
(1, 'New Order', 'You have received a new order from Sarah Wilson', 'order', FALSE),
(2, 'Order Shipped', 'Your order ORD-2024-002 has been shipped', 'order', TRUE),
(4, 'Order Delivered', 'Your order ORD-2024-001 has been delivered', 'order', TRUE),
(5, 'Payment Received', 'Payment for order ORD-2024-002 has been received', 'order', FALSE);

-- Insert sample contact submissions
INSERT INTO contact_submissions (name, email, subject, message, is_responded) VALUES
('Alice Johnson', 'alice@email.com', 'Partnership Inquiry', 'I would like to know more about becoming a partner farmer.', FALSE),
('Bob Smith', 'bob@email.com', 'Product Question', 'Do you deliver to my area?', TRUE),
('Carol Davis', 'carol@email.com', 'Feedback', 'Great service! Keep up the good work.', TRUE);

-- Update farmer statistics based on orders
UPDATE farmers f SET 
    total_orders = (
        SELECT COUNT(*) 
        FROM orders o 
        WHERE o.farmer_id = f.id
    ),
    total_revenue = (
        SELECT COALESCE(SUM(o.total_amount), 0) 
        FROM orders o 
        WHERE o.farmer_id = f.id
    );

-- Update farmer ratings based on reviews
UPDATE farmers f SET 
    rating = (
        SELECT COALESCE(AVG(r.rating), 0) 
        FROM reviews r 
        WHERE r.farmer_id = f.id
    );
