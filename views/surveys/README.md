# Surveys

* Surveys are hosted on Google Drive using a Google Form which then sends the submitted form data to a linked Google Spreadsheet. 
* The following files are responsible for automatically downloading a csv with the survey data which is then processed by a python script that creates new insert statements for each new value in the csv.

# Install Python 
* Navigate to https://www.python.org/ftp/python/2.7.8/python-2.7.8.msi and install Python 2.7.

# Configuring Python Dependencies 
* Navigate to surveys folder
* Run the setup.py script to install the python imports/dependencies, do this by entering the following into command prompt:
```
cd .. /Web-App/views/surveys
py setup.py
```
# Modifying Python File
* Once all pythons imports are installed then open the pullandparse.py file in your editor of choice and change the following:
```
database = "(database name here)"
username = "(username here)"
password = "(password here)"
address = "(database IP address here)"
```
# Install Postgres Python Psycog2
* Open File Explorer and search for "pg_hba.conf", it should be in the path C:\Program Files\PostgreSQL\9.6\data, open the file and modify the IPv4 local connection at the bottom of the file adding 
```
"host   all   all   (database IP here)/24   trust"    
```