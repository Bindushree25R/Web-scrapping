# stepthree.py

import mysql.connector

db = mysql.connector.connect(
    host="localhost",
    user="your_user",
    password="your_password",
    database="your_database"
)
cursor = db.cursor()

# Assume you're associating to projectId = 1
project_id = 1

cursor.execute("SELECT moduleId FROM Modules")
modules = cursor.fetchall()

for (module_id,) in modules:
    cursor.execute("""
        SELECT 1 FROM project_module_association
        WHERE projectId = %s AND moduleId = %s
    """, (project_id, module_id))
    if not cursor.fetchone():
        cursor.execute("""
            INSERT INTO project_module_association (projectId, moduleId)
            VALUES (%s, %s)
        """, (project_id, module_id))
        db.commit()

cursor.close()
db.close()
print("✅ Step 3 completed.")
