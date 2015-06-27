create table article_collection (
  uuid uuid primary key not null,
  date timestamp not null,
  title text not null,
  description text not null
);

create table article (
  aco_uuid uuid references article_collection(uuid) on delete cascade not null,
  id serial primary key not null,
  url text not null,
  title text not null,
  content text not null,
  original_content text not null
);

create table tag (
  id serial primary key not null,
  tag text unique not null
);

create table aco_tag (
  aco_uuid uuid references article_collection (uuid) on delete cascade,
  tag_id int references tag (id) on delete cascade,
  constraint aco_tag_pk primary key (aco_uuid, tag_id)
);
