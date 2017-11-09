@echo off
start "1" https://docs.google.com/spreadsheets/d/1bZ_K7fk1ut4B7qRvcbXFsq8tDIXN23w1ZWHe4nX0ay8/export?exportFormat=csv&gid=94817619

cls

PING localhost -n 6 >NUL

python updateDatabase.py

del "C:\Users\Administrator\Downloads\Responses - Form Responses 1.csv"

echo Database has been succesfully updated!

pause