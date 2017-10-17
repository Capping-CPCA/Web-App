import csv
import psycopg2
import sys
#connects to the web server on a port
#conn = cpcapep.connect(connection,port here)
#Allows Python code to execute PostgreSQL command in a database session.
#cursor = conn.cursor()
csvfile = open("r2.csv","rb")

#Loop that goes through each row and creates an insert statement
reader = csv.reader(csvfile)
for row in reader:
	#print "ROW:  %s   ----    " % row
	query = "INSERT INTO answers (Q1, Q2, Q3) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);" % (row[0], row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9], row[10], row[11], row[12], row[13], row[14], row[15], row[16])
	print "  "
	#cursor.execute(query, data)
	print query
(csvfile).close
