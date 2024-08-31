CREATE TABLE users
(
    id                INT AUTO_INCREMENT PRIMARY KEY,
    first_name        VARCHAR(100) NOT NULL,
    last_name         VARCHAR(100) NOT NULL,
    email             VARCHAR(255) NOT NULL UNIQUE,
    username          VARCHAR(50)  NOT NULL UNIQUE,
    password          VARCHAR(255) NOT NULL,
    nickname          VARCHAR(50),
    profile_picture   VARCHAR(255), -- Добавлено поле для изображения профиля
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    due_date    DATETIME,
    is_active   ENUM ('Y', 'N') DEFAULT 'Y',
    description TEXT,
    assignee_id INT NOT NULL, -- ID пользователя, ответственного за задачу
    creator_id  INT NOT NULL, -- ID пользователя, который создал задачу
    FOREIGN KEY (assignee_id) REFERENCES users (id),
    FOREIGN KEY (creator_id) REFERENCES users (id)
);

CREATE TABLE tags
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);