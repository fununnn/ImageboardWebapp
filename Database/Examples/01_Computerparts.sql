CREATE TABLE Computerparts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    brand VARCHAR(255) NOT NULL,
    model_number VARCHAR(100) NOT NULL,
    release_date DATE,
    description TEXT,
    performance_score INT,
    market_price DECIMAL(10, 2),
    rsm DECIMAL(10, 2),
    power_consumptionw FLOAT,
    lengthm DOUBLE,
    widthm DOUBLE,
    heightm DOUBLE,
    lifespan INT,
    created_at DATETIME,
    updated_at DATETIME
);
