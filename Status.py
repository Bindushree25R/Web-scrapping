
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import mysql.connector
import time

# Database connection
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

# Login
driver.get("http://your-login-url")
username = input("Enter your username: ")
password = input("Enter your password: ")

WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "username"))).send_keys(username)
WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "password"))).send_keys(password)
WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.ID, "login_submit"))).click()
print("✅ Logged in successfully.")

# Navigate to Issues page
driver.get("http://your-projects-url")
WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.LINK_TEXT, "Issues"))).click()
time.sleep(2)

# List of status options
status_options = ['open', 'closed', 'any']

for status in status_options:
    print(f"\n🔁 Scraping for status: {status}")

    # Select status from dropdown
    select = Select(driver.find_element(By.ID, "operators_status_id"))
    select.select_by_value(status)

    # Click Apply button (adjust selector if needed)
    WebDriverWait(driver, 5).until(EC.element_to_be_clickable((By.NAME, "commit"))).click()
    time.sleep(2)

    # Scrape paginated issues for the current status
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
            next_button = driver.find_element(By.LINK_TEXT, "Next »")
            next_button.click()
            time.sleep(2)
        except:
            print(f"✅ Completed scraping for status: {status}")
            break

# Cleanup
cursor.close()
db.close()
driver.quit()
print("\n✅ All status options completed.")


---

✅ Final To-Do Before Running:

1. Replace these placeholders:

"http://your-login-url"

"http://your-projects-url"

"http://your-domain"

MySQL user, password, and database credentials.



2. Ensure:

The Apply button really uses name="commit" — if not, update it with the correct selector (By.ID, By.XPATH, etc.).

