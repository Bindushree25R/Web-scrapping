# steptwo.py

import mysql.connector

db = mysql.connector.connect(
    host="localhost",
    user="your_user",
    password="your_password",
    database="your_database"
)
cursor = db.cursor()

cursor.execute("SELECT requirement_module FROM issues WHERE requirement_module IS NOT NULL")
modules = cursor.fetchall()

for (module,) in modules:
    cursor.execute("INSERT INTO Modules (ModuleName) VALUES (%s)", (module,))
    db.commit()

cursor.close()
db.close()

print("✅ Step 2 completed. All modules inserted.")
