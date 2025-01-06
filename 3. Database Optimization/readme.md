# Database Optimization

#### If the table has a large number of rows, the query can be slow because the database needs to scan all rows to find those matching the user_id condition before performing the aggregation. I created an index on user_id column and covering index that includes both user_id and amount columns
