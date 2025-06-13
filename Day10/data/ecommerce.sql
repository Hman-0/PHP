-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

-- Tạo bảng sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(100),
    brand VARCHAR(100),
    image VARCHAR(255)
);

-- Tạo bảng đánh giá sản phẩm
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tạo bảng bình chọn
CREATE TABLE IF NOT EXISTS polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    option_name VARCHAR(100) NOT NULL,
    votes INT DEFAULT 0
);

-- Thêm dữ liệu mẫu cho sản phẩm
INSERT INTO products (name, description, price, stock, category, brand, image) VALUES
('iPhone 13', 'Điện thoại iPhone 13 mới nhất với camera cải tiến', 24990000, 50, 'Điện tử', 'Apple', 'iphone13.jpg'),
('Samsung Galaxy S21', 'Điện thoại Samsung Galaxy S21 với hiệu năng mạnh mẽ', 19990000, 30, 'Điện tử', 'Samsung', 'galaxys21.jpg'),
('Sony Bravia 4K', 'Tivi Sony Bravia 4K 55 inch với hình ảnh sắc nét', 15990000, 20, 'Điện tử', 'Sony', 'bravia4k.jpg'),
('Áo thun Uniqlo', 'Áo thun nam Uniqlo chất liệu cotton cao cấp', 499000, 100, 'Thời trang', 'Uniqlo', 'uniqlo-tshirt.jpg'),
('Giày Nike Air Max', 'Giày thể thao Nike Air Max phiên bản mới nhất', 2990000, 40, 'Thời trang', 'Nike', 'nikeairmax.jpg'),
('Nồi cơm điện Sunhouse', 'Nồi cơm điện Sunhouse 1.8L với công nghệ tiết kiệm điện', 890000, 25, 'Gia dụng', 'Sunhouse', 'sunhouse-ricecooker.jpg');

-- Thêm dữ liệu mẫu cho đánh giá
INSERT INTO reviews (product_id, user_name, rating, comment) VALUES
(1, 'Nguyễn Văn A', 5, 'Sản phẩm rất tốt, đáng đồng tiền'),
(1, 'Trần Thị B', 4, 'Chất lượng tốt nhưng giá hơi cao'),
(2, 'Lê Văn C', 5, 'Điện thoại chụp ảnh đẹp, pin trâu'),
(3, 'Phạm Thị D', 3, 'Hình ảnh đẹp nhưng âm thanh chưa tốt lắm'),
(4, 'Hoàng Văn E', 5, 'Áo chất vải tốt, form đẹp'),
(5, 'Ngô Thị F', 4, 'Giày đi rất êm chân, thiết kế đẹp');

-- Thêm dữ liệu mẫu cho bình chọn
INSERT INTO polls (option_name, votes) VALUES
('Giao diện', 15),
('Tốc độ', 25),
('Dịch vụ khách hàng', 10);