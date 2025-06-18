# stepone.py

import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import mysql.connector

# Setup database connection
db = mysql.connector.connect(
    host="localhost",
    user="your_user",
    password="your_password",
    database="your_database"
)
cursor = db.cursor()

# Start ChromeDriver
options = Options()
options.add_argument("--no-sandbox")
driver = webdriver.Chrome(service=Service("/home/opr/chromedriver"), options=options)
driver.set_page_load_timeout(30)
driver.implicitly_wait(10)

# Login to site
driver.get("http://your-login-url")
username = input("Enter your username: ")
password = input("Enter your password: ")

WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "password"))).send_keys(password)
WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.ID, "login_submit"))).click()
print("✅ Logged in.")

# Navigate to issues page
driver.get("http://your-projects-url")
WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.LINK_TEXT, "Issues"))).click()
time.sleep(2)

# Read issues
while True:
    soup = BeautifulSoup(driver.page_source, "html.parser")
    rows = soup.select("table.list.issues tr")

    for row in rows:
        link = row.find("a", href=True)
        if link and "/issues/" in link["href"]:
            subject = link.text.strip()
            issue_url = "http://your-domain" + link["href"]

            driver.get(issue_url)
            time.sleep(1)

            issue_soup = BeautifulSoup(driver.page_source, "html.parser")
            req_span = issue_soup.find("span", string="RequirementModules")
            requirement_module = req_span.find_parent().find_next_sibling("div").text.strip() if req_span else None

            cursor.execute("SELECT 1 FROM issues WHERE subject = %s", (subject,))
            if not cursor.fetchone():
                cursor.execute("INSERT INTO issues (subject, requirement_module) VALUES (%s, %s)", (subject, requirement_module))
                db.commit()

            driver.back()
            time.sleep(1)

    try:
        driver.find_element(By.LINK_TEXT, "Next »").click()
        time.sleep(2)
    except:
        break

cursor.close()
db.close()
driver.quit()
print("✅ Step 1 completed.")
