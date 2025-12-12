## command helper

### --db:select 

- use of limt

- --limit:1     # number of element to get
- --limit:1,10  # the 10 at index 1

- use of `order` example
```sh
balafon --db:select %sys% Migrations --limit:1 --order:'clId|DESC, migration_batch|ASC'
```

- use of `like` to select data 

```sh
balafon --db:select %sys% Users --like:'@@clLogin=%wi%,>clId=4'
```

### note: default operator is AND

@@ColumnName will be Like in mysql  



