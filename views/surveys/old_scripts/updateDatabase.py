import csv
import sys
import psycopg2

database = "Survey"
username = "postgres"
password = "password"
address = "127.0.0.1"

#connects to the web server on a port
conn = psycopg2.connect("dbname=" + database + " user=" + username + " host=" + address + " password=" + password)
#Allows Python code to execute PostgreSQL command in a database session.
cursor = conn.cursor()
with open("/Users/Administrator/Downloads/Responses - Form Responses 1.csv","r") as csvfile:

        #Loop that goes through each row and creates an insert statement
        reader = csv.reader(csvfile)
        x = 0
        
        #cursor.execute("DROP TABLE public.answers")
        #cursor.execute("CREATE TABLE answers()")
        #cursor.execute("Alter table answers Add Column FirstWeek varchar(3), Add Column FullName varchar(100), Add Column currentDate varchar(100), Add Column workshopTopic varchar(100), Add Column Loc varchar(100), Add Column Gender varchar(100), Add Column Race varchar(100), Add Column ageGroup varchar(100), Add Column q1 varchar(10), Add Column q2 varchar(10), Add Column q3 varchar(10), Add Column q4 varchar(10), Add Column q5 varchar(10), Add Column q6 varchar(10), Add Column suggestedTopics varchar(100), Add Column additionalComments varchar(1000);")
        cursor.execute("Select COUNT(*) FROM public.answers")
        y = cursor.fetchall()
        
        for row in reader:
                
                #print "ROW:  %s   ----    " % row
                if (x <= y[0][0]):

                        x += 1

                else:
                        
                        query = "INSERT INTO public.answers VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');" % (row[1], row[2], row[0], row[4], row[5], row[6], row[7], row[8], row[9], row[10], row[11], row[12], row[13], row[14], row[15], row[16])
                        #print("     ")
                        cursor.execute(query)
                        #print(query)
                        conn.commit()

cursor.close()
conn.close()
	
