CREATE TABLE IF NOT EXISTS Part (
    id INT PRIMARY KEY AUTO_INCREMENT,
    carID INT,
    name VARCHAR(255),
    description VARCHAR(255),
    price FLOAT,
    quantityInStock INT,
    FOREIGN KEY (carID) REFERENCES Car(id)
);
