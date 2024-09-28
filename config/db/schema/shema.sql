CREATE TYPE task_status AS ENUM ('Y', 'N');

CREATE TABLE IF NOT EXISTS users
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

CREATE TABLE IF NOT EXISTS projects
(
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(250) NOT NULL,
    creator_id   INT          NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS tasks
(
    id          SERIAL PRIMARY KEY,
    title       VARCHAR(250) NOT NULL,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    date_start  TIMESTAMP,
    date_end    TIMESTAMP,
    is_active   task_status DEFAULT 'Y',
    description TEXT,
    assignee_id INT          NOT NULL,
    creator_id  INT          NOT NULL,
    project_id  INT          NOT NULL,
    FOREIGN KEY (assignee_id) REFERENCES users (id),
    FOREIGN KEY (creator_id) REFERENCES users (id),
    FOREIGN KEY (project_id) REFERENCES projects (id)
);

CREATE TABLE IF NOT EXISTS tags
(
    id    SERIAL PRIMARY KEY,
    title VARCHAR(250) NOT NULL
);

CREATE TABLE IF NOT EXISTS public.tags_tasks
(
    id integer NOT NULL DEFAULT nextval('tags_tasks_id_seq'::regclass),
    tags_id integer,
    tasks_id integer,
    CONSTRAINT tags_tasks_pkey PRIMARY KEY (id),
    CONSTRAINT uq_fe787f38bd3e357ed668df4c68817c6f4a8775ef UNIQUE (tags_id, tasks_id),
    CONSTRAINT tags_tasks_tags_id_fkey FOREIGN KEY (tags_id)
    REFERENCES public.tags (id) MATCH SIMPLE
    ON UPDATE CASCADE
    ON DELETE CASCADE
    DEFERRABLE,
    CONSTRAINT tags_tasks_tasks_id_fkey FOREIGN KEY (tasks_id)
    REFERENCES public.tasks (id) MATCH SIMPLE
    ON UPDATE CASCADE
    ON DELETE CASCADE
    DEFERRABLE
)