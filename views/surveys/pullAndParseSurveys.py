import csv
import sys
import psycopg2
import requests
from bs4 import BeautifulSoup

# config.ini information is passed in as arguments from php shell_exec
database = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]
address = sys.argv[4]
googleEmail = sys.argv[5]
googlePassword = sys.argv[6]

class SessionGoogle:
    def __init__(self, url_login, url_auth, login, pwd):
        self.ses = requests.session()
        login_html = self.ses.get(url_login)
        soup_login = BeautifulSoup(login_html.content, "html.parser").find('form').find_all('input')
        my_dict = {}
        for u in soup_login:
            if u.has_attr('value'):
                my_dict[u['name']] = u['value']
        # override the inputs without login and pwd:
        my_dict['Email'] = login
        my_dict['Passwd'] = pwd
        self.ses.post(url_auth, data=my_dict)

    def get(self, URL):
        return self.ses.get(URL)

#connects to the web server on a port
conn = psycopg2.connect("dbname=" + database + " user=" + username + " host=" + address + " password=" + password)
#Allows Python code to execute PostgreSQL command in a database session.
cursor = conn.cursor()

url_login = "https://accounts.google.com/ServiceLogin"
url_auth = "https://accounts.google.com/ServiceLoginAuth"
session = SessionGoogle(url_login, url_auth, googleEmail, googlePassword)
download = session.get("https://docs.google.com/spreadsheets/d/1LDN8wTZMKf7R01U6d0ToHfFjuxnkm5_9gkgptMouvQw/export?exportFormat=csv&gid=2000530113")

csvfile = download.content.decode('utf')

#Loop that goes through each row and creates an insert statement
reader = csv.reader(csvfile.splitlines(), delimiter=',')
insertList = list(reader)

#cursor.execute("DROP TABLE public.answers")
#cursor.execute("CREATE TABLE answers()")
#cursor.execute("Alter table answers Add Column currentDate varchar(20), Add Column FirstWeek varchar(3), Add Column FullName varchar(100), Add Column TimeOfClass varchar(12), Add Column workshopTopic varchar(100), Add Column Loc varchar(100), Add Column Gender varchar(100), Add Column Race varchar(100), Add Column ageGroup varchar(100), Add Column q1 varchar(10), Add Column q2 varchar(10), Add Column q3 varchar(10), Add Column q4 varchar(10), Add Column q5 varchar(10), Add Column q6 varchar(10), Add Column suggestedTopics varchar(100), Add Column additionalComments varchar(1000);")
cursor.execute("Select COUNT(*) FROM surveys")
x = 0
y = cursor.fetchall()
insertedRows = 0

for row in insertList:
    #print "ROW:  %s   ----    " % row
    if (x <= y[0][0]):
        x += 1
        
    else:
        insertedRows += 1
        test = row[0].split(" ", 1)
        classTime = test[0] + " " + row[3]
        
        query = "SELECT surveyInsert(surveyParticipantName := '%s'::TEXT, surveyMaterialPresentedScore := '%s'::INT, surveyPresTopicDiscussedScore := '%s'::INT, surveyPresOtherParentsScore := '%s'::INT,surveyPresChildPerspectiveScore := '%s'::INT, surveyPracticeInfoScore := '%s'::INT, surveyRecommendScore := '%s'::INT, surveySuggestedFutureTopics := '%s'::TEXT, surveyComments := '%s'::TEXT, surveyStartTime := '%s'::TIMESTAMP, surveySiteName := '%s'::TEXT, firstWeek := '%s'::BOOLEAN, topicName := '%s'::TEXT, gender := '%s'::SEX,race := '%s'::RACE, ageGroup := '%s'::TEXT);" % (row[2].replace("'", "\′"), row[9], row[10], row[11], row[12], row[13], row[14], row[15].replace("'", "\′"), row[16].replace("'", "\′"), classTime, row[5], row[1],  row[4], row[6], row[7].replace("'", "\′"), row[8])
        #"INSERT INTO public.answers VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');" % (row[0], row[1], row[2].replace("'", "\′"), row[3], row[4], row[5], row[6], row[7].replace("'", "\′"), row[8], row[9], row[10], row[11], row[12], row[13], row[14], row[15].replace("'", "\′"), row[16].replace("'", "\′"))
        #print("     ")                                                
        
        cursor.execute(query)
        #print(query)
        conn.commit()

x = 0
sys.stdout.write("Database has been updated. (" + str(insertedRows) + ") rows have been inserted.")
cursor.close()
conn.close()
    
