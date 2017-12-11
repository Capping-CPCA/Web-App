import pip
def install (package):

    pip.main(['install', package])
    

if __name__ == '__main__':
    install('bs4')
    install('psycopg2')
	install('requests')
