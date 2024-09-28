INSERT INTO users (first_name, last_name, email, username, password, profile_picture)
VALUES
    ('Иван', 'Иванов', 'ivan.ivanov@example.com', 'ivanivanov', 'password123', ''),
    ('Анна', 'Петрова', 'anna.petrovna@example.com', 'annapetrova', 'mypassword', ''),
    ('Сергей', 'Сидоров', 'sergey.sidorov@example.com', 'sergeysidorov', 'securepass', ''),
    ('Елена', 'Кузнецова', 'elena.kuznetsova@example.com', 'elenakuznetsova', 'password456', ''),
    ('Дмитрий', 'Николаев', 'dmitriy.nikolaev@example.com', 'dmitriynikolaev', 'passw0rd', '');

INSERT INTO projects (title, creator_id, created_date)
VALUES
    ('Проект 1', 1, DEFAULT),
    ('Проект 2', 2, DEFAULT),
    ('Проект 3', 1, DEFAULT),
    ('Проект 4', 3, DEFAULT),
    ('Проект 5', 2, DEFAULT);

INSERT INTO tags (title)
VALUES
    ('Tag 1'),
    ('Tag 2'),
    ('Tag 3'),
    ('Tag 4'),
    ('Tag 5');

INSERT INTO tasks (title, date_start, date_end, is_active, description, assignee_id, creator_id, project_id)
VALUES
    ('Задача 1', '2023-10-01 09:00:00', '2023-10-02 17:00:00', 'Y', 'Описание задачи 1', 1, 1, 1),
    ('Задача 2', '2023-10-05 09:00:00', '2023-10-10 17:00:00', 'Y', 'Описание задачи 2', 2, 1, 1),
    ('Задача 3', '2023-10-12 11:00:00', '2023-10-15 15:00:00', 'Y', 'Описание задачи 3', 3, 2, 2),
    ('Задача 4', '2023-10-20 09:30:00', '2023-10-25 16:30:00', 'N', 'Описание задачи 4', 1, 2, 2),
    ('Задача 5', '2023-10-22 10:00:00', '2023-10-30 18:00:00', 'Y', 'Описание задачи 5', 2, 1, 1);


