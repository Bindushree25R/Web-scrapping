# stepfour.py

import mysql.connector
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import time

# DB Connection
db = mysql.connector.connect(
    host="localhost",
    user="your_user",
    password="your_password",
    database="your_database"
)
cursor = db.cursor()

# Helper Functions
def get_project_module_id(project_id, module_name):
    cursor.execute("SELECT moduleId FROM Modules WHERE ModuleName = %s", (module_name,))
    mod = cursor.fetchone()
    if not mod:
        return None
    module_id = mod[0]
    cursor.execute("""
        SELECT Id FROM project_module_association
        WHERE projectId = %s AND moduleId = %s
    """, (project_id, module_id))
    assoc = cursor.fetchone()
    return assoc[0] if assoc else None

def get_reporter_id(name):
    cursor.execute("SELECT reporterId FROM reporters WHERE reporterName = %s", (name,))
    rep = cursor.fetchone()
    return rep[0] if rep else None

def get_severity_id(sev_name):
    cursor.execute("SELECT severityId FROM severity WHERE severityName = %s", (sev_name,))
    sev = cursor.fetchone()
    return sev[0] if sev else None

def get_observation_classification_id(name):
    cursor.execute("SELECT observationClassificationId FROM observation_classification WHERE classificationName = %s", (name,))
    o = cursor.fetchone()
    return o[0] if o else None

# Selenium Driver
options = Options()
options.add_argument("--no-sandbox")
driver = webdriver.Chrome(service=Service("/home/opr/chromedriver"), options=options)
driver.set_page_load_timeout(30)
driver.implicitly_wait(10)

# Login
driver.get("http://your-login-url")
username = input("Enter your username: ")
password = input("Enter your password: ")

WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "password"))).send_keys(password)
WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.ID, "login_submit"))).click()

# Read issues table
cursor.execute("SELECT subject, requirement_module FROM issues")
all_issues = cursor.fetchall()

for subject, module in all_issues:
    # Navigate to the issue link
    cursor.execute("SELECT * FROM issues WHERE subject = %s", (subject,))
    issue_row = cursor.fetchone()
    if not issue_row:
        continue

    issue_link = "http://your-domain/issues/" + subject.replace(" ", "-")
    driver.get(issue_link)
    time.sleep(1)

    soup = BeautifulSoup(driver.page_source, "html.parser")

    description_div = soup.find("div", {"class": "description"})
    description = description_div.text.strip() if description_div else ""

    project_id = 1
    project_module_id = get_project_module_id(project_id, module)
    reporter_id = get_reporter_id("Default Reporter") or 1
    severity_id = get_severity_id("Low") or 1
    observation_classification_id = get_observation_classification_id("General") or 1

    cursor.execute("""
        INSERT INTO observation (subject, description, project_moduleid, ReporterId, severityId, observationClassificationId)
        VALUES (%s, %s, %s, %s, %s, %s)
    """, (subject, description, project_module_id, reporter_id, severity_id, observation_classification_id))
    db.commit()

cursor.close()
db.close()
driver.quit()
print("✅ Step 4 completed. Observations inserted.")
