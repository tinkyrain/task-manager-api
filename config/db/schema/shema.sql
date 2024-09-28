-- Создание типа ENUM для статуса задачи
CREATE TYPE task_status AS ENUM ('Y', 'N');

-- Создание таблицы пользователей
CREATE TABLE users
(
    id                SERIAL PRIMARY KEY,
    first_name        VARCHAR(250) NOT NULL,
    last_name         VARCHAR(250) NOT NULL,
    email             VARCHAR(250) NOT NULL UNIQUE,
    username          VARCHAR(50)  NOT NULL UNIQUE,
    password          VARCHAR(250) NOT NULL,
    profile_picture   VARCHAR(255),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы задач
CREATE TABLE tasks
(
    id          SERIAL PRIMARY KEY,
    title       VARCHAR(250) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- будет обновлено триггером
    date_start  TIMESTAMP,
    date_end    TIMESTAMP,
    is_active   task_status DEFAULT 'Y',
    description TEXT,
    assignee_id INT NOT NULL, -- ID пользователя, ответственного за задачу
    creator_id  INT NOT NULL, -- ID пользователя, который создал задачу
    FOREIGN KEY (assignee_id) REFERENCES users (id),
    FOREIGN KEY (creator_id) REFERENCES users (id)
);

-- Создание таблицы проектов
CREATE TABLE projects
(
    id            SERIAL PRIMARY KEY,
    title         VARCHAR(250) NOT NULL,
    creator_id    INT NOT NULL,
    created_date  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users (id)
);

-- Создание таблицы тегов
CREATE TABLE tags
(
    id    SERIAL PRIMARY KEY,
    title VARCHAR(250) NOT NULL
);