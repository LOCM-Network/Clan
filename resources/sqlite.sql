-- #!sqlite
-- #{ table
-- #    { init
CREATE TABLE IF NOT EXISTS clans (
    name TEXT NOT NULL,
    tag TEXT NOT NULL,
    owner TEXT NOT NULL,
    deputy TEXT NOT NULL,
    members TEXT NOT NULL,
    created TEXT NOT NULL,
    level INTERGER NOT NULL,
    points TEXT NOT NULL
)
-- #}
-- #    { insert
-- #        :name string
-- #        :tag string
-- #        :owner string
-- #        :deputy string
-- #        :members string
-- #        :created string
-- #        :level int
-- #        :points int
INSERT INTO clans (name, tag, owner, deputy, members, created, level, points) VALUES (:name, :tag, :owner, :deputy, :members, :created, :level, :points)
-- #    }
-- #    { update
-- #        :name string
-- #        :tag string
-- #        :owner string
-- #        :deputy string
-- #        :members string
-- #        :created string
-- #        :level int
-- #        :points int
UPDATE clans SET name = :name, tag = :tag, owner = :owner, deputy = :deputy, members = :members, created = :created, level = :level, points = :points WHERE name = :name
-- #    }
-- #    { delete
-- #        :name string
DELETE FROM clans WHERE name = :name
-- #    }
-- #    { select
SELECT * FROM clans WHERE name = :name
-- #    }
-- #    { select_all
SELECT * FROM clans
-- #    }
-- #}
