@echo off

echo -------------------------------------------
echo DROP DATABASE db_applicationbase
mysql -u root -p -e "DROP DATABASE IF EXISTS db_applicationbase;"

echo CREATE DATABASE db_applicationbase
mysql -u root -p -e "CREATE DATABASE db_applicationbase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo RESTORE db_laportada_v2_2026-07-09.sql
mysql -u root -p db_applicationbase < db_laportada_v2_2026-07-09.sql
