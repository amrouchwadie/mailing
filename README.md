This is highly unprofessional project done for learning purposes.
Database called : your_db_name
it has 3 tables. 
smtp_credentials : id - int, name - varchar, host - varchar, port - int, encryption - enrum (ssl, tls), username - varchar, password - varchar
email_list_entries	: id - int , email_list_id - int (FK), email - varchar
email_lists : int - id, name - varchar(255), created_at - timestamp
